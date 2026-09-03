<?php

namespace App\Services;

use App\Models\AiAssistantSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class AiAssistantService
{
    public function getSetting(): AiAssistantSetting
    {
        $setting = AiAssistantSetting::query()->first();

        if (! $setting) {
            $setting = AiAssistantSetting::query()->create([
                'is_enabled' => true,
                'system_instruction' => $this->defaultInstruction(),
                'max_questions' => 5,
                'max_input_chars' => 500,
                'welcome_message' => 'Xin chào! Mình có thể hỗ trợ nhanh về chép chính tả song ngữ, lộ trình học và cách dùng Dictation Lab.',
            ]);
        }

        return $setting;
    }

    public function updateSetting(array $data, int $updatedBy): AiAssistantSetting
    {
        $setting = $this->getSetting();

        $setting->fill([
            'is_enabled' => (bool) $data['is_enabled'],
            'system_instruction' => trim((string) $data['system_instruction']),
            'max_questions' => (int) $data['max_questions'],
            'max_input_chars' => (int) $data['max_input_chars'],
            'welcome_message' => trim((string) ($data['welcome_message'] ?? '')),
            'updated_by' => $updatedBy,
        ]);
        $setting->save();

        return $setting;
    }

    public function getPublicConfig(): array
    {
        $setting = $this->getSetting();

        return [
            'enabled' => $setting->is_enabled,
            'max_questions' => min(5, max(1, (int) $setting->max_questions)),
            'max_input_chars' => max(100, (int) $setting->max_input_chars),
            'welcome_message' => $setting->welcome_message ?: 'Xin chào, mình có thể giúp bạn điều gì?',
        ];
    }

    public function ask(string $conversationId, string $question): array
    {
        $setting = $this->getSetting();

        if (! $setting->is_enabled) {
            throw new RuntimeException('AI Assistant hiện đang tạm tắt.');
        }

        $maxQuestions = min(5, max(1, (int) $setting->max_questions));
        $sessionKey = $this->conversationSessionKey($conversationId);
        $conversation = session()->get($sessionKey, [
            'question_count' => 0,
            'messages' => [],
        ]);

        $questionCount = (int) ($conversation['question_count'] ?? 0);
        if ($questionCount >= $maxQuestions) {
            return [
                'limit_reached' => true,
                'assistant_message' => 'Bạn đã đạt giới hạn hỏi đáp cho một cuộc hội thoại. Hãy bấm "Cuộc mới" để tiếp tục.',
                'question_count' => $questionCount,
                'max_questions' => $maxQuestions,
                'remaining_questions' => 0,
            ];
        }

        $normalizedQuestion = trim($question);
        $historyMessages = is_array($conversation['messages'] ?? null)
            ? $conversation['messages']
            : [];

        $messages = $this->buildOpenRouterMessages(
            $setting->system_instruction,
            $historyMessages,
            $normalizedQuestion,
        );

        $assistantMessage = $this->requestAssistantMessage($messages);

        $questionCount++;
        $conversation['question_count'] = $questionCount;
        $conversation['messages'][] = ['role' => 'user', 'content' => $normalizedQuestion];
        $conversation['messages'][] = ['role' => 'assistant', 'content' => $assistantMessage];
        $conversation['messages'] = array_slice($conversation['messages'], -10);

        session()->put($sessionKey, $conversation);

        return [
            'limit_reached' => false,
            'assistant_message' => $assistantMessage,
            'question_count' => $questionCount,
            'max_questions' => $maxQuestions,
            'remaining_questions' => max(0, $maxQuestions - $questionCount),
        ];
    }

    public function resetConversation(string $conversationId): void
    {
        session()->forget($this->conversationSessionKey($conversationId));
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $historyMessages
     * @return array<int, array{role: string, content: string}>
     */
    private function buildOpenRouterMessages(
        string $systemInstruction,
        array $historyMessages,
        string $question,
    ): array {
        $messages = [
            [
                'role' => 'system',
                'content' => trim($systemInstruction) !== ''
                    ? $systemInstruction
                    : $this->defaultInstruction(),
            ],
        ];

        $recentHistory = array_slice($historyMessages, -6);
        foreach ($recentHistory as $historyMessage) {
            $role = $historyMessage['role'] ?? '';
            if (! in_array($role, ['user', 'assistant'], true)) {
                continue;
            }

            $messages[] = [
                'role' => $role,
                'content' => Str::limit((string) ($historyMessage['content'] ?? ''), 1200, '...'),
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => Str::limit($question, 1500, '...'),
        ];

        return $messages;
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    private function requestAssistantMessage(array $messages): string
    {
        $apiKey = (string) config('services.openrouter.key');
        if ($apiKey === '') {
            throw new RuntimeException('Chưa cấu hình OPENROUTER_API_KEY.');
        }

        $url = $this->resolveChatCompletionsUrl((string) config('services.openrouter.url'));
        $model = (string) config('services.openrouter.chat_model', config('services.openrouter.model'));
        $siteUrl = (string) config('services.openrouter.site_url');
        $siteName = (string) config('services.openrouter.site_name');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'HTTP-Referer' => $siteUrl,
                'X-OpenRouter-Title' => $siteName,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
                ->connectTimeout(10)
                ->timeout(40)
                ->post($url, [
                    'model' => $model,
                    'temperature' => 0.4,
                    'max_tokens' => 280,
                    'messages' => $messages,
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('AI chat connection timeout.', ['message' => $exception->getMessage()]);
            throw new RuntimeException('AI đang quá tải. Vui lòng thử lại sau ít phút.');
        }

        if (! $response->successful()) {
            Log::warning('AI chat request failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('AI tạm thời không phản hồi. Vui lòng thử lại sau.');
        }

        $assistantMessage = trim((string) $response->json('choices.0.message.content', ''));
        if ($assistantMessage === '') {
            throw new RuntimeException('AI không trả về nội dung hợp lệ.');
        }

        return Str::limit($assistantMessage, 2600, '...');
    }

    private function resolveChatCompletionsUrl(string $configuredUrl): string
    {
        $normalizedUrl = rtrim(trim($configuredUrl), '/');
        if ($normalizedUrl === '') {
            return 'https://openrouter.ai/api/v1/chat/completions';
        }

        if (str_ends_with($normalizedUrl, '/chat/completions')) {
            return $normalizedUrl;
        }

        if (str_contains($normalizedUrl, '/api/v1')) {
            return $normalizedUrl.'/chat/completions';
        }

        return $normalizedUrl.'/api/v1/chat/completions';
    }

    private function conversationSessionKey(string $conversationId): string
    {
        $safeConversationId = preg_replace('/[^a-zA-Z0-9_-]/', '', $conversationId) ?: Str::uuid()->toString();

        return 'ai_assistant.conversations.'.$safeConversationId;
    }

    public function explainSentence(string $english, ?string $vietnamese = null): string
    {
        $context = $vietnamese ? "Nghĩa tiếng Việt: {$vietnamese}\n" : '';

        return $this->requestAssistantMessage([
            [
                'role' => 'system',
                'content' => 'Bạn là giáo viên tiếng Anh. Phân tích ngắn gọn ngữ pháp, cấu trúc và từ vựng của câu sau. Giải thích bằng tiếng Việt, tối đa 150 từ.',
            ],
            [
                'role' => 'user',
                'content' => $context."Câu tiếng Anh: {$english}",
            ],
        ]);
    }

    public function translateWord(string $word, ?string $context = null): string
    {
        $prompt = $context
            ? "Dịch từ/cụm từ \"{$word}\" trong ngữ cảnh: \"{$context}\". Chỉ trả về nghĩa tiếng Việt ngắn gọn."
            : "Dịch từ/cụm từ \"{$word}\" sang tiếng Việt. Chỉ trả về nghĩa ngắn gọn.";

        return $this->requestAssistantMessage([
            [
                'role' => 'system',
                'content' => 'Bạn là từ điển Anh-Việt. Chỉ trả về nghĩa dịch, không giải thích dài.',
            ],
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ]);
    }

    private function defaultInstruction(): string
    {
        return 'Bạn là trợ lý AI của Dictation Lab. Hãy trả lời ngắn gọn, dễ hiểu, đúng ngữ cảnh học tiếng Anh qua chép chính tả song ngữ và hướng dẫn người dùng cách học hiệu quả trong hệ thống.';
    }
}
