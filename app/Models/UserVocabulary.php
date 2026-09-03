<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVocabulary extends Model
{
    protected $table = 'user_vocabularies';

    protected $fillable = [
        'user_id',
        'article_id',
        'word',
        'meaning',
        'sentence_en',
        'sentence_vi',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
