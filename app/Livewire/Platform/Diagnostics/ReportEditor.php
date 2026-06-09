<?php

namespace App\Livewire\Platform\Diagnostics;

use App\Enums\DiagnosticReportStatus;
use App\Models\DiagnosticReport;
use App\Services\DiagnosticReportService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Editor de Relatório')]
class ReportEditor extends Component
{
    #[Locked]
    public int $reportId;

    public string $archetype  = '';
    public string $content    = '';

    /** @var array<int, string> */
    public array $highlights = ['', '', ''];

    /** @var array<string, array<int, string>> */
    public array $swot = [
        'strengths'     => ['', ''],
        'weaknesses'    => ['', ''],
        'opportunities' => ['', ''],
        'threats'       => ['', ''],
    ];

    // ── Mount ─────────────────────────────────────────────────────────

    public function mount(DiagnosticReport $report): void
    {
        $this->reportId  = $report->id;
        $this->archetype = $report->archetype ?? '';
        $this->content   = $report->content ?? '';

        $this->highlights = array_pad(
            array_values($report->highlights ?? []),
            3,
            ''
        );

        if (!empty($report->swot)) {
            foreach ($report->swot as $key => $values) {
                if (array_key_exists($key, $this->swot)) {
                    $this->swot[$key] = array_pad(array_values((array) $values), 2, '');
                }
            }
        }
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function report(): DiagnosticReport
    {
        return DiagnosticReport::with([
            'assessment.user',
            'assessment.tool',
            'assessment.results.componentTool',
            'assessment.results.dimension',
            'aiProvider:id,name,driver',
        ])->findOrFail($this->reportId);
    }

    // ── Actions ───────────────────────────────────────────────────────

    /**
     * (Re)gera o rascunho via IA e popula os campos do editor.
     */
    public function generateAiDraft(): void
    {
        $refreshed = app(DiagnosticReportService::class)->generateAiDraft($this->report);

        $this->archetype  = $refreshed->archetype  ?? $this->archetype;
        $this->content    = $refreshed->content    ?? $this->content;
        $this->highlights = array_pad(
            array_values($refreshed->highlights ?? []),
            3,
            ''
        );

        if (!empty($refreshed->swot)) {
            foreach ($refreshed->swot as $key => $values) {
                if (array_key_exists($key, $this->swot)) {
                    $this->swot[$key] = array_pad(array_values((array) $values), 2, '');
                }
            }
        }

        unset($this->report);
        $this->dispatch('saved');
        session()->flash('success', 'Rascunho gerado pela IA!');
    }

    /**
     * Persiste o conteúdo editado sem alterar o status.
     */
    public function save(): void
    {
        $this->report->update([
            'archetype'  => trim($this->archetype),
            'content'    => trim($this->content),
            'highlights' => array_values(array_filter(array_map('trim', $this->highlights))),
            'swot'       => array_map(
                fn ($arr) => array_values(array_filter(array_map('trim', $arr))),
                $this->swot
            ),
        ]);

        unset($this->report);
        session()->flash('success', 'Relatório salvo!');
    }

    /**
     * Salva e muda o status para "em revisão".
     */
    public function markInReview(): void
    {
        $this->save();

        $this->report->update(['status' => DiagnosticReportStatus::InReview]);

        unset($this->report);
        session()->flash('success', 'Relatório marcado como Em Revisão.');
    }

    /**
     * Salva, aprova e publica — o colaborador passa a ver o relatório.
     */
    public function publish(): void
    {
        $this->save();

        $this->report->update([
            'status'       => DiagnosticReportStatus::Published,
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
            'published_at' => now(),
        ]);

        unset($this->report);
        session()->flash('success', 'Relatório publicado! O colaborador já pode visualizá-lo.');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function statusBadge(DiagnosticReportStatus $status): string
    {
        return match ($status) {
            DiagnosticReportStatus::Pending      => 'bg-gray-100 text-gray-600',
            DiagnosticReportStatus::AiGenerated  => 'bg-purple-100 text-purple-700',
            DiagnosticReportStatus::InReview     => 'bg-amber-100 text-amber-700',
            DiagnosticReportStatus::Approved     => 'bg-blue-100 text-blue-700',
            DiagnosticReportStatus::Published    => 'bg-emerald-100 text-emerald-700',
        };
    }

    public function render()
    {
        return view('livewire.platform.diagnostics.report-editor');
    }
}
