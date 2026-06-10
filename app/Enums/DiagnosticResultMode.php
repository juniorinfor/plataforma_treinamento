<?php

namespace App\Enums;

/**
 * Define como os resultados de um diagnóstico são consumidos.
 */
enum DiagnosticResultMode: string
{
    /** Cada pessoa responde e o resultado é individual (ex.: Executive Mapping). */
    case Individual = 'individual';

    /** Vários respondem; gestor/admin veem o consolidado da empresa (ex.: clima, NR1). */
    case Aggregated = 'aggregated';

    /** Uma única resposta por empresa (ex.: checklist de conformidade preenchido pelo responsável). */
    case CompanySingle = 'company_single';

    public function label(): string
    {
        return match ($this) {
            self::Individual    => 'Individual',
            self::Aggregated    => 'Agregado por empresa',
            self::CompanySingle => 'Resposta única por empresa',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Individual    => 'Cada pessoa responde e vê o seu próprio resultado.',
            self::Aggregated    => 'Vários respondem; o gestor vê o consolidado da empresa e cada um vê o seu.',
            self::CompanySingle => 'Uma resposta oficial por empresa, preenchida pelo responsável.',
        };
    }

    /** Há consolidação por empresa para gestor/admin? */
    public function hasCompanyAggregate(): bool
    {
        return $this === self::Aggregated || $this === self::CompanySingle;
    }
}
