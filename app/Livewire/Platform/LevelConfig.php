<?php

namespace App\Livewire\Platform;

use App\Models\Level;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('XP e Níveis')]
class LevelConfig extends Component
{
    // Modal
    public bool    $showModal    = false;
    public ?int    $editingId    = null;
    public int     $levelNumber  = 1;
    public string  $name         = '';
    public int     $min_xp       = 0;
    public int     $max_xp       = 100;
    public string  $color        = '#6366F1';

    #[Computed]
    public function levels()
    {
        return Level::orderBy('level_number')->get();
    }

    public function openCreate(): void
    {
        $this->reset('editingId', 'name', 'color');
        $last = Level::orderByDesc('level_number')->first();
        $this->levelNumber = $last ? $last->level_number + 1 : 1;
        $this->min_xp      = $last ? $last->max_xp + 1 : 0;
        $this->max_xp      = $this->min_xp + 499;
        $this->showModal   = true;
    }

    public function openEdit(int $id): void
    {
        $level = Level::findOrFail($id);
        $this->editingId   = $id;
        $this->levelNumber = $level->level_number;
        $this->name        = $level->name;
        $this->min_xp      = $level->min_xp;
        $this->max_xp      = $level->max_xp;
        $this->color       = $level->color ?? '#6366F1';
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->validate([
            'levelNumber' => 'required|integer|min:1',
            'name'        => 'required|string|max:60',
            'min_xp'      => 'required|integer|min:0',
            'max_xp'      => 'required|integer|gt:min_xp',
            'color'       => 'required|string|max:20',
        ]);

        Level::updateOrCreate(
            ['id' => $this->editingId],
            [
                'level_number' => $this->levelNumber,
                'name'         => $this->name,
                'min_xp'       => $this->min_xp,
                'max_xp'       => $this->max_xp,
                'color'        => $this->color,
            ]
        );

        unset($this->levels);
        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        Level::destroy($id);
        unset($this->levels);
    }

    public function render()
    {
        return view('livewire.platform.level-config');
    }
}
