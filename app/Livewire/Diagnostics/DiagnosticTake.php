<?php

namespace App\Livewire\Diagnostics;

use App\Enums\DiagnosticAssessmentStatus;
use App\Enums\DiagnosticToolType;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticTool;
use App\Services\DiagnosticScoringService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Respondendo Diagnóstico')]
class DiagnosticTake extends Component
{
    #[Locked]
    public int $assessmentId;

    public int $currentStep = 0;

    /** @var array<int, int>  [question_id => option_id] */
    public array $answers = [];

    public function mount(DiagnosticAssessment $assessment): void
    {
        $user = auth()->user();

        // Responder o questionário é exclusivo do dono.
        // Admin/Gestor não respondem por outro: são redirecionados (sem 403).
        if ($assessment->user_id !== $user->id) {
            if ($assessment->canBeViewedBy($user) && $assessment->isViewable()) {
                $this->redirect(route('diagnostics.result', $assessment->id), navigate: true);
                return;
            }

            if ($assessment->canBeViewedBy($user)) {
                session()->flash('error', 'Este diagnóstico pertence a outro usuário e ainda não foi concluído.');
                $this->redirect(route('diagnostics.index'), navigate: true);
                return;
            }

            abort(403);
        }

        // Não permite refazer um diagnóstico já concluído
        if ($assessment->status === DiagnosticAssessmentStatus::Completed) {
            $this->redirect(route('diagnostics.result', $assessment->id), navigate: true);
            return;
        }

        $this->assessmentId = $assessment->id;

        // Marca como iniciado na primeira abertura
        if ($assessment->status === DiagnosticAssessmentStatus::Draft) {
            $assessment->update([
                'status' => DiagnosticAssessmentStatus::InProgress,
                'started_at' => now(),
            ]);
        }

        // Pré-carrega respostas salvas (permite retomar)
        $this->answers = $assessment->answers
            ->pluck('diagnostic_question_option_id', 'diagnostic_question_id')
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->toArray();
    }

    #[Computed]
    public function assessment(): DiagnosticAssessment
    {
        return DiagnosticAssessment::find($this->assessmentId);
    }

    /**
     * Retorna os steps do questionário.
     * - Ferramenta composta → 1 step por ferramenta componente.
     * - Ferramenta simples  → 1 step por dimensão (ou 1 step único).
     *
     * Cada step: ['tool_id', 'code', 'name', 'color', 'icon', 'questions']
     */
    #[Computed]
    public function steps(): array
    {
        $tool = DiagnosticTool::with([
            'children' => fn ($q) => $q->withPivot(['weight', 'sort_order'])->orderByPivot('sort_order'),
            'children.dimensions',
            'children.dimensions.questions' => fn ($q) => $q->orderBy('sort_order'),
            'children.dimensions.questions.options' => fn ($q) => $q->orderBy('sort_order'),
            'dimensions',
            'dimensions.questions' => fn ($q) => $q->orderBy('sort_order'),
            'dimensions.questions.options' => fn ($q) => $q->orderBy('sort_order'),
        ])->find($this->assessment->diagnostic_tool_id);

        if ($tool->type === DiagnosticToolType::Composite) {
            return $tool->children->map(fn ($child) => [
                'tool_id' => $child->id,
                'code' => $child->code ?? '',
                'name' => $child->name,
                'color' => $child->color ?? '#6366F1',
                'icon' => $child->icon ?? 'chart-bar',
                'questions' => $child->dimensions
                    ->flatMap(fn ($d) => $d->questions)
                    ->sortBy('sort_order')
                    ->values()
                    ->all(),
            ])->toArray();
        }

        if ($tool->dimensions->isNotEmpty()) {
            return $tool->dimensions->map(fn ($dim) => [
                'tool_id' => $tool->id,
                'code' => $dim->code ?? $tool->code ?? '',
                'name' => $dim->name,
                'color' => $dim->color ?? $tool->color ?? '#3B82F6',
                'icon' => $tool->icon ?? 'chart-bar',
                'questions' => $dim->questions->sortBy('sort_order')->values()->all(),
            ])->toArray();
        }

        // Fallback: uma única etapa com todas as perguntas
        return [[
            'tool_id' => $tool->id,
            'code' => $tool->code ?? '',
            'name' => $tool->name,
            'color' => $tool->color ?? '#3B82F6',
            'icon' => $tool->icon ?? 'chart-bar',
            'questions' => $tool->questions()->with('options')->orderBy('sort_order')->get()->all(),
        ]];
    }

    public function totalSteps(): int
    {
        return count($this->steps);
    }

    public function progressPercent(): int
    {
        $total = $this->totalSteps();
        if ($total === 0) {
            return 0;
        }
        return (int) (($this->currentStep / $total) * 100);
    }

    public function answeredInStep(int $step): int
    {
        $questions = $this->steps[$step]['questions'] ?? [];
        $answered = 0;
        foreach ($questions as $q) {
            if (isset($this->answers[$q->id])) {
                $answered++;
            }
        }
        return $answered;
    }

    public function selectAnswer(int $questionId, int $optionId): void
    {
        $this->answers[$questionId] = $optionId;
    }

    public function nextStep(): void
    {
        if (!$this->validateCurrentStep()) {
            return;
        }

        if ($this->currentStep < $this->totalSteps() - 1) {
            $this->currentStep++;
            $this->dispatch('scroll-top');
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
            $this->dispatch('scroll-top');
        }
    }

    public function submit(): void
    {
        if (!$this->validateCurrentStep()) {
            return;
        }

        $assessment = $this->assessment;

        app(DiagnosticScoringService::class)->score($assessment, $this->answers);

        // Gamificação: XP + badges + desafios
        $result = app(\App\Services\GamificationService::class)
            ->onDiagnosticComplete(auth()->user(), $assessment->fresh());

        if ($result['leveled_up']) {
            $this->dispatch('level-up');
        }
        if ($result['badges']->isNotEmpty()) {
            $this->dispatch('badge-earned', badge: $result['badges']->first()->name);
        }

        $this->redirect(route('diagnostics.result', $assessment->id), navigate: true);
    }

    private function validateCurrentStep(): bool
    {
        $questions = $this->steps[$this->currentStep]['questions'] ?? [];

        foreach ($questions as $question) {
            if ($question->is_required && !isset($this->answers[$question->id])) {
                $this->addError(
                    'step',
                    'Responda todas as perguntas obrigatórias antes de continuar.'
                );
                return false;
            }
        }

        $this->resetErrorBag('step');
        return true;
    }

    public function render()
    {
        return view('livewire.diagnostics.take');
    }
}
