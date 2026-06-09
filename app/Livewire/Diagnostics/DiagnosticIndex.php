<?php

namespace App\Livewire\Diagnostics;

use App\Enums\DiagnosticAssessmentStatus;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticTool;
use App\Models\DiagnosticToolComponent;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Diagnósticos')]
class DiagnosticIndex extends Component
{
    /**
     * Ferramentas publicadas que são "raiz" (não são componentes de outra ferramenta).
     */
    #[Computed]
    public function tools(): Collection
    {
        $childIds = DiagnosticToolComponent::pluck('child_tool_id');

        return DiagnosticTool::published()
            ->whereNotIn('id', $childIds)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Assessments do usuário logado: draft, in_progress ou completed.
     * Indexados por tool_id para lookup rápido na view.
     */
    #[Computed]
    public function myAssessments(): Collection
    {
        return DiagnosticAssessment::where('user_id', auth()->id())
            ->whereIn('status', [
                DiagnosticAssessmentStatus::Draft->value,
                DiagnosticAssessmentStatus::InProgress->value,
                DiagnosticAssessmentStatus::Submitted->value,
                DiagnosticAssessmentStatus::Analyzing->value,
                DiagnosticAssessmentStatus::InReview->value,
                DiagnosticAssessmentStatus::Completed->value,
            ])
            ->latest()
            ->get()
            ->keyBy('diagnostic_tool_id');
    }

    /**
     * Cria (ou retoma) um assessment e redireciona para o questionário.
     */
    public function startDiagnostic(int $toolId): void
    {
        $tool = DiagnosticTool::published()->findOrFail($toolId);

        // Retoma draft/in_progress existente
        $assessment = DiagnosticAssessment::where('diagnostic_tool_id', $tool->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', [
                DiagnosticAssessmentStatus::Draft->value,
                DiagnosticAssessmentStatus::InProgress->value,
            ])
            ->first();

        if (!$assessment) {
            $assessment = DiagnosticAssessment::create([
                'diagnostic_tool_id' => $tool->id,
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
                'assigned_by' => auth()->id(),
                'tier' => 'start',
                'status' => DiagnosticAssessmentStatus::Draft,
            ]);
        }

        $this->redirect(route('diagnostics.take', $assessment->id), navigate: true);
    }

    /**
     * Reabre o resultado de um assessment concluído.
     */
    public function viewResult(int $assessmentId): void
    {
        $this->redirect(route('diagnostics.result', $assessmentId), navigate: true);
    }

    public function render()
    {
        return view('livewire.diagnostics.index');
    }
}
