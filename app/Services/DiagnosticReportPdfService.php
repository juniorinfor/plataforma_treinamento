<?php

namespace App\Services;

use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class DiagnosticReportPdfService
{
    /**
     * Gera o PDF a partir de um DiagnosticAssessment (com ou sem relatório publicado).
     * Retorna uma response para download imediato.
     */
    public function generateFromAssessment(DiagnosticAssessment $assessment): Response
    {
        $assessment->loadMissing([
            'tool',
            'user',
            'user.company',
            'results' => fn ($q) => $q->orderBy('id'),
            'results.componentTool',
            'results.dimension',
            'report',
        ]);

        $data = $this->buildViewData($assessment, $assessment->report);

        $pdf = Pdf::loadView('pdf.diagnostic-report', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'helvetica',
                'dpi'                  => 150,
            ]);

        $filename = $this->buildFilename($assessment);

        return $pdf->download($filename);
    }

    /**
     * Gera o PDF diretamente de um DiagnosticReport (usado pelo admin).
     */
    public function generateFromReport(DiagnosticReport $report): Response
    {
        $report->loadMissing([
            'assessment.tool',
            'assessment.user.company',
            'assessment.results.componentTool',
            'assessment.results.dimension',
        ]);

        $data = $this->buildViewData($report->assessment, $report);

        $pdf = Pdf::loadView('pdf.diagnostic-report', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'helvetica',
                'dpi'                  => 150,
            ]);

        $filename = $this->buildFilename($report->assessment);

        return $pdf->download($filename);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function buildViewData(DiagnosticAssessment $assessment, ?DiagnosticReport $report): array
    {
        $tool    = $assessment->tool;
        $user    = $assessment->user;
        $company = $user?->company;
        $score   = (float) ($assessment->global_score ?? 0);

        $accentColor = $tool->color ?? '#6366F1';
        $companyColor = $company?->primary_color ?? $accentColor;

        $scoreColor = match (true) {
            $score >= 85 => '#10B981',
            $score >= 70 => '#3B82F6',
            $score >= 55 => '#F59E0B',
            $score >= 40 => '#EF4444',
            default      => '#6B7280',
        };

        $results = $assessment->results->map(function ($r) use ($accentColor) {
            $s = (float) $r->normalized_score;
            return [
                'name'        => $r->componentTool?->name ?? $r->dimension?->name ?? '—',
                'code'        => $r->componentTool?->code ?? $r->dimension?->code ?? '',
                'color'       => $r->componentTool?->color ?? $r->dimension?->color ?? $accentColor,
                'score'       => $s,
                'label'       => $r->label,
                'score_color' => match (true) {
                    $s >= 85 => '#10B981',
                    $s >= 70 => '#3B82F6',
                    $s >= 55 => '#F59E0B',
                    $s >= 40 => '#EF4444',
                    default  => '#6B7280',
                },
                'bar_width'   => (int) round($s),
            ];
        })->toArray();

        $hasReport = $report !== null
            && $report->status === \App\Enums\DiagnosticReportStatus::Published
            && ($report->content || $report->archetype || !empty($report->highlights) || !empty($report->swot));

        return compact(
            'assessment', 'tool', 'user', 'company',
            'score', 'accentColor', 'companyColor', 'scoreColor',
            'results', 'report', 'hasReport'
        );
    }

    private function buildFilename(DiagnosticAssessment $assessment): string
    {
        $name = $assessment->user?->name ?? 'colaborador';
        $name = \Str::slug($name, '-');
        $code = strtolower($assessment->tool?->code ?? 'diagnostico');
        $date = $assessment->completed_at?->format('Y-m-d') ?? now()->format('Y-m-d');

        return "relatorio-{$code}-{$name}-{$date}.pdf";
    }
}
