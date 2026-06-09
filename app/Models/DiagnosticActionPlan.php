<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiagnosticActionPlan extends Model
{
    protected $fillable = [
        'diagnostic_assessment_id',
        'user_id',
        'status',
        'items_total',
        'items_done',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    // ── Relações ─────────────────────────────────────────────────────

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(DiagnosticAssessment::class, 'diagnostic_assessment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DiagnosticActionItem::class)->orderBy('sort_order');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function progressPercent(): int
    {
        if ($this->items_total === 0) {
            return 0;
        }

        return (int) round(($this->items_done / $this->items_total) * 100);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Recalcula e persiste os contadores de progresso.
     */
    public function syncProgress(): void
    {
        $total = $this->items()->count();
        $done  = $this->items()->where('status', 'done')->count();

        $status = match (true) {
            $done === 0           => 'pending',
            $done < $total        => 'in_progress',
            default               => 'completed',
        };

        $this->update([
            'items_total'  => $total,
            'items_done'   => $done,
            'status'       => $status,
            'completed_at' => ($status === 'completed' && !$this->completed_at) ? now() : $this->completed_at,
        ]);
    }
}
