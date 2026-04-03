<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoints extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'total_xp', 'current_level_id',
        'weekly_xp', 'monthly_xp', 'weekly_reset_at', 'monthly_reset_at',
    ];

    protected function casts(): array
    {
        return [
            'weekly_reset_at' => 'datetime',
            'monthly_reset_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function currentLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'current_level_id');
    }
}
