<?php

namespace App\Livewire\Admin;

use App\Enums\DiagnosticAssessmentStatus;
use App\Models\Course;
use App\Models\DiagnosticAssessment;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Painel do Gestor')]
class AdminDashboard extends Component
{
    private function companyId(): int
    {
        return auth()->user()->company_id;
    }

    // ── Stats ─────────────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        $cid = $this->companyId();

        $totalUsers = User::where('company_id', $cid)->count();

        $enrollments = Enrollment::whereHas('user', fn ($q) => $q->where('company_id', $cid));
        $totalEnrollments = (clone $enrollments)->count();

        $completedEnrollments = (clone $enrollments)
            ->whereNotNull('completed_at')
            ->count();

        $completionRate = $totalEnrollments > 0
            ? (int) round(($completedEnrollments / $totalEnrollments) * 100)
            : 0;

        $diagnosticsDone = DiagnosticAssessment::where('company_id', $cid)
            ->where('status', DiagnosticAssessmentStatus::Completed)
            ->count();

        return compact('totalUsers', 'totalEnrollments', 'completionRate', 'diagnosticsDone');
    }

    // ── Colaboradores inativos ────────────────────────────────────────

    /** Dias sem login que classificam um colaborador como inativo. */
    private const INACTIVE_DAYS = 14;

    #[Computed]
    public function inactive(): array
    {
        $cutoff = Carbon::now()->subDays(self::INACTIVE_DAYS);

        $query = User::where('company_id', $this->companyId())
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('last_login_at')->orWhere('last_login_at', '<', $cutoff));

        $count = (clone $query)->count();
        $list  = (clone $query)
            ->orderByRaw('last_login_at IS NULL DESC')
            ->orderBy('last_login_at')
            ->limit(5)
            ->get(['id', 'name', 'last_login_at']);

        return compact('count', 'list');
    }

    // ── Compliance de cursos obrigatórios ─────────────────────────────

    #[Computed]
    public function mandatoryCompliance(): array
    {
        $cid = $this->companyId();

        $totalUsers = User::where('company_id', $cid)->where('is_active', true)->count();

        $courses = Course::where('is_published', true)
            ->where('is_mandatory', true)
            ->where(fn ($q) => $q->where('company_id', $cid)->orWhere('is_platform_course', true))
            ->get(['id', 'title']);

        $rows = $courses->map(function ($course) use ($cid, $totalUsers) {
            $completed = Enrollment::where('course_id', $course->id)
                ->where('status', 'completed')
                ->whereHas('user', fn ($q) => $q->where('company_id', $cid)->where('is_active', true))
                ->count();

            return [
                'title'     => $course->title,
                'completed' => $completed,
                'total'     => $totalUsers,
                'pct'       => $totalUsers > 0 ? (int) round($completed / $totalUsers * 100) : 0,
            ];
        });

        $overall = $rows->isNotEmpty() ? (int) round($rows->avg('pct')) : null;

        return ['rows' => $rows, 'overall' => $overall];
    }

    // ── Diagnósticos pendentes da equipe ──────────────────────────────

    #[Computed]
    public function pendingDiagnostics(): array
    {
        $query = DiagnosticAssessment::where('company_id', $this->companyId())
            ->whereIn('status', [
                DiagnosticAssessmentStatus::Draft,
                DiagnosticAssessmentStatus::InProgress,
            ]);

        $count = (clone $query)->count();
        $list  = (clone $query)
            ->with(['user:id,name', 'tool:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        return compact('count', 'list');
    }

    // ── Alertas derivados ─────────────────────────────────────────────

    #[Computed]
    public function alerts(): array
    {
        $alerts = [];

        if ($this->stats['completionRate'] < 40 && $this->stats['totalEnrollments'] > 0) {
            $alerts[] = "Taxa de conclusão baixa ({$this->stats['completionRate']}%) — incentive a equipe a retomar os cursos.";
        }

        if ($this->inactive['count'] > 0) {
            $alerts[] = "{$this->inactive['count']} colaborador(es) sem acessar a plataforma há mais de " . self::INACTIVE_DAYS . " dias.";
        }

        $overall = $this->mandatoryCompliance['overall'];
        if ($overall !== null && $overall < 70) {
            $alerts[] = "Compliance de cursos obrigatórios em {$overall}% — abaixo da meta de 70%.";
        }

        if ($this->pendingDiagnostics['count'] > 0) {
            $alerts[] = "{$this->pendingDiagnostics['count']} diagnóstico(s) da equipe iniciados e não concluídos.";
        }

        return $alerts;
    }

    // ── Top cursos por inscrições ─────────────────────────────────────

    #[Computed]
    public function topCourses(): \Illuminate\Support\Collection
    {
        $cid = $this->companyId();

        return Enrollment::selectRaw('course_id, count(*) as total')
            ->whereHas('user', fn ($q) => $q->where('company_id', $cid))
            ->with('course:id,title')
            ->groupBy('course_id')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
    }

    // ── Diagnósticos recentes da empresa ─────────────────────────────

    #[Computed]
    public function recentAssessments(): \Illuminate\Support\Collection
    {
        return DiagnosticAssessment::where('company_id', $this->companyId())
            ->where('status', DiagnosticAssessmentStatus::Completed)
            ->with(['user:id,name', 'tool:id,name,code,color'])
            ->orderByDesc('completed_at')
            ->limit(8)
            ->get();
    }

    // ── Top colaboradores por XP ──────────────────────────────────────

    #[Computed]
    public function topCollaborators(): \Illuminate\Support\Collection
    {
        return User::where('company_id', $this->companyId())
            ->with('points:user_id,total_xp')
            ->get()
            ->sortByDesc(fn ($u) => $u->points?->total_xp ?? 0)
            ->take(5)
            ->values();
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard');
    }
}
