<?php

namespace App\Enums;

enum QuestionType: string
{
    case MultipleChoice = 'multiple_choice';
    case TrueFalse = 'true_false';
    case FillBlank = 'fill_blank';

    public function label(): string
    {
        return match($this) {
            self::MultipleChoice => 'Múltipla Escolha',
            self::TrueFalse => 'Verdadeiro ou Falso',
            self::FillBlank => 'Preencher Lacuna',
        };
    }
}
