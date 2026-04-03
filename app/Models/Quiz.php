<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'lesson_id', 'title', 'description', 'passing_score',
        'time_limit_minutes', 'max_attempts', 'shuffle_questions',
        'hearts_enabled', 'hearts_count', 'xp_reward',
    ];

    protected function casts(): array
    {
        return [
            'shuffle_questions' => 'boolean',
            'hearts_enabled' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
