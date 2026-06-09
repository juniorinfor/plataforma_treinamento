<?php

namespace App\Livewire\Admin;

use App\Enums\DiagnosticAssessmentStatus;
use App\Models\DiagnosticAssessment;
use App\Models\Enrollment;
use App\Models\User;
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
