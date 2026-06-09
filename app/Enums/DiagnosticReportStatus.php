<?php

namespace App\Enums;

enum DiagnosticReportStatus: string
{
    case Pending = 'pending';
    case AiGenerated = 'ai_generated';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::AiGenerated => 'Gerado pela IA',
            self::InReview => 'Em revisão',
            self::Approved => 'Aprovado',
            self::Published => 'Publicado',
        };
    }

    public function isVisibleToClient(): bool
    {
        return $this === self::Published;
    }
}
