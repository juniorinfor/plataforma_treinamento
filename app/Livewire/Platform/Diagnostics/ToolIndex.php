<?php

namespace App\Livewire\Platform\Diagnostics;

use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticTool;
use App\Models\DiagnosticToolComponent;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Ferramentas de Diagnóstico')]
class ToolIndex extends Component
{
    public ?string $confirmDelete = null;

    #[Computed]
    public function tools()
    {
        $childIds = DiagnosticToolComponent::pluck('child_tool_id');

        return DiagnosticTool::withCount(['questions', 'assessments'])
            ->whereNotIn('id', $childIds)           // só ferramentas raiz
            ->orderBy('sort_order')
            ->get();
    }

    public function togglePublish(int $toolId): void
    {
        $tool = DiagnosticTool::findOrFail($toolId);
        $tool->update(['is_published' => !$tool->is_published]);
        $this->dispatch('notify', message: $tool->is_published ? 'Ferramenta publicada.' : 'Ferramenta despublicada.');
    }

    public function deleteTool(int $toolId): void
    {
        DiagnosticTool::findOrFail($toolId)->delete();
        $this->confirmDelete = null;
        $this->dispatch('notify', message: 'Ferramenta excluída.');
    }

    public function render()
    {
        return view('livewire.platform.diagnostics.tool-index');
    }
}
