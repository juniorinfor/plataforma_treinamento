<?php

namespace App\Livewire\Learning;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\LessonInteraction;
use App\Models\LessonProgress;
use App\Models\PointTransaction;
use App\Models\UserPoints;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Aula')]
class LessonViewer extends Component
{
    public int  $id;
    public bool $completed = false;

    public array $interactionAnswers = [];
    public array $interactionSaved = [];

    public function mount(int $id): void
    {
        $this->id = $id;
        Lesson::findOrFail($id); // 404 se não existe

        $this->completed = LessonProgress::where('user_id', auth()->id())
            ->where('lesson_id', $id)
            ->whereNotNull('completed_at')
            ->exists();

        $contentIds = LessonContent::where('lesson_id', $id)
            ->whereIn('type', ['reflection', 'scale'])
            ->pluck('id');

        LessonInteraction::where('user_id', auth()->id())
            ->whereIn('lesson_content_id', $contentIds)
            ->get()
            ->each(function (LessonInteraction $interaction) {
                $this->interactionAnswers[$interaction->lesson_content_id] = $interaction->response['value'] ?? null;
                $this->interactionSaved[$interaction->lesson_content_id] = true;
            });
    }

    public function selectScale(int $contentId, int $value): void
    {
        $this->interactionAnswers[$contentId] = $value;
        $this->saveInteraction($contentId);
    }

    public function saveInteraction(int $contentId): void
    {
        $value = $this->interactionAnswers[$contentId] ?? null;
        if ($value === null || $value === '') return;

        LessonInteraction::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_content_id' => $contentId],
            ['response' => ['value' => $value]]
        );

        $this->interactionSaved[$contentId] = true;
    }

    public function completeLesson(): void
    {
        if ($this->completed) return;

        $lesson = Lesson::with('module.course')->findOrFail($this->id);
        $user   = auth()->user();

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['status' => 'completed', 'completed_at' => now()]
        );

        $this->completed = true;

        if ($lesson->xp_reward > 0) {
            $userPoints = UserPoints::where('user_id', $user->id)
                ->where('company_id', $user->company_id)
                ->first();

            if ($userPoints) {
                $userPoints->increment('total_xp', $lesson->xp_reward);
                $userPoints->increment('weekly_xp', $lesson->xp_reward);
                $userPoints->increment('monthly_xp', $lesson->xp_reward);

                PointTransaction::create([
                    'user_id'        => $user->id,
                    'company_id'     => $user->company_id,
                    'xp_amount'      => $lesson->xp_reward,
                    'type'           => 'lesson_complete',
                    'description'    => "Aula concluída: {$lesson->title}",
                    'reference_type' => Lesson::class,
                    'reference_id'   => $lesson->id,
                    'created_at'     => now(),
                ]);
            }
        }

        $this->recalculateProgress($user->id, $lesson->module->course_id);
    }

    private function recalculateProgress(int $userId, int $courseId): void
    {
        $ids   = Lesson::join('modules', 'lessons.module_id', '=', 'modules.id')
            ->where('modules.course_id', $courseId)
            ->pluck('lessons.id');
        $total = $ids->count();
        $done  = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $ids)->whereNotNull('completed_at')->count();

        if ($total > 0) {
            Enrollment::where('user_id', $userId)->where('course_id', $courseId)->update([
                'progress_percentage' => round($done / $total * 100, 2),
                'status'              => $done >= $total ? 'completed' : 'active',
                'completed_at'        => $done >= $total ? now() : null,
            ]);
        }
    }

    public function render()
    {
        $lesson = Lesson::with(['contents', 'module.course'])->findOrFail($this->id);
        [$prev, $next] = $this->adjacentLessons($lesson);

        return view('livewire.learning.lesson-viewer', compact('lesson', 'prev', 'next'));
    }

    private function adjacentLessons(Lesson $lesson): array
    {
        $all = Lesson::join('modules', 'lessons.module_id', '=', 'modules.id')
            ->where('modules.course_id', $lesson->module->course_id)
            ->orderBy('modules.sort_order')
            ->orderBy('lessons.sort_order')
            ->select('lessons.id', 'lessons.type')
            ->get();

        $idx = $all->search(fn ($l) => $l->id === $lesson->id);

        return [
            $idx !== false && $idx > 0                   ? $all->get($idx - 1) : null,
            $idx !== false && $idx < $all->count() - 1   ? $all->get($idx + 1) : null,
        ];
    }
}
