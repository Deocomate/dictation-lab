<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_id',
        'wpm',
        'accuracy',
        'completed_sentences',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'wpm' => 'integer',
            'accuracy' => 'decimal:2',
            'completed_sentences' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
