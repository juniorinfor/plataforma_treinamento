<?php

namespace App\Livewire\Dashboard;

use App\Enums\DiagnosticAssessmentStatus;
use App\Models\Badge;
use App\Models\Challenge;
use App\Models\Course;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticTool;
use App\Models\DiagnosticToolComponent;
use App\Models\Enrollment;
use App\Models\UserPoints;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class StudentDashboard extends Component
{
    /**
     * Abre o diagnóstico do PRÓPRIO usuário a partir do banner:
     *  - já concluído  → vai para o resultado;
     *  - em andamento  → retoma o questionário;
     *  - inexistente   → cria um rascunho e inicia.
     */
    public function goToDiagnostic(int $toolId): void
    {
        $tool = DiagnosticTool::published()->findOrFail($toolId);

        $latest = DiagnosticAssessment::where('diagnostic_tool_id', $tool->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        if ($latest && $latest->isViewable()) {
            $this->redirect(route('diagnostics.result', $latest->id), navigate: true);
            return;
        }

        if ($latest && in_array($latest->status, [
            DiagnosticAssessmentStatus::Draft,
            DiagnosticAssessmentStatus::InProgress,
        ], true)) {
            $this->redirect(route('diagnostics.take', $latest->id), navigate: true);
            return;
        }

        $assessment = DiagnosticAssessment::create([
            'diagnostic_tool_id' => $tool->id,
            'user_id'            => auth()->id(),
            'company_id'         => auth()->user()->company_id,
            'assigned_by'        => auth()->id(),
            'tier'               => 'start',
            'status'             => DiagnosticAssessmentStatus::Draft,
        ]);

        $this->redirect(route('diagnostics.take', $assessment->id), navigate: true);
    }

    public function render()
    {
        $user = auth()->user();

        // Diagnósticos "raiz" (não-componentes), mesma fonte da página Diagnósticos.
        // Garante que qualquer diagnóstico novo apareça automaticamente aqui também.
        $childIds = DiagnosticToolComponent::pluck('child_tool_id');
        $diagnosticTools = DiagnosticTool::published()
            ->whereNotIn('id', $childIds)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.dashboard.student-dashboard', [
            'user' => $user,
            'enrollments' => Enrollment::where('user_id', $user->id)->with('course')->latest()->take(4)->get(),
            'points' => UserPoints::where('user_id', $user->id)->first(),
            'recentBadges' => $user->badges()->latest('user_badges.created_at')->take(4)->get(),
            'diagnosticTools' => $diagnosticTools,
            'availableCourses' => Course::where(function($q) use ($user) {
                $q->where('company_id', $user->company_id)->orWhere('is_platform_course', true);
            })->where('is_published', true)->take(4)->get(),
        ]);
    }
}
