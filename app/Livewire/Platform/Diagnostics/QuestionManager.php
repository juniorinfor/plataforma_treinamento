<?php

namespace App\Livewire\Platform\Diagnostics;

use App\Enums\DiagnosticQuestionType;
use App\Models\DiagnosticDimension;
use App\Models\DiagnosticQuestion;
use App\Models\DiagnosticQuestionOption;
use App\Models\DiagnosticTool;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Perguntas')]
class QuestionManager extends Component
{
    public DiagnosticTool $tool;

    // ── Painel lateral de edição ──────────────────────────────────────
    public bool   $showPanel       = false;
    public ?int   $editingId       = null;   // null = nova pergunta

    public string $qContent        = '';
    public string $qHelp           = '';
    public string $qType           = 'scale';
    public ?int   $qDimensionId    = null;
    public bool   $qRequired       = true;
    public bool   $qReverse        = false;
    public float  $qWeight         = 1.0;
    public int    $qSortOrder      = 0;

    // Opções da pergunta em edição
    public array $options = [];   // [{id?, content, value, sort_order}]
    public string $optContent = '';
    public float  $optValue   = 0.0;

    // ── Confirmação de exclusão ───────────────────────────────────────
    public ?int $confirmDeleteQuestion = null;

    // ── Escala Likert padrão ─────────────────────────────────────────
    private array $defaultLikert = [
        ['content' => 'Discordo totalmente', 'value' => 1],
        ['content' => 'Discordo',            'value' => 2],
        ['content' => 'Neutro',              'value' => 3],
        ['content' => 'Concordo',            'value' => 4],
        ['content' => 'Concordo totalmente', 'value' => 5],
    ];

    public function mount(DiagnosticTool $tool): void
    {
        $this->tool = $tool;
    }

    #[Computed]
    public function questions()
    {
        return $this->tool
            ->questions()
            ->with('dimension', 'options')
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function dimensions()
    {
        return DiagnosticDimension::where('diagnostic_tool_id', $this->tool->id)
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function questionTypes()
    {
        return DiagnosticQuestionType::cases();
    }

    // ── Abre painel para NOVA pergunta ───────────────────────────────
    public function openNew(): void
    {
        $this->resetPanel();
        $this->qSortOrder = $this->tool->questions()->max('sort_order') + 1;
        $this->options    = $this->defaultLikert;
        $this->showPanel  = true;
    }

    // ── Abre painel para EDITAR pergunta existente ───────────────────
    public function openEdit(int $questionId): void
    {
        $this->resetPanel();
        $q = DiagnosticQuestion::with('options')->findOrFail($questionId);

        $this->editingId     = $q->id;
        $this->qContent      = $q->content;
        $this->qHelp         = $q->help_text ?? '';
        $this->qType         = $q->type->value;
        $this->qDimensionId  = $q->diagnostic_dimension_id;
        $this->qRequired     = $q->is_required;
        $this->qReverse      = $q->reverse_scored;
        $this->qWeight       = (float) $q->weight;
        $this->qSortOrder    = $q->sort_order;

        $this->options = $q->options->sortBy('sort_order')->map(fn ($o) => [
            'id'         => $o->id,
            'content'    => $o->content,
            'value'      => (float) $o->value,
            'sort_order' => $o->sort_order,
        ])->values()->toArray();

        $this->showPanel = true;
    }

    // ── Preenche opções com escala Likert padrão ─────────────────────
    public function fillLikert(): void
    {
        $this->options = $this->defaultLikert;
    }

    // ── Adiciona opção manualmente ───────────────────────────────────
    public function addOption(): void
    {
        $this->validate([
            'optContent' => 'required|min:1',
        ], ['optContent.required' => 'Informe o texto da opção.']);

        $this->options[] = [
            'id'         => null,
            'content'    => $this->optContent,
            'value'      => $this->optValue,
            'sort_order' => count($this->options) + 1,
        ];

        $this->optContent = '';
        $this->optValue   = (float) (count($this->options));
        $this->resetErrorBag('optContent');
    }

    public function removeOption(int $i): void
    {
        array_splice($this->options, $i, 1);
    }

    // ── Salva pergunta ───────────────────────────────────────────────
    public function saveQuestion(): void
    {
        $this->validate([
            'qContent'  => 'required|min:5',
            'qType'     => 'required',
            'qWeight'   => 'required|numeric|min:0.1',
        ], [
            'qContent.required' => 'O enunciado da pergunta é obrigatório.',
            'qContent.min'      => 'O enunciado deve ter ao menos 5 caracteres.',
        ]);

        $data = [
            'diagnostic_tool_id'      => $this->tool->id,
            'diagnostic_dimension_id' => $this->qDimensionId ?: null,
            'type'                    => $this->qType,
            'content'                 => $this->qContent,
            'help_text'               => $this->qHelp ?: null,
            'is_required'             => $this->qRequired,
            'reverse_scored'          => $this->qReverse,
            'weight'                  => $this->qWeight,
            'sort_order'              => $this->qSortOrder,
        ];

        if ($this->editingId) {
            $question = DiagnosticQuestion::findOrFail($this->editingId);
            $question->update($data);
        } else {
            $question = DiagnosticQuestion::create($data);
        }

        // Sincroniza opções
        $keptIds = collect($this->options)->pluck('id')->filter()->toArray();
        DiagnosticQuestionOption::where('diagnostic_question_id', $question->id)
            ->whereNotIn('id', $keptIds)
            ->delete();

        foreach ($this->options as $i => $opt) {
            $optData = [
                'diagnostic_question_id' => $question->id,
                'content'    => $opt['content'],
                'value'      => $opt['value'],
                'sort_order' => $i + 1,
            ];

            if (!empty($opt['id'])) {
                DiagnosticQuestionOption::where('id', $opt['id'])->update($optData);
            } else {
                DiagnosticQuestionOption::create($optData);
            }
        }

        unset($this->questions);   // limpa cache computed
        $this->resetPanel();
        $this->showPanel = false;
    }

    // ── Move pergunta para cima / para baixo ─────────────────────────
    public function moveUp(int $questionId): void
    {
        $this->reorder($questionId, -1);
    }

    public function moveDown(int $questionId): void
    {
        $this->reorder($questionId, +1);
    }

    private function reorder(int $questionId, int $direction): void
    {
        $questions = $this->tool->questions()->orderBy('sort_order')->get();
        $idx = $questions->search(fn ($q) => $q->id === $questionId);
        $swapIdx = $idx + $direction;

        if ($swapIdx < 0 || $swapIdx >= $questions->count()) {
            return;
        }

        $a = $questions[$idx];
        $b = $questions[$swapIdx];

        [$a->sort_order, $b->sort_order] = [$b->sort_order, $a->sort_order];
        $a->save();
        $b->save();

        unset($this->questions);
    }

    // ── Exclui pergunta ──────────────────────────────────────────────
    public function deleteQuestion(int $questionId): void
    {
        DiagnosticQuestion::findOrFail($questionId)->delete();
        $this->confirmDeleteQuestion = null;
        unset($this->questions);
    }

    // ── Reset do painel ──────────────────────────────────────────────
    private function resetPanel(): void
    {
        $this->editingId    = null;
        $this->qContent     = '';
        $this->qHelp        = '';
        $this->qType        = 'scale';
        $this->qDimensionId = null;
        $this->qRequired    = true;
        $this->qReverse     = false;
        $this->qWeight      = 1.0;
        $this->qSortOrder   = 0;
        $this->options      = [];
        $this->optContent   = '';
        $this->optValue     = 0.0;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.platform.diagnostics.question-manager');
    }
}
