<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumThread extends Model
{
    protected $fillable = [
        'company_id', 'forum_category_id', 'user_id', 'title', 'content',
        'is_pinned', 'is_locked', 'views_count', 'posts_count',
        'last_post_at', 'last_post_user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned'    => 'boolean',
            'is_locked'    => 'boolean',
            'last_post_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo  { return $this->belongsTo(Company::class); }
    public function category(): BelongsTo { return $this->belongsTo(ForumCategory::class, 'forum_category_id'); }
    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function posts(): HasMany      { return $this->hasMany(ForumPost::class); }
    public function lastPostUser(): BelongsTo { return $this->belongsTo(User::class, 'last_post_user_id'); }
}
