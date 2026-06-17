<?php

namespace App\Livewire\Diagnostics;

use App\Enums\DiagnosticAssessmentStatus;
use App\Models\Company;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticResult;
use App\Models\DiagnosticTool;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Painel de resultados de um diagnóstico, por papel:
 *  - Gestor  → agregado da PRÓPRIA empresa (anônimo se confidencial, com nº
 *    mínimo de respostas) + acompanhamento de participação + responder o seu.
 *  - Admin   → todas as empresas, com drill-down identificado por empresa.
 */
#[Layout('components.layouts.app')]
#[Title('Resultados do Diagnóstico')]
class DiagnosticPanel extends Component
{
    #[Locked]
    public int $toolId;

    /** Admin: empresa selecionada para drill-down (null = visão geral). */
    public ?int $selectedCompanyId = null;

    public function mount(DiagnosticTool $tool): void
    {
        $user = auth()->user();
        abort_unless($user->isGestor() || $user->isPlatformAdmin(), 403);

        $this->toolId = $tool->id;
    }

    public function isAdmin(): bool
    {
        return auth()->user()->isPlatformAdmin();
    }

    #[Computed]
    public function tool(): DiagnosticTool
    {
        return DiagnosticTool::with('dimensions')->findOrFail($this->toolId);
    }

    /** Empresa em foco: admin usa a selecionada; gestor usa a sua. */
    private function scopeCompanyId(): ?int
    {
        return $this->isAdmin() ? $this->selectedCompanyId : auth()->user()->company_id;
    }

    /** Assessments concluídos no escopo atual. */
    private function baseQuery()
    {
        $q = DiagnosticAssessment::where('diagnostic_tool_id', $this->toolId)
            ->where('status', DiagnosticAssessmentStatus::Completed);

        if ($cid = $this->scopeCompanyId()) {
            $q->where('company_id', $cid);
        }

        return $q;
    }

    #[Computed]
    public function completedCount(): int
    {
        return $this->baseQuery()->count();
    }

    /** Agregado bloqueado por confidencialidade (gestor abaixo do mínimo). */
    #[Computed]
    public function locked(): bool
    {
        if ($this->isAdmin()) {
            return false;
        }

        return $this->tool->is_confidential
            && $this->completedCount < ($this->tool->min_responses ?? 5);
    }

    #[Computed]
    public function globalAverage(): float
    {
        return round((float) $this->baseQuery()->avg('global_score'), 1);
    }

    /** Média por dimensão/índice no escopo (inclui o IOH). */
    #[Computed]
    public function dimensionAverages(): array
    {
        $ids = (clone $this->baseQuery())->pluck('id');
        if ($ids->isEmpty()) {
            return [];
        }

        $rows = DiagnosticResult::whereIn('diagnostic_assessment_id', $ids)
            ->whereNotNull('diagnostic_dimension_id')
            ->selectRaw('diagnostic_dimension_id, AVG(normalized_score) as avg_score')
            ->groupBy('diagnostic_dimension_id')
            ->get()
            ->keyBy('diagnostic_dimension_id');

        return $this->tool->dimensions
            ->map(fn ($d) => [
                'name'  => $d->name,
                'code'  => $d->code,
                'color' => $d->color ?? '#6366F1',
                'score' => isset($rows[$d->id]) ? round((float) $rows[$d->id]->avg_score, 1) : null,
            ])
            ->filter(fn ($r) => $r['score'] !== null)
            ->values()
            ->all();
    }

    /** Acompanhamento de participação da empresa (nomes + status, sem notas). */
    #[Computed]
    public function participation(): array
    {
        $cid = $this->isAdmin() ? $this->selectedCompanyId : auth()->user()->company_id;
        if (!$cid) {
            return [];
        }

        $users = User::where('company_id', $cid)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $completed = DiagnosticAssessment::where('diagnostic_tool_id', $this->toolId)
            ->where('company_id', $cid)
            ->where('status', DiagnosticAssessmentStatus::Completed)
            ->pluck('user_id')->flip();

        $started = DiagnosticAssessment::where('diagnostic_tool_id', $this->toolId)
            ->where('company_id', $cid)
            ->whereIn('status', [
                DiagnosticAssessmentStatus::Draft,
                DiagnosticAssessmentStatus::InProgress,
            ])
            ->pluck('user_id')->flip();

        return $users->map(fn ($u) => [
            'name'   => $u->name,
            'status' => isset($completed[$u->id]) ? 'concluido'
                : (isset($started[$u->id]) ? 'em_andamento' : 'pendente'),
        ])->all();
    }

    /**
     * Resultados individuais identificados.
     * Visível para admin (sempre) e para gestor apenas em ferramentas NÃO confidenciais.
     */
    #[Computed]
    public function individuals(): Collection
    {
        if (!$this->isAdmin() && $this->tool->is_confidential) {
            return collect();
        }

        if (!$this->scopeCompanyId()) {
            return collect();
        }

        return $this->baseQuery()
            ->with('user:id,name')
            ->orderByDesc('global_score')
            ->get()
            ->map(fn ($a) => [
                'id'    => $a->id,
                'name'  => $a->user?->name ?? '—',
                'score' => $a->global_score,
                'label' => $a->global_label,
            ]);
    }

    /** Admin: empresas que possuem respostas concluídas para este diagnóstico. */
    #[Computed]
    public function companies(): array
    {
        if (!$this->isAdmin()) {
            return [];
        }

        $rows = DiagnosticAssessment::where('diagnostic_tool_id', $this->toolId)
            ->where('status', DiagnosticAssessmentStatus::Completed)
            ->whereNotNull('company_id')
            ->selectRaw('company_id, COUNT(*) as n, AVG(global_score) as avg_score')
            ->groupBy('company_id')
            ->get();

        $names = Company::whereIn('id', $rows->pluck('company_id'))->pluck('name', 'id');

        return $rows->map(fn ($r) => [
            'id'  => $r->company_id,
            'name' => $names[$r->company_id] ?? 'Empresa #' . $r->company_id,
            'n'   => (int) $r->n,
            'avg' => round((float) $r->avg_score, 1),
        ])->sortByDesc('avg')->values()->all();
    }

    public function selectCompany(int $companyId): void
    {
        $this->selectedCompanyId = $companyId;
    }

    public function clearCompany(): void
    {
        $this->selectedCompanyId = null;
    }

    /** Gestor: iniciar/retomar/abrir o SEU próprio diagnóstico. */
    public function respondMine(): void
    {
        if ($this->isAdmin()) {
            return; // admin não responde
        }

        $latest = DiagnosticAssessment::where('diagnostic_tool_id', $this->toolId)
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        if ($latest && $latest->isViewable()) {
            $this->redirect(route('diagnostics.result', $latest->id), navigate: true);
            return;
        }

        if ($latest && in_array($latest->status, [
            DiagnosticAssessmentStatus::Draft,
            DiagnosticAssessmentStatus::InProgress,
        ], true)) {
            $this->redirect(route('diagnostics.take', $latest->id), navigate: true);
            return;
        }

        $assessment = DiagnosticAssessment::create([
            'diagnostic_tool_id' => $this->toolId,
            'user_id'            => auth()->id(),
            'company_id'         => auth()->user()->company_id,
            'assigned_by'        => auth()->id(),
            'tier'               => 'start',
            'status'             => DiagnosticAssessmentStatus::Draft,
        ]);

        $this->redirect(route('diagnostics.take', $assessment->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.diagnostics.panel');
    }
}
