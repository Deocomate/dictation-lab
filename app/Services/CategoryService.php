<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    public function getCategories(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Category::query()->withCount('articles');

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('slug', 'like', '%'.$filters['search'].'%')
                    ->orWhere('description', 'like', '%'.$filters['search'].'%');
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getActiveCategories(): Collection
    {
        return Category::query()->active()->orderBy('name')->get();
    }

    public function createCategory(array $data): Category
    {
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        return Category::create($data);
    }

    public function quickCreateCategory(string $name, ?string $slug = null): Category
    {
        $resolvedSlug = $slug ? Str::slug($slug) : $this->generateUniqueSlug($name);

        return Category::create([
            'name' => trim($name),
            'slug' => $resolvedSlug,
            'status' => 'active',
            'description' => null,
        ]);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        return $category->fresh();
    }

    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }

    public function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = 'danh-muc';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Category::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
