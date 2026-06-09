<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'company_id', 'subscription_id', 'amount', 'currency',
        'billing_type', 'status', 'paid_at', 'due_at',
        'gateway_invoice_id', 'payment_url', 'pix_qr_code',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'due_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'overdue']);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'paid'     => 'Pago',
            'pending'  => 'Pendente',
            'overdue'  => 'Vencido',
            'refunded' => 'Estornado',
            'deleted'  => 'Cancelado',
            default    => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'paid'     => 'text-emerald-600 bg-emerald-50',
            'pending'  => 'text-amber-600 bg-amber-50',
            'overdue'  => 'text-red-600 bg-red-50',
            default    => 'text-gray-500 bg-gray-50',
        };
    }
}
