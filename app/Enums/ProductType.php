<?php

namespace App\Enums;

enum ProductType: string
{
    case CourseAvulso = 'course_avulso';
    case Pacote       = 'pacote';

    public function label(): string
    {
        return match ($this) {
            self::CourseAvulso => 'Curso avulso',
            self::Pacote       => 'Pacote',
        };
    }
}
