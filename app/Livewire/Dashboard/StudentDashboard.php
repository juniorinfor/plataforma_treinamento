<?php

namespace App\Livewire\Dashboard;

use App\Models\Badge;
use App\Models\Challenge;
use App\Models\Course;
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

        return view('livewire.dashboard.student-dashboard', [
            'user' => $user,
            'enrollments' => Enrollment::where('user_id', $user->id)->with('course')->latest()->take(4)->get(),
            'points' => UserPoints::where('user_id', $user->id)->first(),
            'recentBadges' => $user->badges()->latest('user_badges.created_at')->take(4)->get(),
            'availableCourses' => Course::where(function($q) use ($user) {
                $q->where('company_id', $user->company_id)->orWhere('is_platform_course', true);
            })->where('is_published', true)->take(4)->get(),
        ]);
    }
}
