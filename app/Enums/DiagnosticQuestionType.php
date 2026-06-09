<?php

namespace App\Enums;

enum DiagnosticQuestionType: string
{
    case Scale = 'scale';
    case SingleChoice = 'single_choice';
    case MultipleChoice = 'multiple_choice';
    case TrueFalse = 'true_false';
    case Text = 'text';
    case Ranking = 'ranking';

    public function label(): string
    {
        return match ($this) {
            self::Scale => 'Escala (Likert)',
            self::SingleChoice => 'Escolha única',
            self::MultipleChoice => 'Múltipla escolha',
            self::TrueFalse => 'Verdadeiro / Falso',
            self::Text => 'Texto aberto',
            self::Ranking => 'Ordenação',
        };
    }

    /**
     * Indica se o tipo entra no cálculo quantitativo de score.
     */
    public function isScorable(): bool
    {
        return $this !== self::Text;
    }
}
