<?php

namespace App\Enums;

enum LessonType: string
{
    case Text = 'text';
    case Video = 'video';
    case Pdf = 'pdf';
    case Quiz = 'quiz';
    case Interactive = 'interactive';

    public function label(): string
    {
        return match($this) {
            self::Text => 'Texto',
            self::Video => 'Vídeo',
            self::Pdf => 'PDF',
            self::Quiz => 'Quiz',
            self::Interactive => 'Interativo',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Text => 'document-text',
            self::Video => 'play-circle',
            self::Pdf => 'document',
            self::Quiz => 'question-mark-circle',
            self::Interactive => 'puzzle-piece',
        };
    }
}
