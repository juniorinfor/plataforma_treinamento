<?php

namespace App\Enums;

enum UserRole: string
{
    case PlatformAdmin = 'platform_admin';
    case CompanyAdmin = 'company_admin';
    case Manager = 'manager';
    case Employee = 'employee';

    public function label(): string
    {
        return match($this) {
            self::PlatformAdmin => 'Administrador da Plataforma',
            self::CompanyAdmin => 'Administrador',
            self::Manager => 'Gestor',
            self::Employee => 'Colaborador',
        };
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::PlatformAdmin, self::CompanyAdmin]);
    }

    public function isCompanyLevel(): bool
    {
        return in_array($this, [self::CompanyAdmin, self::Manager, self::Employee]);
    }
}
