<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Streak extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'current_streak', 'longest_streak',
        'last_activity_date', 'streak_freezes_remaining',
    ];

    protected function casts(): array
    {
        return ['last_activity_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(StreakHistory::class);
    }
}
