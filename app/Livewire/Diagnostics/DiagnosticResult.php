<?php

namespace App\Livewire\Diagnostics;

use App\Enums\DiagnosticAssessmentStatus;
use App\Models\DiagnosticAssessment;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Resultado do Diagnóstico')]
class DiagnosticResult extends Component
{
    #[Locked]
    public int $assessmentId;

    public function mount(DiagnosticAssessment $assessment): void
    {
        $user = auth()->user();

        // Dono, Admin do Sistema ou Gestor da mesma empresa podem visualizar.
        abort_unless($assessment->canBeViewedBy($user), 403);

        if (!$assessment->isViewable()) {
            // Ainda não concluído: o dono retoma; um supervisor volta ao índice.
            if ($assessment->user_id === $user->id) {
                $this->redirect(route('diagnostics.take', $assessment->id), navigate: true);
            } else {
                session()->flash('error', 'Este diagnóstico ainda não foi concluído.');
                $this->redirect(route('diagnostics.index'), navigate: true);
            }
            return;
        }

        $this->assessmentId = $assessment->id;
    }

    #[Computed]
    public function assessment(): DiagnosticAssessment
    {
        return DiagnosticAssessment::with([
            'tool',
            'results' => fn ($q) => $q->orderBy('id'),
            'results.componentTool',
            'results.dimension',
            'report',
        ])->find($this->assessmentId);
    }

    /**
     * Dados estruturados para os gráficos ApexCharts.
     */
    #[Computed]
    public function chartData(): array
    {
        $assessment = $this->assessment;
        $results    = $assessment->results;

        $labels = $scores = $colors = $codes = [];

        foreach ($results as $result) {
            $tool   = $result->componentTool;
            $dim    = $result->dimension;
            $labels[] = $tool?->name ?? $dim?->name ?? '—';
            $codes[]  = $tool?->code ?? $dim?->code ?? '';
            $scores[] = (float) round($result->normalized_score, 1);
            $colors[] = $tool?->color ?? $dim?->color ?? '#6366F1';
        }

        $globalScore = (float) ($assessment->global_score ?? 0);
        $globalColor = match (true) {
            $globalScore >= 85 => '#10B981',
            $globalScore >= 70 => '#3B82F6',
            $globalScore >= 55 => '#F59E0B',
            $globalScore >= 40 => '#EF4444',
            default            => '#6B7280',
        };

        return [
            'labels'      => $labels,
            'codes'       => $codes,
            'scores'      => $scores,
            'colors'      => $colors,
            'globalScore' => $globalScore,
            'globalLabel' => $assessment->global_label ?? '—',
            'globalColor' => $globalColor,
        ];
    }

    /**
     * Texto de interpretação por faixa de score.
     */
    public function interpretation(float $score): string
    {
        return match (true) {
            $score >= 85 => 'Zona de excelência. Mantenha e sirva de referência.',
            $score >= 70 => 'Ambiente saudável com boas práticas consolidadas.',
            $score >= 55 => 'Atenção necessária. Há pontos de melhoria claros.',
            $score >= 40 => 'Situação crítica. Ação imediata recomendada.',
            default      => 'Vulnerabilidade alta. Plano estruturado urgente.',
        };
    }

    public function render()
    {
        return view('livewire.diagnostics.result');
    }
}
