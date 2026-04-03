<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreakHistory extends Model
{
    public $timestamps = false;

    protected $table = 'streak_history';

    protected $fillable = ['streak_id', 'activity_date', 'xp_earned'];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function streak(): BelongsTo
    {
        return $this->belongsTo(Streak::class);
    }
}
