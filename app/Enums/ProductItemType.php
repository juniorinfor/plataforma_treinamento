<?php

namespace App\Enums;

enum ProductItemType: string
{
    case Course         = 'course';
    case Diagnostic     = 'diagnostic';
    case AllCourses     = 'all_courses';
    case AllDiagnostics = 'all_diagnostics';

    public function label(): string
    {
        return match ($this) {
            self::Course         => 'Curso específico',
            self::Diagnostic     => 'Diagnóstico específico',
            self::AllCourses     => 'Todos os cursos',
            self::AllDiagnostics => 'Todos os diagnósticos',
        };
    }

    public function needsReference(): bool
    {
        return in_array($this, [self::Course, self::Diagnostic], true);
    }
}
