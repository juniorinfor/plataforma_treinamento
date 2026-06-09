<?php

namespace App\Enums;

enum UserRole: string
{
    case PlatformAdmin = 'platform_admin'; // Nível 3 — Admin do Sistema
    case CompanyAdmin  = 'company_admin';  // Nível 2 — Gestor (com billing)
    case Manager       = 'manager';        // Nível 2 — Gestor (sem billing)
    case Employee      = 'employee';       // Nível 1 — Colaborador

    // ── Labels ────────────────────────────────────────────────────────

    public function label(): string
    {
        return match ($this) {
            self::PlatformAdmin => 'Administrador do Sistema',
            self::CompanyAdmin  => 'Gestor',
            self::Manager       => 'Gestor',
            self::Employee      => 'Colaborador',
        };
    }

    /** Rótulo técnico/interno (para listagens administrativas). */
    public function techLabel(): string
    {
        return match ($this) {
            self::PlatformAdmin => 'Admin do Sistema',
            self::CompanyAdmin  => 'Gestor (billing)',
            self::Manager       => 'Gestor',
            self::Employee      => 'Colaborador',
        };
    }

    // ── Nível numérico ────────────────────────────────────────────────

    /**
     * Retorna o nível conceitual:
     *  1 = Colaborador
     *  2 = Gestor (company_admin ou manager)
     *  3 = Admin do Sistema
     */
    public function level(): int
    {
        return match ($this) {
            self::PlatformAdmin => 3,
            self::CompanyAdmin  => 2,
            self::Manager       => 2,
            self::Employee      => 1,
        };
    }

    // ── Helpers de permissão ──────────────────────────────────────────

    /** Admin do Sistema — acesso total à plataforma. */
    public function isPlatformAdmin(): bool
    {
        return $this === self::PlatformAdmin;
    }

    /**
     * Gestor (qualquer variante) — acesso ao dashboard de empresa,
     * lista de colaboradores e estatísticas.
     */
    public function isGestor(): bool
    {
        return in_array($this, [self::CompanyAdmin, self::Manager], true);
    }

    /**
     * Gestor com billing — pode alterar plano e pagar assinatura.
     * Apenas company_admin.
     */
    public function canManageBilling(): bool
    {
        return $this === self::CompanyAdmin;
    }

    /**
     * Alias legado — verdadeiro se tem acesso ao painel administrativo
     * da empresa (company_admin OU manager).
     */
    public function isAdmin(): bool
    {
        return $this->isGestor() || $this->isPlatformAdmin();
    }

    /** Pertence ao contexto de uma empresa (não é platform_admin). */
    public function isCompanyLevel(): bool
    {
        return $this !== self::PlatformAdmin;
    }

    // ── Factory ───────────────────────────────────────────────────────

    /**
     * Retorna os roles disponíveis para criação de usuários
     * dentro de uma empresa (excluindo platform_admin).
     */
    public static function companyRoles(): array
    {
        return [self::CompanyAdmin, self::Manager, self::Employee];
    }
}
