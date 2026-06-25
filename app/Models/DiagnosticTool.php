<?php

namespace App\Models;

use App\Enums\DiagnosticInputSource;
use App\Enums\DiagnosticResultMode;
use App\Enums\DiagnosticToolType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class DiagnosticTool extends Model
{
    protected $fillable = [
        'company_id', 'created_by', 'ai_provider_id', 'code', 'name', 'slug',
        'short_description', 'description', 'ai_prompt', 'type', 'input_source',
        'result_mode', 'is_confidential', 'min_responses',
        'requires_review', 'icon', 'color', 'estimated_minutes',
        'is_published', 'is_platform_tool', 'sort_order', 'xp_reward', 'settings',
    ];

    protected function casts(): array
    {
        return [
            'type' => DiagnosticToolType::class,
            'input_source' => DiagnosticInputSource::class,
            'result_mode' => DiagnosticResultMode::class,
            'is_confidential' => 'boolean',
            'min_responses' => 'integer',
            'requires_review' => 'boolean',
            'is_published' => 'boolean',
            'is_platform_tool' => 'boolean',
            'estimated_minutes' => 'integer',
            'sort_order' => 'integer',
            'xp_reward' => 'integer',
            'settings' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function aiProvider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class);
    }

    public function dimensions(): HasMany
    {
        return $this->hasMany(DiagnosticDimension::class)->orderBy('sort_order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DiagnosticQuestion::class)->orderBy('sort_order');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(DiagnosticAssessment::class);
    }

    /**
     * Ferramentas-filhas (para ferramentas compostas, ex.: IO).
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(
            DiagnosticTool::class,
            'diagnostic_tool_components',
            'parent_tool_id',
            'child_tool_id',
        )->withPivot(['weight', 'sort_order'])->orderBy('diagnostic_tool_components.sort_order');
    }

    public function components(): HasMany
    {
        return $this->hasMany(DiagnosticToolComponent::class, 'parent_tool_id')->orderBy('sort_order');
    }

    public function isComposite(): bool
    {
        return $this->type === DiagnosticToolType::Composite;
    }

    // ── Modo de resultado / confidencialidade ─────────────────────────

    public function isIndividual(): bool
    {
        return ($this->result_mode ?? DiagnosticResultMode::Individual) === DiagnosticResultMode::Individual;
    }

    public function isAggregated(): bool
    {
        return $this->result_mode === DiagnosticResultMode::Aggregated;
    }

    public function isCompanySingle(): bool
    {
        return $this->result_mode === DiagnosticResultMode::CompanySingle;
    }

    /** Tem consolidação por empresa (painel de gestor/admin)? */
    public function hasCompanyAggregate(): bool
    {
        return $this->result_mode?->hasCompanyAggregate() ?? false;
    }

    /** Resultados individuais ficam ocultos para o gestor (só agregado)? */
    public function hidesIndividualFromManager(): bool
    {
        return (bool) $this->is_confidential;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Ferramentas visíveis para uma empresa: as da própria empresa + as de plataforma.
     */
    public function scopeForCompany($query, ?int $companyId)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->where('company_id', $companyId)->orWhere('is_platform_tool', true);
        });
    }
}
