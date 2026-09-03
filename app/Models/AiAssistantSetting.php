<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAssistantSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'system_instruction',
        'max_questions',
        'max_input_chars',
        'welcome_message',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'max_questions' => 'integer',
            'max_input_chars' => 'integer',
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
