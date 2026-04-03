<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonContent extends Model
{
    protected $fillable = ['lesson_id', 'type', 'content', 'sort_order', 'settings'];

    protected function casts(): array
    {
        return ['settings' => 'array'];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
