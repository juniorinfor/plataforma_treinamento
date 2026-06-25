<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Editor de conteúdo')]
class LessonEditor extends Component
{
    use WithFileUploads;

    public int $courseId;
    public int $lessonId;

    // Painel de adição de novo bloco
    public ?string $adding = null; // 'text' | 'video' | 'pdf'
    public string $newText = '';
    public string $newVideoUrl = '';
    public $newPdf = null;

    // Edição inline
    public ?int $editingBlockId = null;
    public string $editContent = '';
    public string $editVideoUrl = '';

    public function mount(Course $course, Lesson $lesson): void
    {
        abort_unless(auth()->user()->isPlatformAdmin(), 403);
        abort_unless($lesson->module->course_id === $course->id, 404);
        $this->courseId = $course->id;
        $this->lessonId = $lesson->id;
    }

    #[Computed]
    public function lesson(): Lesson
    {
        return Lesson::with('module.course')->findOrFail($this->lessonId);
    }

    #[Computed]
    public function blocks(): Collection
    {
        return LessonContent::where('lesson_id', $this->lessonId)
            ->orderBy('sort_order')
            ->get();
    }

    // ── Adição de blocos ──────────────────────────────────────────────

    public function startAdding(string $type): void
    {
        $this->adding = $type;
        $this->newText = '';
        $this->newVideoUrl = '';
        $this->newPdf = null;
        $this->editingBlockId = null;
        $this->resetValidation();
    }

    public function cancelAdding(): void
    {
        $this->adding = null;
        $this->newPdf = null;
        $this->resetValidation();
    }

    public function addText(): void
    {
        $this->validate(['newText' => ['required', 'string', 'min:1']]);
        $this->createBlock('text', $this->newText);
        $this->adding = null;
        $this->newText = '';
    }

    public function addVideo(): void
    {
        $this->validate(['newVideoUrl' => ['required', 'url']]);
        $embed = $this->toEmbedUrl($this->newVideoUrl);
        if (!$embed) {
            $this->addError('newVideoUrl', 'URL inválida. Use links do YouTube ou Vimeo.');
            return;
        }
        LessonContent::create([
            'lesson_id'  => $this->lessonId,
            'type'       => 'video',
            'content'    => $this->newVideoUrl,
            'sort_order' => $this->nextSortOrder(),
            'settings'   => [
                'embed_url' => $embed,
                'provider'  => $this->detectProvider($this->newVideoUrl),
            ],
        ]);
        unset($this->blocks);
        $this->adding = null;
        $this->newVideoUrl = '';
    }

    public function addPdf(): void
    {
        $this->validate(['newPdf' => ['required', 'file', 'mimes:pdf', 'max:20480']]);
        $path = $this->newPdf->store("lessons/{$this->lessonId}", 'public');
        LessonContent::create([
            'lesson_id'  => $this->lessonId,
            'type'       => 'pdf',
            'content'    => $path,
            'sort_order' => $this->nextSortOrder(),
            'settings'   => [
                'filename' => $this->newPdf->getClientOriginalName(),
                'size'     => $this->newPdf->getSize(),
            ],
        ]);
        unset($this->blocks);
        $this->adding = null;
        $this->newPdf = null;
    }

    // ── Edição inline ─────────────────────────────────────────────────

    public function startEdit(int $id): void
    {
        $block = LessonContent::find($id);
        if (!$block) return;
        $this->editingBlockId = $id;
        $this->adding = null;
        $this->resetValidation();
        if ($block->type === 'video') {
            $this->editVideoUrl = $block->content;
        } else {
            $this->editContent = $block->content;
        }
    }

    public function saveEdit(): void
    {
        $block = LessonContent::find($this->editingBlockId);
        if (!$block) { $this->cancelEdit(); return; }

        if ($block->type === 'video') {
            $this->validate(['editVideoUrl' => ['required', 'url']]);
            $embed = $this->toEmbedUrl($this->editVideoUrl);
            if (!$embed) {
                $this->addError('editVideoUrl', 'URL inválida. Use links do YouTube ou Vimeo.');
                return;
            }
            $block->update([
                'content'  => $this->editVideoUrl,
                'settings' => [
                    'embed_url' => $embed,
                    'provider'  => $this->detectProvider($this->editVideoUrl),
                ],
            ]);
        } else {
            $this->validate(['editContent' => ['required', 'string', 'min:1']]);
            $block->update(['content' => $this->editContent]);
        }

        unset($this->blocks);
        $this->editingBlockId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingBlockId = null;
        $this->resetValidation();
    }

    // ── Operações sobre blocos ────────────────────────────────────────

    public function deleteBlock(int $id): void
    {
        $block = LessonContent::find($id);
        if (!$block) return;
        if ($block->type === 'pdf' && $block->content) {
            Storage::disk('public')->delete($block->content);
        }
        $block->delete();
        unset($this->blocks);
    }

    public function moveBlock(int $id, string $dir): void
    {
        $items = $this->blocks->all();
        $idx = null;
        foreach ($items as $i => $item) {
            if ($item->id === $id) { $idx = $i; break; }
        }
        if ($idx === null) return;
        $swap = $dir === 'up' ? $idx - 1 : $idx + 1;
        if ($swap < 0 || $swap >= count($items)) return;
        $a = $items[$idx];
        $b = $items[$swap];
        $tmp = $a->sort_order;
        $a->update(['sort_order' => $b->sort_order]);
        $b->update(['sort_order' => $tmp]);
        unset($this->blocks);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function createBlock(string $type, string $content): void
    {
        LessonContent::create([
            'lesson_id'  => $this->lessonId,
            'type'       => $type,
            'content'    => $content,
            'sort_order' => $this->nextSortOrder(),
        ]);
        unset($this->blocks);
    }

    private function nextSortOrder(): int
    {
        return (int) LessonContent::where('lesson_id', $this->lessonId)->max('sort_order') + 1;
    }

    private function toEmbedUrl(string $url): ?string
    {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}";
        }
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }
        return null;
    }

    private function detectProvider(string $url): string
    {
        if (str_contains($url, 'youtube') || str_contains($url, 'youtu.be')) return 'youtube';
        if (str_contains($url, 'vimeo')) return 'vimeo';
        return 'other';
    }

    public function render()
    {
        return view('livewire.admin.lesson-editor');
    }
}
