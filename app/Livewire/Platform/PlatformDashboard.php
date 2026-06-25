<?php

namespace App\Livewire\Platform;

use App\Enums\DiagnosticAssessmentStatus;
use App\Enums\DiagnosticReportStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Company;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticReport;
use App\Models\DiagnosticTool;
use App\Models\Enrollment;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Painel da Plataforma')]
class PlatformDashboard extends Component
{
    /** Status que contam como assinatura "viva". */
    private const ACTIVE_STATUSES = [SubscriptionStatus::Trial, SubscriptionStatus::Active];

    // ── KPIs globais ──────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        $totalCompanies = Company::count();
        $totalUsers     = User::count();

        $activeCompanies = Company::with('plan')
            ->whereIn('subscription_status', array_map(fn ($s) => $s->value, self::ACTIVE_STATUSES))
            ->get();

        $activeSubscriptions = $activeCompanies->count();
        $estimatedMrr        = $activeCompanies->sum(fn ($c) => (float) ($c->plan?->price_monthly ?? 0));

        return compact('totalCompanies', 'totalUsers', 'activeSubscriptions', 'estimatedMrr');
    }

    // ── Fila de relatórios de diagnóstico ─────────────────────────────

    #[Computed]
    public function reportQueueCount(): int
    {
        return DiagnosticReport::whereIn('status', [
            DiagnosticReportStatus::Pending,
            DiagnosticReportStatus::AiGenerated,
        ])->count();
    }

    #[Computed]
    public function recentReports(): \Illuminate\Support\Collection
    {
        return DiagnosticReport::whereIn('status', [
            DiagnosticReportStatus::Pending,
            DiagnosticReportStatus::AiGenerated,
        ])
            ->with(['assessment.user:id,name', 'assessment.tool:id,name'])
            ->latest()
            ->limit(6)
            ->get();
    }

    // ── Ferramentas publicadas ────────────────────────────────────────

    #[Computed]
    public function publishedTools(): int
    {
        return DiagnosticTool::published()->count();
    }

    // ── Atividade global ──────────────────────────────────────────────

    #[Computed]
    public function activity(): array
    {
        $completedAssessments = DiagnosticAssessment::where('status', DiagnosticAssessmentStatus::Completed)->count();
        $totalEnrollments     = Enrollment::count();

        return compact('completedAssessments', 'totalEnrollments');
    }

    // ── Empresas recentes ─────────────────────────────────────────────

    #[Computed]
    public function recentCompanies(): \Illuminate\Support\Collection
    {
        return Company::with('plan:id,name')
            ->withCount('users')
            ->latest()
            ->limit(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.platform.platform-dashboard');
    }
}
