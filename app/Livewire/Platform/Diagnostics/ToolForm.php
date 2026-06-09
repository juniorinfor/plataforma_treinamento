<?php

namespace App\Livewire\Platform\Diagnostics;

use App\Enums\DiagnosticInputSource;
use App\Enums\DiagnosticToolType;
use App\Models\DiagnosticDimension;
use App\Models\DiagnosticTool;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Ferramenta de Diagnóstico')]
class ToolForm extends Component
{
    public ?int $toolId = null;

    // Campos da ferramenta
    public string $code            = '';
    public string $name            = '';
    public string $slug            = '';
    public string $short_description = '';
    public string $description     = '';
    public string $type            = 'simple';
    public string $input_source    = 'questionnaire';
    public bool   $requires_review = false;
    public string $icon            = 'chart-bar';
    public string $color           = '#6366F1';
    public int    $estimated_minutes = 15;
    public int    $xp_reward       = 50;
    public bool   $is_published    = false;
    public bool   $is_platform_tool = true;
    public int    $sort_order      = 0;

    // Dimensões (lista inline)
    public array $dimensions = [];   // [{id?, name, code, color, weight, sort_order}]

    // Nova dimensão em edição
    public string $dimName   = '';
    public string $dimCode   = '';
    public string $dimColor  = '#6366F1';
    public float  $dimWeight = 1.0;

    public function mount(?DiagnosticTool $tool = null): void
    {
        if ($tool?->id) {
            $this->toolId             = $tool->id;
            $this->code               = $tool->code ?? '';
            $this->name               = $tool->name;
            $this->slug               = $tool->slug;
            $this->short_description  = $tool->short_description ?? '';
            $this->description        = $tool->description ?? '';
            $this->type               = $tool->type->value;
            $this->input_source       = $tool->input_source->value;
            $this->requires_review    = $tool->requires_review;
            $this->icon               = $tool->icon ?? 'chart-bar';
            $this->color              = $tool->color ?? '#6366F1';
            $this->estimated_minutes  = $tool->estimated_minutes;
            $this->xp_reward          = $tool->xp_reward;
            $this->is_published       = $tool->is_published;
            $this->is_platform_tool   = $tool->is_platform_tool;
            $this->sort_order         = $tool->sort_order;

            $this->dimensions = $tool->dimensions->map(fn ($d) => [
                'id'         => $d->id,
                'name'       => $d->name,
                'code'       => $d->code ?? '',
                'color'      => $d->color ?? $tool->color ?? '#6366F1',
                'weight'     => (float) $d->weight,
                'sort_order' => $d->sort_order,
            ])->toArray();
        }
    }

    public function updatedName(string $value): void
    {
        if (!$this->toolId) {
            $this->slug = Str::slug($value);
        }
    }

    public function addDimension(): void
    {
        $this->validate([
            'dimName' => 'required|min:2',
        ], ['dimName.required' => 'Informe o nome da dimensão.']);

        $this->dimensions[] = [
            'id'         => null,
            'name'       => $this->dimName,
            'code'       => strtoupper($this->dimCode ?: Str::limit(Str::slug($this->dimName, ''), 5, '')),
            'color'      => $this->dimColor,
            'weight'     => $this->dimWeight,
            'sort_order' => count($this->dimensions) + 1,
        ];

        $this->dimName = $this->dimCode = '';
        $this->dimColor = '#6366F1';
        $this->dimWeight = 1.0;
        $this->resetErrorBag('dimName');
    }

    public function removeDimension(int $index): void
    {
        array_splice($this->dimensions, $index, 1);
        // Re-indexa sort_order
        foreach ($this->dimensions as $i => &$dim) {
            $dim['sort_order'] = $i + 1;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name'              => 'required|min:3|max:120',
            'slug'              => 'required|min:3|max:120',
            'type'              => 'required|in:simple,composite',
            'input_source'      => 'required|in:questionnaire,upload,both',
            'estimated_minutes' => 'required|integer|min:1',
            'xp_reward'         => 'required|integer|min:0',
            'color'             => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'name.required'  => 'O nome é obrigatório.',
            'slug.required'  => 'O slug é obrigatório.',
            'color.regex'    => 'Cor inválida (use formato #RRGGBB).',
        ]);

        $data = [
            'code'             => strtoupper($this->code) ?: null,
            'name'             => $this->name,
            'slug'             => Str::slug($this->slug),
            'short_description'=> $this->short_description ?: null,
            'description'      => $this->description ?: null,
            'type'             => $this->type,
            'input_source'     => $this->input_source,
            'requires_review'  => $this->requires_review,
            'icon'             => $this->icon ?: 'chart-bar',
            'color'            => $this->color,
            'estimated_minutes'=> $this->estimated_minutes,
            'xp_reward'        => $this->xp_reward,
            'is_published'     => $this->is_published,
            'is_platform_tool' => $this->is_platform_tool,
            'sort_order'       => $this->sort_order,
            'created_by'       => auth()->id(),
        ];

        if ($this->toolId) {
            $tool = DiagnosticTool::findOrFail($this->toolId);
            $tool->update($data);
        } else {
            $tool = DiagnosticTool::create($data);
            $this->toolId = $tool->id;
        }

        // Sincroniza dimensões
        $existingIds = collect($this->dimensions)->pluck('id')->filter()->toArray();
        DiagnosticDimension::where('diagnostic_tool_id', $tool->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        foreach ($this->dimensions as $i => $dim) {
            $dimData = [
                'diagnostic_tool_id' => $tool->id,
                'name'       => $dim['name'],
                'code'       => $dim['code'] ?: null,
                'slug'       => Str::slug($dim['name']),
                'color'      => $dim['color'],
                'weight'     => $dim['weight'],
                'sort_order' => $i + 1,
            ];

            if ($dim['id']) {
                DiagnosticDimension::where('id', $dim['id'])->update($dimData);
            } else {
                $created = DiagnosticDimension::create($dimData);
                $this->dimensions[$i]['id'] = $created->id;
            }
        }

        $this->redirect(route('platform.diagnostics.questions', $tool->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.platform.diagnostics.tool-form', [
            'toolTypes'    => DiagnosticToolType::cases(),
            'inputSources' => DiagnosticInputSource::cases(),
            'iconOptions'  => [
                'chart-bar'  => 'Gráfico de Barras',
                'chart-pie'  => 'Gráfico de Pizza',
                'user-circle'=> 'Usuário',
                'sparkles'   => 'Estrelas',
                'flag'       => 'Bandeira',
                'heart'      => 'Coração',
                'shield-check'=> 'Escudo',
                'sun'        => 'Sol',
                'academic-cap'=> 'Formatura',
            ],
        ]);
    }
}
