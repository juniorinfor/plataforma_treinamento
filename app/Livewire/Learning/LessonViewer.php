<?php

namespace App\Livewire\Learning;

use App\Models\Lesson;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class LessonViewer extends Component
{
    public int $id;

    public function mount(int $id): void
    {
        $this->id = $id;
    }

    public function render()
    {
        $lesson = Lesson::with(['contents', 'module.course'])->findOrFail($this->id);
        return view('livewire.learning.lesson-viewer', ['lesson' => $lesson]);
    }
}
