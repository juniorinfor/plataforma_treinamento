<?php

namespace App\Livewire\Platform\Diagnostics;

use App\Models\DiagnosticTool;
use App\Models\DiagnosticToolComponent;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Área de edição dos prompts de IA por diagnóstico (somente Admin do Sistema).
 * O prompt definido aqui é combinado com as perguntas/resultados do diagnóstico
 * para gerar o relatório de IA (ver DiagnosticReportService::buildPrompt).
 */
#[Layout('components.layouts.app')]
#[Title('Prompts de IA')]
class PromptManager extends Component
{
    /** @var array<int, string> prompt por tool_id */
    public array $prompts = [];

    public ?int $justSaved = null;

    public function mount(): void
    {
        foreach ($this->rootTools() as $tool) {
            $this->prompts[$tool->id] = $tool->ai_prompt ?? '';
        }
    }

    private function rootTools(): Collection
    {
        $childIds = DiagnosticToolComponent::pluck('child_tool_id');

        return DiagnosticTool::whereNotIn('id', $childIds)
            ->orderBy('sort_order')
            ->get();
    }

    public function save(int $toolId): void
    {
        $tool = DiagnosticTool::findOrFail($toolId);
        $tool->update([
            'ai_prompt' => trim((string) ($this->prompts[$toolId] ?? '')) ?: null,
        ]);

        $this->justSaved = $toolId;
        $this->dispatch('notify', message: 'Prompt salvo.');
    }

    public function render()
    {
        return view('livewire.platform.diagnostics.prompt-manager', [
            'tools' => $this->rootTools(),
        ]);
    }
}
