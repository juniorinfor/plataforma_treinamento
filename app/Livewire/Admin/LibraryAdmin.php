<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Biblioteca')]
class LibraryAdmin extends Component
{
    use WithFileUploads, WithPagination;

    public ?int $selectedCompany = null;

    public bool   $showForm     = false;
    public ?int   $editingId    = null;
    public string $title        = '';
    public string $description  = '';
    public bool   $is_published = true;
    public $file                = null;
    public ?string $existingPath = null;

    public function mount(): void
    {
        if (!auth()->user()->isPlatformAdmin()) {
            $this->selectedCompany = auth()->user()->company_id;
        }
    }

    protected function cid(): ?int { return $this->selectedCompany; }

    #[Computed]
    public function companies()
    {
        return auth()->user()->isPlatformAdmin()
            ? Company::orderBy('name')->get(['id', 'name'])
            : collect();
    }

    #[Computed]
    public function documents()
    {
        if (!$this->cid()) {
            return Document::with('uploader:id,name')->orderByDesc('created_at')->paginate(15);
        }
        return Document::where('company_id', $this->cid())
            ->with('uploader:id,name')
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function openCreate(): void
    {
        $this->reset('editingId', 'title', 'description', 'file', 'existingPath');
        $this->is_published = true;
        $this->showForm     = true;
    }

    public function openEdit(int $id): void
    {
        $doc = Document::findOrFail($id);
        $this->editingId    = $id;
        $this->title        = $doc->title;
        $this->description  = $doc->description ?? '';
        $this->is_published = $doc->is_published;
        $this->existingPath = $doc->file_path;
        $this->file         = null;
        $this->showForm     = true;
    }

    public function save(): void
    {
        $this->validate([
            'title'        => 'required|string|max:160',
            'description'  => 'nullable|string|max:500',
            'file'         => $this->editingId ? 'nullable|file|max:20480' : 'required|file|max:20480',
            'is_published' => 'boolean',
        ]);

        $filePath = $this->existingPath;
        $fileType = null;
        $fileSize = null;

        if ($this->file) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $this->file->store('library', 'public');
            $fileType = $this->file->getMimeType();
            $fileSize = (int) round($this->file->getSize() / 1024);
        }

        $cid = $this->cid() ?? auth()->user()->company_id;

        $data = [
            'company_id'   => $cid,
            'title'        => $this->title,
            'description'  => $this->description ?: null,
            'is_published' => $this->is_published,
            'uploaded_by'  => auth()->id(),
        ];

        if ($filePath) {
            $data['file_path']    = $filePath;
            $data['file_type']    = $fileType;
            $data['file_size_kb'] = $fileSize;
        }

        Document::updateOrCreate(['id' => $this->editingId], $data);

        unset($this->documents);
        $this->showForm = false;
    }

    public function togglePublish(int $id): void
    {
        $doc = Document::findOrFail($id);
        $doc->update(['is_published' => !$doc->is_published]);
        unset($this->documents);
    }

    public function delete(int $id): void
    {
        $doc = Document::findOrFail($id);
        if ($doc->file_path) {
            Storage::disk('public')->delete($doc->file_path);
        }
        $doc->delete();
        unset($this->documents);
    }

    public function render()
    {
        return view('livewire.admin.library-admin');
    }
}
