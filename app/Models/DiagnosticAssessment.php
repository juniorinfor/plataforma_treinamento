<?php

namespace App\Models;

use App\Enums\DiagnosticAssessmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiagnosticAssessment extends Model
{
    protected $fillable = [
        'diagnostic_tool_id', 'user_id', 'company_id', 'department_id',
        'assigned_by', 'tier', 'status', 'global_score', 'global_label',
        'started_at', 'submitted_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => DiagnosticAssessmentStatus::class,
            'global_score' => 'decimal:2',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(DiagnosticTool::class, 'diagnostic_tool_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(DiagnosticAnswer::class);
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(DiagnosticUpload::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(DiagnosticResult::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(DiagnosticReport::class);
    }

    public function actionPlan(): HasOne
    {
        return $this->hasOne(DiagnosticActionPlan::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === DiagnosticAssessmentStatus::Completed;
    }

    /**
     * O assessment já tem resultado consultável?
     */
    public function isViewable(): bool
    {
        return in_array($this->status, [
            DiagnosticAssessmentStatus::Completed,
            DiagnosticAssessmentStatus::Submitted,
            DiagnosticAssessmentStatus::Analyzing,
            DiagnosticAssessmentStatus::InReview,
        ], true);
    }

    /**
     * Quem pode VISUALIZAR este assessment (resultado / plano):
     *  - o próprio dono;
     *  - o Admin do Sistema (platform_admin) — acesso a tudo;
     *  - um Gestor da mesma empresa (supervisão).
     *
     * Responder o questionário (Take) continua restrito ao dono.
     */
    public function canBeViewedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        if ($user->isPlatformAdmin()) {
            return true;
        }

        if ($user->isGestor() && $this->company_id && $this->company_id === $user->company_id) {
            return true;
        }

        return false;
    }
}
