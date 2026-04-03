<?php

namespace App\Livewire\Saas;

use App\Models\Plan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PlansPage extends Component
{
    public function render()
    {
        return view('livewire.saas.plans-page', [
            'plans' => Plan::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
