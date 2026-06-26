<?php

namespace App\Livewire\Admin;

use App\Enums\BadgeRarity;
use App\Models\Badge;
use App\Models\Company;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Conquistas')]
class BadgeAdmin extends Component
{
    public ?int $selectedCompany = null;

    public bool   $showModal    = false;
    public ?int   $editingId    = null;
    public string $name         = '';
    public string $description  = '';
    public string $color        = '#6366F1';
    public string $rarity       = 'common';
    public int    $xp_reward    = 50;
    public bool   $is_active    = true;

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
    public function badges()
    {
        $q = Badge::query();
        if ($this->cid()) {
            $q->where('company_id', $this->cid());
        }
        return $q->orderBy('name')->get();
    }

    #[Computed]
    public function rarities(): array
    {
        return array_map(fn ($r) => ['value' => $r->value, 'label' => $r->label(), 'color' => $r->color()], BadgeRarity::cases());
    }

    public function openCreate(): void
    {
        $this->reset('editingId', 'name', 'description');
        $this->color     = '#6366F1';
        $this->rarity    = 'common';
        $this->xp_reward = 50;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $badge = Badge::findOrFail($id);
        $this->editingId   = $id;
        $this->name        = $badge->name;
        $this->description = $badge->description ?? '';
        $this->color       = $badge->color ?? '#6366F1';
        $this->rarity      = $badge->rarity->value;
        $this->xp_reward   = $badge->xp_reward;
        $this->is_active   = $badge->is_active;
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'        => 'required|string|max:80',
            'description' => 'nullable|string|max:300',
            'color'       => 'required|string|max:20',
            'rarity'      => 'required|in:common,uncommon,rare,epic,legendary',
            'xp_reward'   => 'required|integer|min:0',
        ]);

        $cid = $this->cid() ?? auth()->user()->company_id;

        Badge::updateOrCreate(['id' => $this->editingId], [
            'company_id'  => $cid,
            'name'        => $this->name,
            'slug'        => Str::slug($this->name),
            'description' => $this->description ?: null,
            'color'       => $this->color,
            'rarity'      => $this->rarity,
            'xp_reward'   => $this->xp_reward,
            'is_active'   => $this->is_active,
        ]);

        unset($this->badges);
        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        $badge = Badge::findOrFail($id);
        $badge->update(['is_active' => !$badge->is_active]);
        unset($this->badges);
    }

    public function delete(int $id): void
    {
        Badge::destroy($id);
        unset($this->badges);
    }

    public function render()
    {
        return view('livewire.admin.badge-admin');
    }
}
