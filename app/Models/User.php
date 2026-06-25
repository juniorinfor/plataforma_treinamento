<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\DiagnosticAssessmentStatus;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'company_id', 'name', 'email', 'password', 'avatar_path',
        'role', 'is_active', 'settings', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function points(): HasOne
    {
        return $this->hasOne(UserPoints::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withPivot('earned_at');
    }

    public function streak(): HasOne
    {
        return $this->hasOne(Streak::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function diagnosticAssessments(): HasMany
    {
        return $this->hasMany(DiagnosticAssessment::class);
    }

    /**
     * HasOne do tipo "latestOfMany" — seguro para eager loading em coleções.
     */
    public function latestDiagnosticAssessment(): HasOne
    {
        return $this->hasOne(DiagnosticAssessment::class)
            ->where('status', DiagnosticAssessmentStatus::Completed)
            ->latestOfMany('completed_at');
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    // ── Role helpers ─────────────────────────────────────────────────

    /** Nível 3 — Admin do Sistema (desenvolvedor/plataforma). */
    public function isPlatformAdmin(): bool
    {
        return $this->role === UserRole::PlatformAdmin;
    }

    /**
     * Nível 2 — Gestor com billing.
     * Pode alterar plano, pagar assinatura, gerenciar empresa.
     */
    public function isCompanyAdmin(): bool
    {
        return $this->role === UserRole::CompanyAdmin;
    }

    /**
     * Nível 2 — Gestor (qualquer variante: company_admin ou manager).
     * Pode ver dashboard, colaboradores e estatísticas da empresa.
     */
    public function isGestor(): bool
    {
        return $this->role?->isGestor() ?? false;
    }

    /** Nível 1 — Colaborador. */
    public function isEmployee(): bool
    {
        return $this->role === UserRole::Employee;
    }

    /** Retorna o nível numérico (1 / 2 / 3). */
    public function roleLevel(): int
    {
        return $this->role?->level() ?? 1;
    }

    /** Pode gerenciar billing (só company_admin). */
    public function canManageBilling(): bool
    {
        return $this->role?->canManageBilling() ?? false;
    }

    /**
     * Rota inicial (home) de acordo com o papel — fonte única da verdade
     * para o redirect pós-login e para o link "início" no menu.
     */
    public function homeRoute(): string
    {
        return match (true) {
            $this->isPlatformAdmin() => 'platform.dashboard',
            $this->isGestor()        => 'admin.dashboard',
            default                  => 'dashboard',
        };
    }

    public function getTotalXpAttribute(): int
    {
        return $this->points?->total_xp ?? 0;
    }

    public function getCurrentStreakAttribute(): int
    {
        return $this->streak?->current_streak ?? 0;
    }
}
