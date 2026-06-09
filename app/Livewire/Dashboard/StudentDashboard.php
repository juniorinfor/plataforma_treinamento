<?php

namespace App\Livewire\Dashboard;

use App\Models\Badge;
use App\Models\Challenge;
use App\Models\Course;
use App\Models\DiagnosticTool;
use App\Models\DiagnosticToolComponent;
use App\Models\Enrollment;
use App\Models\UserPoints;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class StudentDashboard extends Component
{
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
