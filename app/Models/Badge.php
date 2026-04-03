<?php

namespace App\Models;

use App\Enums\BadgeRarity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = [
        'company_id', 'name', 'slug', 'description', 'icon_path', 'color',
        'category', 'criteria_type', 'criteria_config', 'xp_reward', 'rarity', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rarity' => BadgeRarity::class,
            'criteria_config' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')->withPivot('earned_at');
    }
}
