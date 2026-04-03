<?php

namespace App\Enums;

enum ChallengeType: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Special = 'special';

    public function label(): string
    {
        return match($this) {
            self::Daily => 'Diário',
            self::Weekly => 'Semanal',
            self::Monthly => 'Mensal',
            self::Special => 'Especial',
        };
    }
}
