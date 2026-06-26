<?php

namespace App\Livewire\Platform;

use App\Models\Plan;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Plano')]
class PlanForm extends Component
{
    public ?int $planId = null;

    // Campos do plano
    public string $name           = '';
    public string $slug           = '';
    public string $description    = '';
    public float  $price_monthly  = 0;
    public float  $price_yearly   = 0;
    public int    $max_users      = 25;
    public int    $max_courses    = 10;
    public int    $max_storage_mb = 1024;
    public bool   $is_active      = true;
    public int    $sort_order     = 0;

    /** IDs dos produtos selecionados para este plano. */
    public array $selectedProductIds = [];

    public function mount(?Plan $plan = null): void
    {
        if ($plan && $plan->exists) {
            $this->planId          = $plan->id;
            $this->name            = $plan->name;
            $this->slug            = $plan->slug;
            $this->description     = $plan->description ?? '';
            $this->price_monthly   = (float) $plan->price_monthly;
            $this->price_yearly    = (float) $plan->price_yearly;
            $this->max_users       = $plan->max_users;
            $this->max_courses     = $plan->max_courses;
            $this->max_storage_mb  = $plan->max_storage_mb;
            $this->is_active       = $plan->is_active;
            $this->sort_order      = $plan->sort_order;
            $this->selectedProductIds = $plan->products()->pluck('products.id')->toArray();
        }
    }

    #[Computed]
    public function availableProducts()
    {
        return Product::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'type']);
    }

    public function updatedName(): void
    {
        if (!$this->planId) {
            $this->slug = Str::slug($this->name);
        }
    }

    protected function rules(): array
    {
        $uniqueSlug = 'unique:plans,slug' . ($this->planId ? ",{$this->planId}" : '');

        return [
            'name'           => 'required|string|max:100',
            'slug'           => "required|string|max:120|{$uniqueSlug}",
            'description'    => 'nullable|string|max:500',
            'price_monthly'  => 'required|numeric|min:0',
            'price_yearly'   => 'required|numeric|min:0',
            'max_users'      => 'required|integer|min:1',
            'max_courses'    => 'required|integer|min:1',
            'max_storage_mb' => 'required|integer|min:1',
            'sort_order'     => 'required|integer|min:0',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $data['is_active'] = $this->is_active;

        if ($this->planId) {
            $plan = Plan::findOrFail($this->planId);
            $plan->update($data);
        } else {
            $plan = Plan::create($data);
        }

        $plan->products()->sync($this->selectedProductIds);

        session()->flash('status', 'Plano salvo com sucesso.');
        $this->redirect(route('platform.plans.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.platform.plan-form');
    }
}
