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
        $courses = Course::where('company_id', auth()->user()->company_id)
            ->orWhere('is_platform_course', true)
            ->withCount('enrollments')
            ->get();
        return view('livewire.admin.course-management', ['courses' => $courses]);
    }
}
