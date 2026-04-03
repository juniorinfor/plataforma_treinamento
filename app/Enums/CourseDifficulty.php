<?php

namespace App\Enums;

enum CourseDifficulty: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';

    public function label(): string
    {
        return match($this) {
            self::Beginner => 'Iniciante',
            self::Intermediate => 'Intermediário',
            self::Advanced => 'Avançado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Beginner => 'emerald',
            self::Intermediate => 'amber',
            self::Advanced => 'red',
        };
    }
}
