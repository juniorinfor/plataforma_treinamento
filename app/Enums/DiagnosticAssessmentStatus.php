<?php

namespace App\Enums;

enum DiagnosticAssessmentStatus: string
{
    case Draft = 'draft';
    case InProgress = 'in_progress';
    case Submitted = 'submitted';
    case Analyzing = 'analyzing';
    case InReview = 'in_review';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::InProgress => 'Respondendo',
            self::Submitted => 'Enviado',
            self::Analyzing => 'Em análise',
            self::InReview => 'Em revisão',
            self::Completed => 'Concluído',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::InProgress => 'blue',
            self::Submitted => 'indigo',
            self::Analyzing => 'amber',
            self::InReview => 'purple',
            self::Completed => 'emerald',
        };
    }

    public function isFinished(): bool
    {
        return $this === self::Completed;
    }
}
