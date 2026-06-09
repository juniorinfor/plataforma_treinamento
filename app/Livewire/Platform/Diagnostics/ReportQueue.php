<?php

namespace App\Livewire\Platform\Diagnostics;

use App\Enums\DiagnosticReportStatus;
use App\Models\DiagnosticReport;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Fila de Relatórios')]
class ReportQueue extends Component
{
    use WithPagination;

    public string $filterStatus = '';
    public string $search = '';

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function statusCounts(): array
    {
        $raw = DiagnosticReport::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'all'    => array_sum($raw),
            'counts' => $raw,
        ];
    }

    #[Computed]
    public function reports()
    {
        return DiagnosticReport::with([
            'assessment.user:id,name,email',
            'assessment.tool:id,name,code,color',
        ])
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, function ($q) {
                $q->whereHas('assessment.user', fn ($q2) =>
                    $q2->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%")
                );
            })
            ->latest()
            ->paginate(15);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function statusColor(string $status): string
    {
        return match ($status) {
            'pending'      => 'bg-gray-100 text-gray-600',
            'ai_generated' => 'bg-purple-100 text-purple-700',
            'in_review'    => 'bg-amber-100 text-amber-700',
            'approved'     => 'bg-blue-100 text-blue-700',
            'published'    => 'bg-emerald-100 text-emerald-700',
            default        => 'bg-gray-100 text-gray-600',
        };
    }

    public function render()
    {
        return view('livewire.platform.diagnostics.report-queue');
    }
}
