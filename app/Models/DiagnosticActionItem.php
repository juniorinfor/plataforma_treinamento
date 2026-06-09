<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticActionItem extends Model
{
    protected $fillable = [
        'diagnostic_action_plan_id',
        'diagnostic_result_id',
        'course_id',
        'title',
        'description',
        'type',
        'status',
        'is_auto_generated',
        'sort_order',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_auto_generated' => 'boolean',
            'completed_at'      => 'datetime',
        ];
    }

    // ── Relações ─────────────────────────────────────────────────────

    public function plan(): BelongsTo
    {
        return $this->belongsTo(DiagnosticActionPlan::class, 'diagnostic_action_plan_id');
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(DiagnosticResult::class, 'diagnostic_result_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function typeIcon(): string
    {
        return match ($this->type) {
            'course'     => 'academic-cap',
            'reading'    => 'book-open',
            'reflection' => 'light-bulb',
            default      => 'check-circle',
        };
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'course'     => 'Treinamento',
            'reading'    => 'Leitura',
            'reflection' => 'Reflexão',
            default      => 'Ação',
        };
    }
}
