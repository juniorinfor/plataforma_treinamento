<?php

namespace App\Livewire\Courses;

use App\Models\Category;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CourseIndex extends Component
{
    public string $search = '';
    public string $categoryFilter = '';
    public string $tab = 'all';

    public function render()
    {
        $user = auth()->user();
        $query = Course::where('is_published', true)
            ->where(function($q) use ($user) {
                $q->where('company_id', $user->company_id)
                  ->orWhere('is_platform_course', true);
            });

        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }
        if ($this->tab === 'company') {
            $query->where('company_id', $user->company_id);
        } elseif ($this->tab === 'platform') {
            $query->where('is_platform_course', true);
        } elseif ($this->tab === 'mandatory') {
            $query->where('is_mandatory', true);
        }

        return view('livewire.courses.course-index', [
            'courses' => $query->with('category')->get(),
            'categories' => Category::whereNull('company_id')->orWhere('company_id', $user->company_id)->get(),
        ]);
    }
}
