<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'logo_path', 'primary_color', 'secondary_color',
        'email', 'phone', 'document', 'asaas_customer_id',
        'invite_token', 'allow_self_registration',
        'plan_id', 'subscription_status',
        'trial_ends_at', 'max_users', 'settings', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'subscription_status' => SubscriptionStatus::class,
            'trial_ends_at' => 'datetime',
            'is_active' => 'boolean',
            'allow_self_registration' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function isSubscriptionActive(): bool
    {
        return $this->subscription_status->isActive();
    }

    // ── Auto-cadastro de colaboradores ────────────────────────────────

    /** Garante que exista um token de convite e o retorna. */
    public function ensureInviteToken(): string
    {
        if (!$this->invite_token) {
            $this->forceFill(['invite_token' => Str::random(48)])->save();
        }
        return $this->invite_token;
    }

    /** URL pública de auto-cadastro (null se ainda não houver token). */
    public function selfRegisterUrl(): ?string
    {
        return $this->invite_token ? route('company.join', $this->invite_token) : null;
    }

    /** Colaboradores ativos atuais. */
    public function activeUsersCount(): int
    {
        return $this->users()->where('is_active', true)->count();
    }

    /** Ainda há vaga dentro do limite do plano? */
    public function hasCapacity(): bool
    {
        return !$this->max_users || $this->activeUsersCount() < $this->max_users;
    }
}
