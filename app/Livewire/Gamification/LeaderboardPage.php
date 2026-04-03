<?php

namespace App\Livewire\Gamification;

use App\Models\UserPoints;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class LeaderboardPage extends Component
{
    public string $period = 'weekly';

    public function render()
    {
        $user = auth()->user();
        $ranking = UserPoints::where('company_id', $user->company_id)
            ->with('user')
            ->orderByDesc($this->period === 'weekly' ? 'weekly_xp' : ($this->period === 'monthly' ? 'monthly_xp' : 'total_xp'))
            ->get();

        return view('livewire.gamification.leaderboard-page', [
            'ranking' => $ranking,
        ]);
    }
}
