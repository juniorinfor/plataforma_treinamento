<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonInteraction extends Model
{
    protected $fillable = ['user_id', 'lesson_content_id', 'response'];

    protected function casts(): array
    {
        return ['response' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(LessonContent::class, 'lesson_content_id');
    }
}
