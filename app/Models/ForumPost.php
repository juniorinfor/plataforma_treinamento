<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumPost extends Model
{
    protected $fillable = ['forum_thread_id', 'user_id', 'content', 'is_solution', 'edited_at'];

    protected function casts(): array
    {
        return [
            'is_solution' => 'boolean',
            'edited_at'   => 'datetime',
        ];
    }

    public function thread(): BelongsTo { return $this->belongsTo(ForumThread::class, 'forum_thread_id'); }
    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
}
