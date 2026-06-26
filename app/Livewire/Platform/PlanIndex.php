<?php

namespace App\Livewire\Platform;

use App\Models\Plan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Planos')]
class PlanIndex extends Component
{
    #[Computed]
    public function plans()
    {
        return Plan::withCount('products')->orderBy('sort_order')->get();
    }

    public function toggleActive(int $id): void
    {
        $plan = Plan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);
        unset($this->plans);
    }

    public function moveUp(int $id): void
    {
        $plan = Plan::findOrFail($id);
        $prev = Plan::where('sort_order', '<', $plan->sort_order)
            ->orderByDesc('sort_order')->first();
        if ($prev) {
            [$plan->sort_order, $prev->sort_order] = [$prev->sort_order, $plan->sort_order];
            $plan->save();
            $prev->save();
            unset($this->plans);
        }
    }

    public function moveDown(int $id): void
    {
        $plan = Plan::findOrFail($id);
        $next = Plan::where('sort_order', '>', $plan->sort_order)
            ->orderBy('sort_order')->first();
        if ($next) {
            [$plan->sort_order, $next->sort_order] = [$next->sort_order, $plan->sort_order];
            $plan->save();
            $next->save();
            unset($this->plans);
        }
    }

    public function render()
    {
        return view('livewire.platform.plan-index');
    }
}
