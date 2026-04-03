<?php

namespace App\Livewire\Courses;

use App\Models\Course;
use App\Models\Enrollment;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CourseShow extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $user = auth()->user();
        $course = Course::where('slug', $this->slug)
            ->with(['modules.lessons', 'category', 'creator'])
            ->firstOrFail();
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();

        return view('livewire.courses.course-show', [
            'course' => $course,
            'enrollment' => $enrollment,
        ]);
    }
}
