<?php
namespace App\Livewire\Forum;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Fórum')]
class ForumIndex extends Component
{
    public function render()
    {
        return view('livewire.forum.forum-index');
    }
}
