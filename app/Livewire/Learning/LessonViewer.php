<?php

namespace App\Livewire\Learning;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\GamificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class LessonViewer extends Component
{
    public int $id;
    public bool $completed = false;

    public function mount(int $id): void
    {
        $this->id = $id;

        // Verifica se já foi concluída anteriormente
        $this->completed = LessonProgress::where('user_id', auth()->id())
            ->where('lesson_id', $id)
            ->whereNotNull('completed_at')
            ->exists();
    }

    public function completeLesson(): void
    {
        if ($this->completed) {
            return;
        }

        $lesson = Lesson::findOrFail($this->id);
        $user   = auth()->user();

        // Registra progresso
        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['status' => 'completed', 'completed_at' => now()]
        );

        $this->completed = true;

        // Gamificação
        $result = app(GamificationService::class)->onLessonComplete($user, $lesson);

        $this->dispatch('xp-earned', amount: $result['xp']);

        if ($result['leveled_up']) {
            $this->dispatch('level-up');
        }

        if ($result['badges']->isNotEmpty()) {
            $this->dispatch('badge-earned', badge: $result['badges']->first()->name);
        }
    }

    public function render()
    {
        $lesson = Lesson::with(['contents', 'module.course'])->findOrFail($this->id);
        return view('livewire.learning.lesson-viewer', ['lesson' => $lesson]);
    }
}
