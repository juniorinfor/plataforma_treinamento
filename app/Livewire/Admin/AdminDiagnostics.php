<?php

namespace App\Livewire\Admin;

use App\Enums\DiagnosticAssessmentStatus;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticTool;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Relatório de Diagnósticos')]
class AdminDiagnostics extends Component
{
    use WithPagination;

    public string $filterTool   = '';
    public string $filterLabel  = '';
    public string $search       = '';

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingFilterTool(): void  { $this->resetPage(); }
    public function updatingFilterLabel(): void { $this->resetPage(); }

    private function companyId(): int
    {
        return auth()->user()->company_id;
    }

    // ── Ferramentas disponíveis para filtro ───────────────────────────

    #[Computed]
    public function tools(): \Illuminate\Support\Collection
    {
        return DiagnosticTool::whereHas('assessments', fn ($q) =>
            $q->where('company_id', $this->companyId())
              ->where('status', DiagnosticAssessmentStatus::Completed)
        )
        ->orderBy('name')
        ->get(['id', 'name', 'code', 'color']);
    }

    // ── Stats de resumo ───────────────────────────────────────────────

    #[Computed]
    public function summary(): array
    {
        $base = DiagnosticAssessment::where('company_id', $this->companyId())
            ->where('status', DiagnosticAssessmentStatus::Completed);

        $total  = (clone $base)->count();
        $avgRaw = (clone $base)->avg('global_score');
        $avg    = $avgRaw ? round($avgRaw, 1) : 0;

        // Distribuição de labels
        $dist = (clone $base)
            ->selectRaw('global_label, count(*) as cnt')
            ->groupBy('global_label')
            ->pluck('cnt', 'global_label')
            ->toArray();

        return compact('total', 'avg', 'dist');
    }

    // ── Lista paginada ────────────────────────────────────────────────

    #[Computed]
    public function assessments()
    {
        return DiagnosticAssessment::where('company_id', $this->companyId())
            ->where('status', DiagnosticAssessmentStatus::Completed)
            ->when($this->filterTool, fn ($q) =>
                $q->where('diagnostic_tool_id', $this->filterTool)
            )
            ->when($this->filterLabel, fn ($q) =>
                $q->where('global_label', $this->filterLabel)
            )
            ->when($this->search, fn ($q) =>
                $q->whereHas('user', fn ($q2) =>
                    $q2->where('name', 'like', "%{$this->search}%")
                )
            )
            ->with([
                'user:id,name',
                'tool:id,name,code,color',
                'results:id,diagnostic_assessment_id,component_tool_id,diagnostic_dimension_id,normalized_score,label',
                'results.componentTool:id,name,code,color',
                'results.dimension:id,name,code,color',
                'actionPlan:id,diagnostic_assessment_id,status,items_total,items_done',
            ])
            ->orderByDesc('completed_at')
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.admin.admin-diagnostics');
    }
}
