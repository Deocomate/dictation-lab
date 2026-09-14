<?php

namespace App\Models;

use App\Services\ArticleContentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    protected $fillable = [
        'title',
        'excerpt',
        'image_path',
        'content_json',
        'is_premium',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'content_json' => 'array',
            'is_premium' => 'boolean',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function dictationHistories(): HasMany
    {
        return $this->hasMany(DictationHistory::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * @return list<array{type: string, level?: int, text?: string, en?: string, vi?: string}>
     */
    public function contentBlocks(): array
    {
        return app(ArticleContentService::class)->upgradeLegacy($this->content_json ?? []);
    }

    /**
     * @return list<array{en: string, vi: string}>
     */
    public function sentences(): array
    {
        return app(ArticleContentService::class)->extractSentences($this->content_json ?? []);
    }

    public function sentenceCount(): int
    {
        return count($this->sentences());
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
