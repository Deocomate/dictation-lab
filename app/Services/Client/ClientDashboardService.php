<?php

namespace App\Services\Client;

use App\Models\Article;
use App\Models\Category;
use App\Models\DictationHistory;
use App\Models\User;
use Illuminate\Support\Collection;

class ClientDashboardService
{
    public function getStats(User $user): array
    {
        $dictations = $user->dictationHistories();

        return [
            'total_articles_completed' => (clone $dictations)->distinct('article_id')->count('article_id'),
            'total_sentences' => (int) (clone $dictations)->sum('completed_sentences'),
            'total_dictations' => $dictations->count(),
            'avg_wpm' => round($dictations->avg('wpm') ?? 0),
            'avg_accuracy' => round($dictations->avg('accuracy') ?? 0, 1),
            'total_vocab' => $user->userVocabularies()->count(),
        ];
    }

    public function getWeeklyWpm(User $user): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $avg = DictationHistory::where('user_id', $user->id)
                ->whereDate('completed_at', $date->toDateString())
                ->avg('wpm');
            $data[] = [
                'date' => $date->format('D'),
                'label' => $date->format('d/m'),
                'wpm' => round($avg ?? 0),
            ];
        }

        return $data;
    }

    public function getRecentActivity(User $user, int $limit = 10): Collection
    {
        return $user->dictationHistories()
            ->with('article:id,title', 'article.categories:id,name')
            ->latest('completed_at')
            ->take($limit)
            ->get()
            ->map(fn (DictationHistory $d) => [
                'type' => 'dictation',
                'history_id' => $d->id,
                'article_id' => $d->article_id,
                'article' => $d->article,
                'wpm' => $d->wpm,
                'accuracy' => $d->accuracy,
                'completed_sentences' => $d->completed_sentences,
                'created_at' => $d->completed_at,
            ]);
    }

    public function getRecommendedArticles(User $user, int $limit = 3): Collection
    {
        $completedIds = $user->dictationHistories()->pluck('article_id')->unique();

        return Article::published()
            ->with('categories:id,name')
            ->whereNotIn('id', $completedIds)
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    public function getResumeList(User $user): Collection
    {
        $latestByArticle = $user->dictationHistories()
            ->with('article:id,title,content_json,is_premium', 'article.categories:id,name')
            ->latest('completed_at')
            ->get()
            ->unique('article_id');

        return $latestByArticle->filter(function (DictationHistory $history) {
            $article = $history->article;
            if (! $article) {
                return false;
            }

            $total = $article->sentenceCount();

            return $total > 0 && $history->completed_sentences < $total;
        })->values();
    }

    public function getCategoriesWithCounts(): Collection
    {
        return Category::where('status', 'active')
            ->withCount(['articles' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get();
    }
}
