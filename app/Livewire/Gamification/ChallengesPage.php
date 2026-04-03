<?php

namespace App\Livewire\Gamification;

use App\Models\Challenge;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ChallengesPage extends Component
{
    public function render()
    {
        return view('livewire.gamification.challenges-page');
    }
}
