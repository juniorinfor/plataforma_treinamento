<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Fórum')]
class ForumAdmin extends Component
{
    public ?int $selectedCompany = null;

    // Tabs: 'categories' | 'threads'
    public string $tab = 'categories';

    // Modal de categoria
    public bool   $showCatModal  = false;
    public ?int   $editingCatId  = null;
    public string $catName       = '';
    public string $catDescription= '';
    public string $catColor      = '#6366F1';
    public bool   $catActive     = true;

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
    public function categories()
    {
        $q = ForumCategory::withCount('threads');
        if ($this->cid()) {
            $q->where('company_id', $this->cid());
        }
        return $q->orderBy('sort_order')->get();
    }

    #[Computed]
    public function threads()
    {
        $q = ForumThread::with(['user:id,name', 'category:id,name'])
            ->withCount('posts');
        if ($this->cid()) {
            $q->where('company_id', $this->cid());
        }
        return $q->orderByDesc('last_post_at')->limit(50)->get();
    }

    // ── Categorias ────────────────────────────────────────────────────

    public function openCreateCat(): void
    {
        $this->reset('editingCatId', 'catName', 'catDescription');
        $this->catColor  = '#6366F1';
        $this->catActive = true;
        $this->showCatModal = true;
    }

    public function openEditCat(int $id): void
    {
        $cat = ForumCategory::findOrFail($id);
        $this->editingCatId   = $id;
        $this->catName        = $cat->name;
        $this->catDescription = $cat->description ?? '';
        $this->catColor       = $cat->color;
        $this->catActive      = $cat->is_active;
        $this->showCatModal   = true;
    }

    public function saveCategory(): void
    {
        $this->validate([
            'catName'        => 'required|string|max:80',
            'catDescription' => 'nullable|string|max:300',
            'catColor'       => 'required|string|max:20',
        ]);

        $cid = $this->cid() ?? auth()->user()->company_id;

        ForumCategory::updateOrCreate(['id' => $this->editingCatId], [
            'company_id'  => $cid,
            'name'        => $this->catName,
            'description' => $this->catDescription ?: null,
            'color'       => $this->catColor,
            'is_active'   => $this->catActive,
        ]);

        unset($this->categories);
        $this->showCatModal = false;
    }

    public function deleteCategory(int $id): void
    {
        ForumCategory::destroy($id);
        unset($this->categories);
    }

    // ── Threads (moderação) ───────────────────────────────────────────

    public function togglePin(int $id): void
    {
        $thread = ForumThread::findOrFail($id);
        $thread->update(['is_pinned' => !$thread->is_pinned]);
        unset($this->threads);
    }

    public function toggleLock(int $id): void
    {
        $thread = ForumThread::findOrFail($id);
        $thread->update(['is_locked' => !$thread->is_locked]);
        unset($this->threads);
    }

    public function deleteThread(int $id): void
    {
        ForumThread::destroy($id);
        unset($this->threads);
    }

    public function render()
    {
        return view('livewire.admin.forum-admin');
    }
}
