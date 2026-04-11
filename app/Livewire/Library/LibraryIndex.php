<?php
namespace App\Livewire\Library;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Biblioteca')]
class LibraryIndex extends Component
{
    public function render()
    {
        return view('livewire.library.library-index');
    }
}
