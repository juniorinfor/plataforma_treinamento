<?php

namespace App\Livewire\Library;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Biblioteca')]
class LibraryIndex extends Component
{
    public string $search     = '';
    public string $activeType = 'todos';

    // ── Helpers ───────────────────────────────────────────────────────

    protected function cid(): ?int
    {
        return auth()->user()->company_id;
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function documents()
    {
        if (!$this->cid()) return collect();

        $q = Document::where('company_id', $this->cid())
            ->where('is_published', true);

        if ($this->activeType !== 'todos') {
            $q->where('file_type', $this->activeType);
        }

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $q->where(fn ($s) => $s->where('title', 'like', $term)
                                   ->orWhere('description', 'like', $term));
        }

        return $q->orderByDesc('created_at')->get();
    }

    #[Computed]
    public function recentDocuments()
    {
        if (!$this->cid()) return collect();
        return Document::where('company_id', $this->cid())
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function availableTypes(): array
    {
        if (!$this->cid()) return [];
        return Document::where('company_id', $this->cid())
            ->where('is_published', true)
            ->distinct()
            ->pluck('file_type')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    // ── Actions ───────────────────────────────────────────────────────

    public function download(int $id): mixed
    {
        $doc = Document::where('company_id', $this->cid())
            ->where('is_published', true)
            ->findOrFail($id);

        abort_unless(Storage::exists($doc->file_path), 404);

        $ext      = pathinfo($doc->file_path, PATHINFO_EXTENSION);
        $filename = $doc->title . ($ext ? '.' . $ext : '');

        return Storage::download($doc->file_path, $filename);
    }

    public function render()
    {
        return view('livewire.library.library-index');
    }
}
