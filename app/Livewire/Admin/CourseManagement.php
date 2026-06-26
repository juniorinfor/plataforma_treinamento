<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CourseManagement extends Component
{
    public function render()
    {
        $user = auth()->user();
        $query = Course::withCount('enrollments')->with('company:id,name');

        if ($user->isPlatformAdmin()) {
            // Platform admin vê todos os cursos
            $query->orderByDesc('is_platform_course')->orderBy('title');
        } else {
            // Gestor vê cursos da empresa + cursos de plataforma
            $query->where(fn ($q) =>
                $q->where('company_id', $user->company_id)
                  ->orWhere('is_platform_course', true)
            )->orderByDesc('is_platform_course')->orderBy('title');
        }

        return view('livewire.admin.course-management', ['courses' => $query->get()]);
    }
}
