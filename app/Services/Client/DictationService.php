<?php

namespace App\Services\Client;

use App\Models\Article;
use App\Models\DictationHistory;
use App\Models\User;

class DictationService
{
    public function getArticleForDictation(int $articleId): Article
    {
        return Article::where('status', 'published')
            ->with('categories')
            ->findOrFail($articleId);
    }

    public function saveResult(User $user, array $data): DictationHistory
    {
        return DictationHistory::create([
            'user_id' => $user->id,
            'article_id' => $data['article_id'],
            'wpm' => $data['wpm'],
            'accuracy' => $data['accuracy'],
            'completed_sentences' => $data['completed_sentences'] ?? 0,
            'completed_at' => now(),
        ]);
    }
}
