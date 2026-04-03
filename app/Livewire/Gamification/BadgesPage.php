<?php

namespace App\Livewire\Gamification;

use App\Models\Badge;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class BadgesPage extends Component
{
    public function render()
    {
        $user = auth()->user();
        $allBadges = Badge::where(function($q) use ($user) {
            $q->whereNull('company_id')->orWhere('company_id', $user->company_id);
        })->where('is_active', true)->get();

        $earnedIds = $user->badges()->pluck('badges.id')->toArray();

        return view('livewire.gamification.badges-page', [
            'badges' => $allBadges,
            'earnedIds' => $earnedIds,
        ]);
    }
}
