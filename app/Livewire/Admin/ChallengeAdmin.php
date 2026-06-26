<?php

namespace App\Livewire\Admin;

use App\Enums\ChallengeType;
use App\Models\Badge;
use App\Models\Challenge;
use App\Models\Company;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Desafios')]
class ChallengeAdmin extends Component
{
    public ?int $selectedCompany = null;

    public bool    $showModal   = false;
    public ?int    $editingId   = null;
    public string  $title       = '';
    public string  $description = '';
    public string  $type        = 'weekly';
    public int     $xp_reward   = 100;
    public ?int    $badge_id    = null;
    public string  $starts_at   = '';
    public string  $ends_at     = '';
    public bool    $is_active   = true;

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
    public function challenges()
    {
        $q = Challenge::with('badge:id,name,color');
        if ($this->cid()) {
            $q->where('company_id', $this->cid());
        }
        return $q->orderByDesc('starts_at')->get();
    }

    #[Computed]
    public function availableBadges()
    {
        $q = Badge::where('is_active', true);
        if ($this->cid()) {
            $q->where('company_id', $this->cid());
        }
        return $q->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function types(): array
    {
        return array_map(fn ($t) => ['value' => $t->value, 'label' => $t->label()], ChallengeType::cases());
    }

    public function openCreate(): void
    {
        $this->reset('editingId', 'title', 'description', 'badge_id', 'starts_at', 'ends_at');
        $this->type      = 'weekly';
        $this->xp_reward = 100;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $ch = Challenge::findOrFail($id);
        $this->editingId   = $id;
        $this->title       = $ch->title;
        $this->description = $ch->description ?? '';
        $this->type        = $ch->type->value;
        $this->xp_reward   = $ch->xp_reward;
        $this->badge_id    = $ch->badge_id;
        $this->starts_at   = $ch->starts_at?->format('Y-m-d') ?? '';
        $this->ends_at     = $ch->ends_at?->format('Y-m-d') ?? '';
        $this->is_active   = $ch->is_active;
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->validate([
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:400',
            'type'        => 'required|in:daily,weekly,monthly,special',
            'xp_reward'   => 'required|integer|min:0',
            'badge_id'    => 'nullable|exists:badges,id',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
        ]);

        $cid = $this->cid() ?? auth()->user()->company_id;

        Challenge::updateOrCreate(['id' => $this->editingId], [
            'company_id'  => $cid,
            'title'       => $this->title,
            'description' => $this->description ?: null,
            'type'        => $this->type,
            'xp_reward'   => $this->xp_reward,
            'badge_id'    => $this->badge_id ?: null,
            'starts_at'   => $this->starts_at ?: null,
            'ends_at'     => $this->ends_at ?: null,
            'is_active'   => $this->is_active,
        ]);

        unset($this->challenges);
        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        $ch = Challenge::findOrFail($id);
        $ch->update(['is_active' => !$ch->is_active]);
        unset($this->challenges);
    }

    public function delete(int $id): void
    {
        Challenge::destroy($id);
        unset($this->challenges);
    }

    public function render()
    {
        return view('livewire.admin.challenge-admin');
    }
}
