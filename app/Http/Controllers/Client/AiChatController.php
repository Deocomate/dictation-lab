<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class AiChatController extends Controller
{
    public function __construct(private readonly AiAssistantService $aiAssistantService) {}

    public function config(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->aiAssistantService->getPublicConfig(),
        ]);
    }

    public function message(Request $request): JsonResponse
    {
        $setting = $this->aiAssistantService->getSetting();
        $maxInputChars = max(100, (int) $setting->max_input_chars);

        $data = $request->validate([
            'conversation_id' => ['required', 'string', 'max:100'],
            'message' => ['bail', 'required', 'string', 'max:'.$maxInputChars],
        ]);

        try {
            $result = $this->aiAssistantService->ask(
                (string) $data['conversation_id'],
                (string) $data['message'],
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 503);
        }

        if ($result['limit_reached']) {
            return response()->json([
                'success' => false,
                'limit_reached' => true,
                'message' => $result['assistant_message'],
                'question_count' => $result['question_count'],
                'max_questions' => $result['max_questions'],
                'remaining_questions' => $result['remaining_questions'],
            ], 429);
        }

        return response()->json([
            'success' => true,
            'assistant_message' => $result['assistant_message'],
            'question_count' => $result['question_count'],
            'max_questions' => $result['max_questions'],
            'remaining_questions' => $result['remaining_questions'],
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'string', 'max:100'],
        ]);

        $this->aiAssistantService->resetConversation((string) $data['conversation_id']);

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo cuộc hội thoại mới.',
        ]);
    }
}
