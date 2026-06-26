<?php

namespace App\Livewire\Platform;

use App\Enums\ProductItemType;
use App\Enums\ProductType;
use App\Models\Course;
use App\Models\DiagnosticTool;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Produto')]
class ProductForm extends Component
{
    public ?int $productId = null;

    public string $name          = '';
    public string $slug          = '';
    public string $description   = '';
    public string $type          = 'pacote';
    public ?float $price_once    = null;
    public ?float $price_monthly = null;
    public ?float $price_yearly  = null;
    public bool   $is_active     = true;
    public int    $sort_order    = 0;

    /** Itens do pacote (array local; persistido no save). */
    public array $items = [];

    /** Controla qual seletor inline está aberto: null | 'course' | 'diagnostic' */
    public ?string $addingItemType = null;
    public ?int    $addItemRefId   = null;

    public function mount(?Product $product = null): void
    {
        if ($product && $product->exists) {
            $this->productId    = $product->id;
            $this->name         = $product->name;
            $this->slug         = $product->slug;
            $this->description  = $product->description ?? '';
            $this->type         = $product->type->value;
            $this->price_once   = $product->price_once   !== null ? (float) $product->price_once   : null;
            $this->price_monthly= $product->price_monthly!== null ? (float) $product->price_monthly: null;
            $this->price_yearly = $product->price_yearly !== null ? (float) $product->price_yearly : null;
            $this->is_active    = $product->is_active;
            $this->sort_order   = $product->sort_order;

            $this->items = $product->items()->with(['course', 'tool'])->get()
                ->map(fn (ProductItem $item) => [
                    'type'               => $item->type->value,
                    'course_id'          => $item->course_id,
                    'diagnostic_tool_id' => $item->diagnostic_tool_id,
                    'label'              => $item->label(),
                ])->toArray();
        }
    }

    #[Computed]
    public function availableCourses()
    {
        return Course::where('is_published', true)
            ->where('is_platform_course', true)
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    #[Computed]
    public function availableTools()
    {
        return DiagnosticTool::where('is_published', true)
            ->where('is_platform_tool', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function updatedName(): void
    {
        if (!$this->productId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedType(): void
    {
        if ($this->type !== ProductType::Pacote->value) {
            $this->items = [];
            $this->addingItemType = null;
        }
    }

    public function openAddItem(string $kind): void
    {
        $this->addingItemType = $kind;
        $this->addItemRefId   = null;
    }

    public function cancelAddItem(): void
    {
        $this->addingItemType = null;
        $this->addItemRefId   = null;
    }

    public function addItem(string $itemType, ?int $refId = null): void
    {
        $type = ProductItemType::from($itemType);

        if ($type->needsReference() && !$refId) {
            return;
        }

        // Prevent duplicates
        foreach ($this->items as $existing) {
            if ($existing['type'] === $itemType
                && $existing['course_id'] === ($type === ProductItemType::Course ? $refId : null)
                && $existing['diagnostic_tool_id'] === ($type === ProductItemType::Diagnostic ? $refId : null)
            ) {
                $this->addingItemType = null;
                return;
            }
        }

        $label = match ($type) {
            ProductItemType::AllCourses     => 'Todos os cursos',
            ProductItemType::AllDiagnostics => 'Todos os diagnósticos',
            ProductItemType::Course         => Course::find($refId)?->title ?? '—',
            ProductItemType::Diagnostic     => DiagnosticTool::find($refId)?->name ?? '—',
        };

        $this->items[] = [
            'type'               => $itemType,
            'course_id'          => $type === ProductItemType::Course ? $refId : null,
            'diagnostic_tool_id' => $type === ProductItemType::Diagnostic ? $refId : null,
            'label'              => $label,
        ];

        $this->addingItemType = null;
        $this->addItemRefId   = null;
    }

    public function removeItem(int $index): void
    {
        array_splice($this->items, $index, 1);
        $this->items = array_values($this->items);
    }

    protected function rules(): array
    {
        $uniqueSlug = 'unique:products,slug' . ($this->productId ? ",{$this->productId}" : '');

        return [
            'name'          => 'required|string|max:100',
            'slug'          => "required|string|max:120|{$uniqueSlug}",
            'description'   => 'nullable|string|max:500',
            'type'          => 'required|in:course_avulso,pacote',
            'price_once'    => 'nullable|numeric|min:0',
            'price_monthly' => 'nullable|numeric|min:0',
            'price_yearly'  => 'nullable|numeric|min:0',
            'sort_order'    => 'required|integer|min:0',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['is_active'] = $this->is_active;

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->update($data);
        } else {
            $product = Product::create($data);
        }

        // Rebuild items from local array
        $product->items()->delete();
        foreach ($this->items as $item) {
            $product->items()->create([
                'type'               => $item['type'],
                'course_id'          => $item['course_id'],
                'diagnostic_tool_id' => $item['diagnostic_tool_id'],
            ]);
        }

        session()->flash('status', 'Produto salvo com sucesso.');
        $this->redirect(route('platform.products.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.platform.product-form');
    }
}
