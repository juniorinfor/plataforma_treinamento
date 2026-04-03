<?php

namespace App\Enums;

enum BadgeRarity: string
{
    case Common = 'common';
    case Uncommon = 'uncommon';
    case Rare = 'rare';
    case Epic = 'epic';
    case Legendary = 'legendary';

    public function label(): string
    {
        return match($this) {
            self::Common => 'Comum',
            self::Uncommon => 'Incomum',
            self::Rare => 'Raro',
            self::Epic => 'Épico',
            self::Legendary => 'Lendário',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Common => '#9CA3AF',
            self::Uncommon => '#10B981',
            self::Rare => '#3B82F6',
            self::Epic => '#8B5CF6',
            self::Legendary => '#F59E0B',
        };
    }
}
