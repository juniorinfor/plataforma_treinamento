<?php

namespace App\Livewire\Admin;

use App\Enums\LessonType;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Construtor de estrutura do curso: módulos e aulas (CRUD + reordenação).
 * O conteúdo de cada aula (texto, vídeo, PDF) é editado na etapa seguinte.
 * Exclusivo do Admin do Sistema.
 */
#[Layout('components.layouts.app')]
#[Title('Conteúdo do curso')]
class CourseBuilder extends Component
{
    public int $courseId;

    // Modal de módulo
    public bool $showModuleModal = false;
    public ?int $moduleId = null;
    public string $moduleTitle = '';

    // Modal de aula
    public bool $showLessonModal = false;
    public ?int $lessonModuleId = null;
    public ?int $lessonId = null;
    public string $lessonTitle = '';
    public string $lessonType = 'text';
    public int $lessonDuration = 5;

    public function mount(Course $course): void
    {
        abort_unless(auth()->user()->isPlatformAdmin(), 403);
        $this->courseId = $course->id;
    }

    #[Computed]
    public function course(): Course
    {
        return Course::findOrFail($this->courseId);
    }

    #[Computed]
    public function modules(): Collection
    {
        return Module::where('course_id', $this->courseId)
            ->with(['lessons' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    public function lessonTypes(): array
    {
        return array_map(
            fn (LessonType $t) => ['value' => $t->value, 'label' => $t->label()],
            LessonType::cases()
        );
    }

    // ── Módulos ───────────────────────────────────────────────────────

    public function openModuleModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->moduleId = $id;
        $this->moduleTitle = $id ? Module::find($id)?->title ?? '' : '';
        $this->showModuleModal = true;
    }

    public function saveModule(): void
    {
        $this->validate(['moduleTitle' => ['required', 'string', 'min:2', 'max:160']]);

        if ($this->moduleId) {
            Module::where('id', $this->moduleId)->update(['title' => $this->moduleTitle]);
        } else {
            Module::create([
                'course_id'  => $this->courseId,
                'title'      => $this->moduleTitle,
                'sort_order' => (int) Module::where('course_id', $this->courseId)->max('sort_order') + 1,
                'is_published' => true,
            ]);
        }

        $this->showModuleModal = false;
        unset($this->modules);
    }

    public function deleteModule(int $id): void
    {
        Module::where('id', $id)->where('course_id', $this->courseId)->delete();
        unset($this->modules);
    }

    public function moveModule(int $id, string $dir): void
    {
        $this->swapOrder($this->modules->all(), $id, $dir);
        unset($this->modules);
    }

    // ── Aulas ─────────────────────────────────────────────────────────

    public function openLessonModal(int $moduleId, ?int $lessonId = null): void
    {
        $this->resetValidation();
        $this->lessonModuleId = $moduleId;
        $this->lessonId = $lessonId;

        if ($lessonId && $lesson = Lesson::find($lessonId)) {
            $this->lessonTitle    = $lesson->title;
            $this->lessonType     = $lesson->type->value;
            $this->lessonDuration = $lesson->duration_minutes;
        } else {
            $this->lessonTitle    = '';
            $this->lessonType     = 'text';
            $this->lessonDuration = 5;
        }

        $this->showLessonModal = true;
    }

    public function saveLesson(): void
    {
        $this->validate([
            'lessonTitle'    => ['required', 'string', 'min:2', 'max:160'],
            'lessonType'     => ['required', 'in:text,video,pdf,quiz,interactive'],
            'lessonDuration' => ['required', 'integer', 'min:1', 'max:600'],
        ]);

        if ($this->lessonId) {
            Lesson::where('id', $this->lessonId)->update([
                'title'            => $this->lessonTitle,
                'type'             => $this->lessonType,
                'duration_minutes' => $this->lessonDuration,
            ]);
        } else {
            Lesson::create([
                'module_id'        => $this->lessonModuleId,
                'title'            => $this->lessonTitle,
                'type'             => $this->lessonType,
                'duration_minutes' => $this->lessonDuration,
                'sort_order'       => (int) Lesson::where('module_id', $this->lessonModuleId)->max('sort_order') + 1,
                'is_published'     => true,
            ]);
        }

        $this->showLessonModal = false;
        unset($this->modules);
    }

    public function deleteLesson(int $id): void
    {
        Lesson::where('id', $id)->delete();
        unset($this->modules);
    }

    public function moveLesson(int $moduleId, int $id, string $dir): void
    {
        $module = $this->modules->firstWhere('id', $moduleId);
        if ($module) {
            $this->swapOrder($module->lessons->all(), $id, $dir);
            unset($this->modules);
        }
    }

    // ── Helper de reordenação ─────────────────────────────────────────

    /**
     * Troca o sort_order de um item com o vizinho na direção indicada.
     *
     * @param  array<int, \Illuminate\Database\Eloquent\Model>  $items  já ordenados
     */
    private function swapOrder(array $items, int $id, string $dir): void
    {
        $idx = null;
        foreach ($items as $i => $item) {
            if ($item->id === $id) {
                $idx = $i;
                break;
            }
        }
        if ($idx === null) {
            return;
        }

        $swap = $dir === 'up' ? $idx - 1 : $idx + 1;
        if ($swap < 0 || $swap >= count($items)) {
            return;
        }

        $a = $items[$idx];
        $b = $items[$swap];
        $tmp = $a->sort_order;
        $a->update(['sort_order' => $b->sort_order]);
        $b->update(['sort_order' => $tmp]);
    }

    public function render()
    {
        return view('livewire.admin.course-builder');
    }
}
