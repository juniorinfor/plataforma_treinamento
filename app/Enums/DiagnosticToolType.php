<?php

namespace App\Enums;

enum DiagnosticToolType: string
{
    case Simple = 'simple';
    case Composite = 'composite';

    public function label(): string
    {
        return match ($this) {
            self::Simple => 'Simples',
            self::Composite => 'Composta',
        };
    }
}
