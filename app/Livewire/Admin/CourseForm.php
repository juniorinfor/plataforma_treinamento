<?php

namespace App\Livewire\Admin;

use App\Enums\CourseDifficulty;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Curso')]
class CourseForm extends Component
{
    use WithFileUploads;

    public ?int $courseId = null;

    public string $title = '';
    public string $short_description = '';
    public string $description = '';
    public ?int $category_id = null;
    public string $difficulty = 'beginner';
    public float $estimated_hours = 1;
    public int $xp_reward = 100;
    public bool $is_mandatory = false;
    public bool $is_published = false;

    /** Novo upload (temporário) */
    public $thumbnail = null;
    /** Caminho já salvo (edição) */
    public ?string $existingThumbnail = null;

    public function mount(?Course $course = null): void
    {
        $user = auth()->user();
        if ($course && $course->exists) {
            // Gestor só edita cursos da própria empresa; platform_admin edita qualquer um
            if (!$user->isPlatformAdmin()) {
                abort_unless(!$course->is_platform_course && $course->company_id === $user->company_id, 403);
            }
        } else {
            // Criação: gestor pode criar cursos da sua empresa
            abort_unless($user->isPlatformAdmin() || $user->isGestor(), 403);
        }

        if ($course && $course->exists) {
            $this->courseId          = $course->id;
            $this->title             = $course->title;
            $this->short_description = $course->short_description ?? '';
            $this->description       = $course->description ?? '';
            $this->category_id       = $course->category_id;
            $this->difficulty        = $course->difficulty->value;
            $this->estimated_hours   = (float) $course->estimated_hours;
            $this->xp_reward         = $course->xp_reward;
            $this->is_mandatory      = $course->is_mandatory;
            $this->is_published      = $course->is_published;
            $this->existingThumbnail = $course->thumbnail_path;
        }
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('name')->get(['id', 'name', 'color']);
    }

    #[Computed]
    public function difficulties(): array
    {
        return array_map(
            fn (CourseDifficulty $d) => ['value' => $d->value, 'label' => $d->label()],
            CourseDifficulty::cases()
        );
    }

    protected function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'min:3', 'max:160'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'category_id'       => ['nullable', 'exists:categories,id'],
            'difficulty'        => ['required', 'in:beginner,intermediate,advanced'],
            'estimated_hours'   => ['required', 'numeric', 'min:0', 'max:999'],
            'xp_reward'         => ['required', 'integer', 'min:0', 'max:100000'],
            'is_mandatory'      => ['boolean'],
            'is_published'      => ['boolean'],
            'thumbnail'         => ['nullable', 'image', 'max:2048'], // até 2MB
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $thumbPath = $this->existingThumbnail;
        if ($this->thumbnail) {
            $thumbPath = $this->thumbnail->store('courses', 'public');
        }

        $payload = [
            'title'             => $data['title'],
            'short_description' => $data['short_description'] ?: null,
            'description'       => $data['description'] ?: null,
            'category_id'       => $data['category_id'] ?: null,
            'difficulty'        => $data['difficulty'],
            'estimated_hours'   => $data['estimated_hours'],
            'xp_reward'         => $data['xp_reward'],
            'is_mandatory'      => $data['is_mandatory'],
            'is_published'      => $data['is_published'],
            'thumbnail_path'    => $thumbPath,
        ];

        $user = auth()->user();
        if ($this->courseId) {
            $course = Course::findOrFail($this->courseId);
            if ($data['is_published'] && !$course->is_published) {
                $payload['published_at'] = now();
            }
            $course->update($payload);
        } else {
            $payload['created_by'] = auth()->id();
            if ($user->isPlatformAdmin()) {
                $payload['company_id']         = null;
                $payload['is_platform_course'] = true;
            } else {
                $payload['company_id']         = $user->company_id;
                $payload['is_platform_course'] = false;
            }
            $payload['slug']         = $this->uniqueSlug($data['title']);
            $payload['published_at'] = $data['is_published'] ? now() : null;
            $course = Course::create($payload);
            $this->courseId = $course->id;
        }

        session()->flash('status', 'Curso salvo com sucesso.');

        // Após salvar, segue para montar o conteúdo (módulos e aulas).
        return $this->redirect(route('admin.courses.builder', $course->id), navigate: true);
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'curso';
        $slug = $base;
        $i = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }

    public function render()
    {
        return view('livewire.admin.course-form');
    }
}
