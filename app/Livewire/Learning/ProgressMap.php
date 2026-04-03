<?php

namespace App\Livewire\Learning;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ProgressMap extends Component
{
    public function render()
    {
        $user = auth()->user();
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['course.modules.lessons'])
            ->get();

        return view('livewire.learning.progress-map', [
            'enrollments' => $enrollments,
        ]);
    }
}
