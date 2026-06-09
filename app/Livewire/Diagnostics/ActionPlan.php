<?php

namespace App\Livewire\Diagnostics;

use App\Models\DiagnosticActionItem;
use App\Models\DiagnosticActionPlan;
use App\Models\DiagnosticAssessment;
use App\Services\DiagnosticActionPlanService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Plano de Ação')]
class ActionPlan extends Component
{
    #[Locked]
    public int $assessmentId;

    public function mount(DiagnosticAssessment $assessment): void
    {
        // Apenas o dono do assessment pode ver o plano
        abort_if($assessment->user_id !== auth()->id(), 403);

        $this->assessmentId = $assessment->id;

        // Garante que o plano existe; gera se necessário
        if (!DiagnosticActionPlan::where('diagnostic_assessment_id', $assessment->id)->exists()) {
            app(DiagnosticActionPlanService::class)->generate($assessment);
        }
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function assessment(): DiagnosticAssessment
    {
        return DiagnosticAssessment::with(['tool', 'results.componentTool', 'results.dimension'])
            ->findOrFail($this->assessmentId);
    }

    #[Computed]
    public function plan(): ?DiagnosticActionPlan
    {
        return DiagnosticActionPlan::where('diagnostic_assessment_id', $this->assessmentId)
            ->with([
                'items' => fn ($q) => $q->orderBy('sort_order'),
                'items.result.componentTool',
                'items.result.dimension',
                'items.course',
            ])
            ->first();
    }

    /**
     * Retorna os itens agrupados por resultado (índice / dimensão).
     * Formato: [ ['result' => DiagnosticResult|null, 'label' => string, 'color' => string, 'score' => float, 'items' => Collection] ]
     */
    #[Computed]
    public function groupedItems(): array
    {
        if (!$this->plan) {
            return [];
        }

        $groups = [];

        foreach ($this->plan->items as $item) {
            $result = $item->result;
            $key    = $result?->id ?? 0;

            if (!isset($groups[$key])) {
                $label  = $result?->componentTool?->name ?? $result?->dimension?->name ?? 'Geral';
                $code   = $result?->componentTool?->code ?? $result?->dimension?->code ?? '';
                $color  = $result?->componentTool?->color
                       ?? $result?->dimension?->color
                       ?? ($this->assessment->tool->color ?? '#6366F1');
                $score  = $result ? (float) $result->normalized_score : null;

                $groups[$key] = [
                    'result' => $result,
                    'label'  => $label,
                    'code'   => $code,
                    'color'  => $color,
                    'score'  => $score,
                    'items'  => collect(),
                ];
            }

            $groups[$key]['items']->push($item);
        }

        return array_values($groups);
    }

    // ── Actions ───────────────────────────────────────────────────────

    public function toggleItem(int $itemId): void
    {
        $item = DiagnosticActionItem::findOrFail($itemId);

        // Segurança: item deve pertencer ao plano do usuário autenticado
        abort_if($item->plan->user_id !== auth()->id(), 403);

        $isDone = $item->status === 'done';

        $item->update([
            'status'       => $isDone ? 'pending' : 'done',
            'completed_at' => $isDone ? null : now(),
        ]);

        $item->plan->syncProgress();

        unset($this->plan);
    }

    public function render()
    {
        return view('livewire.diagnostics.action-plan');
    }
}
