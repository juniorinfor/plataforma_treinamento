<?php

namespace App\Livewire\Courses;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
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

    public function enroll(): void
    {
        $user   = auth()->user();
        $course = Course::where('slug', $this->slug)
            ->with(['modules' => fn ($q) => $q->orderBy('sort_order')
                ->with(['lessons' => fn ($q2) => $q2->orderBy('sort_order')])])
            ->firstOrFail();

        Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'enrolled_by'         => $user->id,
                'enrolled_at'         => now(),
                'status'              => 'active',
                'progress_percentage' => 0,
            ]
        );

        // Vai direto para a primeira aula
        $first = $course->modules
            ->sortBy('sort_order')
            ->flatMap(fn ($m) => $m->lessons->sortBy('sort_order'))
            ->first();

        if ($first) {
            $this->redirect(
                $first->type->value === 'quiz'
                    ? route('quiz.play', $first->id)
                    : route('lesson.view', $first->id),
                navigate: true
            );
        }
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
