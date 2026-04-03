<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AdminDashboard extends Component
{
    public function render()
    {
        $companyId = auth()->user()->company_id;
        return view('livewire.admin.admin-dashboard', [
            'totalUsers' => User::where('company_id', $companyId)->count(),
            'totalCourses' => Course::where('company_id', $companyId)->orWhere('is_platform_course', true)->count(),
            'totalEnrollments' => Enrollment::whereHas('user', fn($q) => $q->where('company_id', $companyId))->count(),
            'completionRate' => 68,
        ]);
    }
}
