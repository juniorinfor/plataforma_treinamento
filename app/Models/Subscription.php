<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'company_id', 'plan_id', 'status', 'starts_at', 'ends_at',
        'cancelled_at', 'payment_gateway', 'gateway_subscription_id',
        'cycle', 'next_due_date',
    ];

    protected function casts(): array
    {
        return [
            'status'        => \App\Enums\SubscriptionStatus::class,
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
            'cancelled_at'  => 'datetime',
            'next_due_date' => 'date',
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status?->isActive() ?? false;
    }

    public function cycleLabel(): string
    {
        return $this->cycle === 'YEARLY' ? 'Anual' : 'Mensal';
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
