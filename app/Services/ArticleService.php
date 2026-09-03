<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Throwable;

class ArticleService
{
    public function __construct(
        private readonly FileUploadService $fileUploadService,
        private readonly ArticleContentService $articleContentService,
    ) {}

    public function getArticles(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Article::query()->with('categories');

        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        if (! empty($filters['category_id'])) {
            $categoryId = $filters['category_id'];
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId));
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function createArticle(array $data, ?UploadedFile $image = null): Article
    {
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        if ($image) {
            $data['image_path'] = $this->fileUploadService->upload($image, 'articles');
        }

        $article = Article::create($data);
        $this->syncCategories($article, $categoryIds);

        return $article->fresh(['categories']);
    }

    public function updateArticle(Article $article, array $data, ?UploadedFile $image = null, bool $removeImage = false): Article
    {
        $categoryIds = $data['category_ids'] ?? null;
        unset($data['category_ids']);

        $oldImagePath = $article->image_path;
        $uploadedImagePath = null;

        if ($image) {
            $uploadedImagePath = $this->fileUploadService->upload($image, 'articles');
            $data['image_path'] = $uploadedImagePath;
        } elseif ($removeImage) {
            $data['image_path'] = null;
        }

        try {
            $article->update($data);

            if (is_array($categoryIds)) {
                $this->syncCategories($article, $categoryIds);
            }
        } catch (Throwable $throwable) {
            if ($uploadedImagePath) {
                $this->fileUploadService->delete($uploadedImagePath);
            }

            throw $throwable;
        }

        if (($removeImage || $image) && $oldImagePath && $oldImagePath !== ($data['image_path'] ?? null)) {
            $this->fileUploadService->delete($oldImagePath);
        }

        return $article->fresh(['categories']);
    }

    public function deleteArticle(Article $article): void
    {
        if ($article->image_path) {
            $this->fileUploadService->delete($article->image_path);
        }

        $article->delete();
    }

    public function syncCategories(Article $article, array $categoryIds): void
    {
        $article->categories()->sync($categoryIds);
    }

    /**
     * @param  array<int, array<string, mixed>>  $content
     * @return list<array{type: string, level?: int, text?: string, en?: string, vi?: string}>
     */
    public function normalizeContent(array $content): array
    {
        return $this->articleContentService->normalizeBlocks($content);
    }
}
