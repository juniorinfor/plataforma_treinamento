<?php

namespace App\Enums;

enum DiagnosticInputSource: string
{
    case Questionnaire = 'questionnaire';
    case Upload = 'upload';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::Questionnaire => 'Questionário',
            self::Upload => 'Upload de laudo',
            self::Both => 'Questionário ou upload',
        };
    }

    public function allowsQuestionnaire(): bool
    {
        return $this === self::Questionnaire || $this === self::Both;
    }

    public function allowsUpload(): bool
    {
        return $this === self::Upload || $this === self::Both;
    }
}
