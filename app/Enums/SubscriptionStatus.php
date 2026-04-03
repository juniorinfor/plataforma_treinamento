<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Trial = 'trial';
    case Active = 'active';
    case PastDue = 'past_due';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match($this) {
            self::Trial => 'Período de Teste',
            self::Active => 'Ativo',
            self::PastDue => 'Pagamento Pendente',
            self::Cancelled => 'Cancelado',
            self::Expired => 'Expirado',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Trial, self::Active]);
    }
}
