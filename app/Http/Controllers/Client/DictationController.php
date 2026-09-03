<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AiAssistantService;
use App\Services\Client\DictationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class DictationController extends Controller
{
    public function __construct(
        private readonly DictationService $dictationService,
        private readonly AiAssistantService $aiAssistantService,
    ) {}

    public function show(int $article): View
    {
        $articleData = $this->dictationService->getArticleForDictation($article);

        if ($articleData->is_premium && ! auth()->user()->isPro()) {
            abort(403, 'Bài viết này yêu cầu tài khoản Pro.');
        }

        return view('client.learning.study-dictation', [
            'article' => $articleData,
        ]);
    }

    public function saveResult(Request $request): JsonResponse
    {
        $data = $request->validate([
            'article_id' => ['required', 'exists:articles,id'],
            'wpm' => ['required', 'numeric', 'min:0'],
            'accuracy' => ['required', 'numeric', 'min:0', 'max:100'],
            'completed_sentences' => ['required', 'integer', 'min:0'],
        ]);

        $result = $this->dictationService->saveResult(auth()->user(), $data);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function explain(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->isPro(), 403, 'Tính năng này dành cho tài khoản Pro.');

        $data = $request->validate([
            'sentence_en' => ['required', 'string', 'max:2000'],
            'sentence_vi' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $explanation = $this->aiAssistantService->explainSentence(
                $data['sentence_en'],
                $data['sentence_vi'] ?? null,
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'explanation' => $explanation,
        ]);
    }
}
