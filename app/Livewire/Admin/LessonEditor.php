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

    public const CALLOUT_STYLES = ['info', 'tip', 'warning', 'success'];

    // Painel de adição de novo bloco
    public ?string $adding = null;

    // Blocos legados (texto simples / vídeo / pdf) — inalterados
    public string $newText = '';
    public string $newVideoUrl = '';
    public $newPdf = null;

    // Buffer compartilhado — usado tanto para adicionar quanto editar os novos tipos de bloco
    public string $bufContent  = '';
    public string $bufTitle    = '';
    public string $bufStyle    = 'info';
    public string $bufAuthor   = '';
    public string $bufMinLabel = '';
    public string $bufMaxLabel = '';
    public $bufImage           = null;
    public string $bufCaption  = '';
    public array $bufColumns   = [];
    public array $bufCards     = [];
    public array $bufSections  = [];

    // Edição inline
    public ?int $editingBlockId = null;
    public string $editContent = '';
    public string $editVideoUrl = '';

    public function mount(Course $course, Lesson $lesson): void
    {
        $user = auth()->user();
        if (!$user->isPlatformAdmin()) {
            abort_unless(!$course->is_platform_course && $course->company_id === $user->company_id, 403);
        }
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

    // ── Início de adição / edição ────────────────────────────────────────

    public function startAdding(string $type): void
    {
        $this->adding = $type;
        $this->newText = '';
        $this->newVideoUrl = '';
        $this->newPdf = null;
        $this->editingBlockId = null;
        $this->resetValidation();
        $this->resetBuffer($type);
    }

    public function cancelAdding(): void
    {
        $this->adding = null;
        $this->newPdf = null;
        $this->bufImage = null;
        $this->resetValidation();
    }

    private function resetBuffer(string $type): void
    {
        $this->bufContent  = '';
        $this->bufTitle    = '';
        $this->bufStyle    = 'info';
        $this->bufAuthor   = '';
        $this->bufMinLabel = '';
        $this->bufMaxLabel = '';
        $this->bufImage    = null;
        $this->bufCaption  = '';
        $this->bufColumns  = match ($type) {
            'comparison' => [
                ['title' => '', 'color' => '#6366f1', 'itemsText' => ''],
                ['title' => '', 'color' => '#f43f5e', 'itemsText' => ''],
            ],
            default => [],
        };
        $this->bufCards    = $type === 'flashcards' ? [['front' => '', 'back' => '']] : [];
        $this->bufSections = $type === 'accordion' ? [['title' => '', 'body' => '']] : [];
    }

    // ── Adição de blocos legados (texto / vídeo / pdf) ───────────────────

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

    // ── Adição dos novos tipos de bloco ───────────────────────────────────

    public function addRich(): void
    {
        $this->validate(['bufContent' => ['required', 'string', 'min:1']], [], ['bufContent' => 'Conteúdo']);
        $this->createBlockWithSettings('rich', $this->bufContent, null);
        $this->finishAdding();
    }

    public function addImage(): void
    {
        $this->validate([
            'bufImage'   => ['required', 'image', 'max:5120'],
            'bufCaption' => ['nullable', 'string', 'max:255'],
        ]);
        $path = $this->bufImage->store("lessons/{$this->lessonId}", 'public');
        $this->createBlockWithSettings('image', $path, ['caption' => $this->bufCaption]);
        $this->finishAdding();
    }

    public function addCallout(): void
    {
        $this->validate([
            'bufContent' => ['required', 'string', 'min:1'],
            'bufStyle'   => ['required', 'in:' . implode(',', self::CALLOUT_STYLES)],
            'bufTitle'   => ['nullable', 'string', 'max:255'],
        ]);
        $this->createBlockWithSettings('callout', $this->bufContent, [
            'style' => $this->bufStyle,
            'title' => $this->bufTitle,
        ]);
        $this->finishAdding();
    }

    public function addQuote(): void
    {
        $this->validate([
            'bufContent' => ['required', 'string', 'min:1'],
            'bufAuthor'  => ['nullable', 'string', 'max:255'],
        ]);
        $this->createBlockWithSettings('quote', $this->bufContent, ['author' => $this->bufAuthor]);
        $this->finishAdding();
    }

    public function addComparison(): void
    {
        if (!$this->validateColumns()) return;

        $this->createBlockWithSettings('comparison', $this->bufContent, [
            'columns' => $this->columnsToArray(),
        ]);
        $this->finishAdding();
    }

    public function addFlashcards(): void
    {
        if (!$this->validateCards()) return;

        $this->createBlockWithSettings('flashcards', $this->bufContent, [
            'cards' => $this->bufCards,
        ]);
        $this->finishAdding();
    }

    public function addScale(): void
    {
        $this->validate([
            'bufContent'  => ['required', 'string', 'min:1'],
            'bufMinLabel' => ['nullable', 'string', 'max:100'],
            'bufMaxLabel' => ['nullable', 'string', 'max:100'],
        ]);
        $this->createBlockWithSettings('scale', $this->bufContent, [
            'minLabel' => $this->bufMinLabel,
            'maxLabel' => $this->bufMaxLabel,
        ]);
        $this->finishAdding();
    }

    public function addReflection(): void
    {
        $this->validate(['bufContent' => ['required', 'string', 'min:1']]);
        $this->createBlockWithSettings('reflection', $this->bufContent, null);
        $this->finishAdding();
    }

    public function addAccordion(): void
    {
        if (!$this->validateSections()) return;

        $this->createBlockWithSettings('accordion', $this->bufContent, [
            'items' => $this->bufSections,
        ]);
        $this->finishAdding();
    }

    public function addVideoPlaceholder(): void
    {
        $this->validate(['bufContent' => ['required', 'string', 'min:1']]);
        $this->createBlockWithSettings('video_placeholder', $this->bufContent, null);
        $this->finishAdding();
    }

    private function finishAdding(): void
    {
        $this->adding = null;
    }

    // ── Edição inline ─────────────────────────────────────────────────

    public function startEdit(int $id): void
    {
        $block = LessonContent::find($id);
        if (!$block) return;

        $this->editingBlockId = $id;
        $this->adding = null;
        $this->resetValidation();
        $settings = $block->settings ?? [];

        if ($block->type === 'video') {
            $this->editVideoUrl = $block->content;
            return;
        }
        if ($block->type === 'text') {
            $this->editContent = $block->content;
            return;
        }

        $this->bufContent  = $block->content ?? '';
        $this->bufTitle    = $settings['title'] ?? '';
        $this->bufStyle    = $settings['style'] ?? 'info';
        $this->bufAuthor   = $settings['author'] ?? '';
        $this->bufMinLabel = $settings['minLabel'] ?? '';
        $this->bufMaxLabel = $settings['maxLabel'] ?? '';
        $this->bufCaption  = $settings['caption'] ?? '';

        if ($block->type === 'comparison') {
            $cols = $settings['columns'] ?? [];
            $this->bufColumns = $cols ? array_map(fn ($c) => [
                'title'     => $c['title'] ?? '',
                'color'     => $c['color'] ?? '#6366f1',
                'itemsText' => implode("\n", $c['items'] ?? []),
            ], $cols) : [['title' => '', 'color' => '#6366f1', 'itemsText' => '']];
        }

        if ($block->type === 'flashcards') {
            $this->bufCards = $settings['cards'] ?: [['front' => '', 'back' => '']];
        }

        if ($block->type === 'accordion') {
            $this->bufSections = $settings['items'] ?: [['title' => '', 'body' => '']];
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
        } elseif ($block->type === 'text') {
            $this->validate(['editContent' => ['required', 'string', 'min:1']]);
            $block->update(['content' => $this->editContent]);
        } elseif ($block->type === 'image') {
            $this->validate(['bufCaption' => ['nullable', 'string', 'max:255']]);
            $block->update(['settings' => array_merge($block->settings ?? [], ['caption' => $this->bufCaption])]);
        } elseif ($block->type === 'rich') {
            $this->validate(['bufContent' => ['required', 'string', 'min:1']]);
            $block->update(['content' => $this->bufContent]);
        } elseif ($block->type === 'callout') {
            $this->validate([
                'bufContent' => ['required', 'string', 'min:1'],
                'bufStyle'   => ['required', 'in:' . implode(',', self::CALLOUT_STYLES)],
                'bufTitle'   => ['nullable', 'string', 'max:255'],
            ]);
            $block->update(['content' => $this->bufContent, 'settings' => ['style' => $this->bufStyle, 'title' => $this->bufTitle]]);
        } elseif ($block->type === 'quote') {
            $this->validate([
                'bufContent' => ['required', 'string', 'min:1'],
                'bufAuthor'  => ['nullable', 'string', 'max:255'],
            ]);
            $block->update(['content' => $this->bufContent, 'settings' => ['author' => $this->bufAuthor]]);
        } elseif ($block->type === 'comparison') {
            if (!$this->validateColumns()) return;
            $block->update(['content' => $this->bufContent, 'settings' => ['columns' => $this->columnsToArray()]]);
        } elseif ($block->type === 'flashcards') {
            if (!$this->validateCards()) return;
            $block->update(['settings' => ['cards' => $this->bufCards]]);
        } elseif ($block->type === 'scale') {
            $this->validate([
                'bufContent'  => ['required', 'string', 'min:1'],
                'bufMinLabel' => ['nullable', 'string', 'max:100'],
                'bufMaxLabel' => ['nullable', 'string', 'max:100'],
            ]);
            $block->update(['content' => $this->bufContent, 'settings' => ['minLabel' => $this->bufMinLabel, 'maxLabel' => $this->bufMaxLabel]]);
        } elseif ($block->type === 'reflection') {
            $this->validate(['bufContent' => ['required', 'string', 'min:1']]);
            $block->update(['content' => $this->bufContent]);
        } elseif ($block->type === 'accordion') {
            if (!$this->validateSections()) return;
            $block->update(['settings' => ['items' => $this->bufSections]]);
        } elseif ($block->type === 'video_placeholder') {
            $this->validate(['bufContent' => ['required', 'string', 'min:1']]);
            $block->update(['content' => $this->bufContent]);
        }

        unset($this->blocks);
        $this->editingBlockId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingBlockId = null;
        $this->resetValidation();
    }

    // ── Repetidores (colunas / cards / seções) ───────────────────────────

    public function addColumn(): void
    {
        $this->bufColumns[] = ['title' => '', 'color' => '#6366f1', 'itemsText' => ''];
    }

    public function removeColumn(int $i): void
    {
        if (count($this->bufColumns) <= 2) return;
        unset($this->bufColumns[$i]);
        $this->bufColumns = array_values($this->bufColumns);
    }

    public function addCard(): void
    {
        $this->bufCards[] = ['front' => '', 'back' => ''];
    }

    public function removeCard(int $i): void
    {
        if (count($this->bufCards) <= 1) return;
        unset($this->bufCards[$i]);
        $this->bufCards = array_values($this->bufCards);
    }

    public function addSection(): void
    {
        $this->bufSections[] = ['title' => '', 'body' => ''];
    }

    public function removeSection(int $i): void
    {
        if (count($this->bufSections) <= 1) return;
        unset($this->bufSections[$i]);
        $this->bufSections = array_values($this->bufSections);
    }

    // ── Operações sobre blocos ────────────────────────────────────────

    public function deleteBlock(int $id): void
    {
        $block = LessonContent::find($id);
        if (!$block) return;
        if (in_array($block->type, ['pdf', 'image']) && $block->content) {
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

    // ── Validação manual dos repetidores ──────────────────────────────

    private function validateColumns(): bool
    {
        if (count($this->bufColumns) < 2) {
            $this->addError('bufColumns', 'Adicione ao menos 2 colunas.');
            return false;
        }
        foreach ($this->bufColumns as $i => $col) {
            if (trim($col['title'] ?? '') === '' || trim($col['itemsText'] ?? '') === '') {
                $this->addError("bufColumns.$i", 'Preencha o título e ao menos um item em cada coluna.');
                return false;
            }
        }
        return true;
    }

    private function validateCards(): bool
    {
        if (count($this->bufCards) < 1) {
            $this->addError('bufCards', 'Adicione ao menos 1 cartão.');
            return false;
        }
        foreach ($this->bufCards as $i => $card) {
            if (trim($card['front'] ?? '') === '' || trim($card['back'] ?? '') === '') {
                $this->addError("bufCards.$i", 'Preencha a frente e o verso de cada cartão.');
                return false;
            }
        }
        return true;
    }

    private function validateSections(): bool
    {
        if (count($this->bufSections) < 1) {
            $this->addError('bufSections', 'Adicione ao menos 1 seção.');
            return false;
        }
        foreach ($this->bufSections as $i => $sec) {
            if (trim($sec['title'] ?? '') === '' || trim($sec['body'] ?? '') === '') {
                $this->addError("bufSections.$i", 'Preencha o título e o conteúdo de cada seção.');
                return false;
            }
        }
        return true;
    }

    private function columnsToArray(): array
    {
        return array_map(fn ($c) => [
            'title' => $c['title'],
            'color' => $c['color'] ?: '#6366f1',
            'items' => array_values(array_filter(array_map('trim', explode("\n", $c['itemsText'])))),
        ], $this->bufColumns);
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

    private function createBlockWithSettings(string $type, string $content, ?array $settings): void
    {
        LessonContent::create([
            'lesson_id'  => $this->lessonId,
            'type'       => $type,
            'content'    => $content,
            'sort_order' => $this->nextSortOrder(),
            'settings'   => $settings,
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
