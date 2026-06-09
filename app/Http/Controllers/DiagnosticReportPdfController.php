<?php

namespace App\Http\Controllers;

use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticReport;
use App\Services\DiagnosticReportPdfService;

class DiagnosticReportPdfController extends Controller
{
    public function __construct(
        private readonly DiagnosticReportPdfService $pdfService
    ) {}

    /**
     * Download do PDF pelo próprio colaborador.
     * Só permite ao dono do assessment.
     */
    public function downloadAssessment(DiagnosticAssessment $assessment)
    {
        abort_if($assessment->user_id !== auth()->id(), 403);
        abort_unless($assessment->isCompleted(), 404);

        return $this->pdfService->generateFromAssessment($assessment);
    }

    /**
     * Download do PDF pelo platform_admin (a partir do relatório).
     * Não exige que o relatório esteja publicado — o admin pode baixar em qualquer status.
     */
    public function downloadReport(DiagnosticReport $report)
    {
        return $this->pdfService->generateFromReport($report);
    }
}
