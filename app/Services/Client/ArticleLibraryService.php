<?php

namespace App\Services\Client;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleLibraryService
{
    public function getArticles(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Article::published()->with('categories');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category'])) {
            $slug = $filters['category'];
            $query->whereHas('categories', fn ($q) => $q->where('slug', $slug));
        }

        if (! empty($filters['access'])) {
            $query->where('is_premium', in_array($filters['access'], ['pro', 'premium'], true));
        }

        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'title_asc' => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function getTotalCount(): int
    {
        return Article::published()->count();
    }
}
