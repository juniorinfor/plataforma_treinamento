<?php

namespace App\Livewire\Platform;

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Produtos')]
class ProductIndex extends Component
{
    #[Computed]
    public function products()
    {
        return Product::withCount('items')->orderBy('sort_order')->get();
    }

    public function toggleActive(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);
        unset($this->products);
    }

    public function moveUp(int $id): void
    {
        $product = Product::findOrFail($id);
        $prev = Product::where('sort_order', '<', $product->sort_order)
            ->orderByDesc('sort_order')->first();
        if ($prev) {
            [$product->sort_order, $prev->sort_order] = [$prev->sort_order, $product->sort_order];
            $product->save();
            $prev->save();
            unset($this->products);
        }
    }

    public function moveDown(int $id): void
    {
        $product = Product::findOrFail($id);
        $next = Product::where('sort_order', '>', $product->sort_order)
            ->orderBy('sort_order')->first();
        if ($next) {
            [$product->sort_order, $next->sort_order] = [$next->sort_order, $product->sort_order];
            $product->save();
            $next->save();
            unset($this->products);
        }
    }

    public function render()
    {
        return view('livewire.platform.product-index');
    }
}
