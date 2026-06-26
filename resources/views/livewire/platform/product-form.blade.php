<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">

    <div class="flex items-center gap-3">
        <a href="{{ route('platform.products.index') }}" wire:navigate
           class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $productId ? 'Editar produto' : 'Novo produto' }}</h1>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- Dados básicos --}}
        <div class="tu-card p-6 space-y-5">
            <h2 class="font-semibold text-gray-800">Dados do produto</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input wire:model.live="name" type="text"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none"
                           placeholder="Ex: Pacote Liderança">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                    <input wire:model="slug" type="text"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none bg-gray-50">
                    @error('slug') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <textarea wire:model="description" rows="2"
                          class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none resize-none"
                          placeholder="Descrição exibida na página de vendas"></textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="type" value="course_avulso"
                               class="text-indigo-600 focus:ring-indigo-400">
                        <span class="text-sm font-medium text-gray-700">Curso avulso</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model.live="type" value="pacote"
                               class="text-indigo-600 focus:ring-indigo-400">
                        <span class="text-sm font-medium text-gray-700">Pacote</span>
                    </label>
                </div>
                @error('type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Preços --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço avulso (R$)</label>
                    <input wire:model="price_once" type="number" min="0" step="0.01"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none"
                           placeholder="0,00">
                    @error('price_once') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço mensal (R$)</label>
                    <input wire:model="price_monthly" type="number" min="0" step="0.01"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none"
                           placeholder="0,00">
                    @error('price_monthly') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço anual (R$)</label>
                    <input wire:model="price_yearly" type="number" min="0" step="0.01"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none"
                           placeholder="0,00">
                    @error('price_yearly') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="w-24">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                    <input wire:model="sort_order" type="number" min="0"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none">
                </div>
                <label class="flex items-center gap-2 cursor-pointer mt-5">
                    <input type="checkbox" wire:model="is_active" class="rounded text-indigo-600 focus:ring-indigo-400">
                    <span class="text-sm font-medium text-gray-700">Produto ativo (visível para compra)</span>
                </label>
            </div>
        </div>

        {{-- Itens do pacote --}}
        @if($type === 'pacote')
        <div class="tu-card p-6 space-y-4">
            <div>
                <h2 class="font-semibold text-gray-800">Itens do pacote</h2>
                <p class="text-xs text-gray-400 mt-0.5">Defina o que está incluído neste pacote.</p>
            </div>

            {{-- Lista de itens adicionados --}}
            @if(count($items) > 0)
            <ul class="space-y-2">
                @foreach($items as $index => $item)
                <li class="flex items-center gap-3 px-3 py-2 rounded-lg bg-gray-50">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                 {{ in_array($item['type'], ['all_courses', 'course']) ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                        {{ match($item['type']) {
                            'course'          => 'Curso',
                            'diagnostic'      => 'Diagnóstico',
                            'all_courses'     => 'Todos os cursos',
                            'all_diagnostics' => 'Todos os diagnósticos',
                            default           => $item['type'],
                        } }}
                    </span>
                    <span class="flex-1 text-sm text-gray-700 truncate">{{ $item['label'] }}</span>
                    <button type="button" wire:click="removeItem({{ $index }})"
                            class="p-1 rounded hover:bg-red-50 text-gray-300 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-sm text-gray-400">Nenhum item adicionado ainda.</p>
            @endif

            {{-- Seletor inline --}}
            @if($addingItemType === 'course')
            <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-indigo-200 bg-indigo-50">
                <select wire:model="addItemRefId"
                        class="flex-1 rounded-lg border border-gray-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    <option value="">— Selecione um curso —</option>
                    @foreach($this->availableCourses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
                <button type="button"
                        wire:click="addItem('course', {{ $addItemRefId ?? 'null' }})"
                        class="tu-btn tu-btn-primary text-xs px-3 py-1.5 rounded-lg disabled:opacity-50"
                        @disabled(!$addItemRefId)>
                    Adicionar
                </button>
                <button type="button" wire:click="cancelAddItem()"
                        class="text-xs text-gray-400 hover:text-gray-600 px-2">Cancelar</button>
            </div>
            @elseif($addingItemType === 'diagnostic')
            <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-purple-200 bg-purple-50">
                <select wire:model="addItemRefId"
                        class="flex-1 rounded-lg border border-gray-200 px-2 py-1.5 text-sm focus:ring-2 focus:ring-purple-300 outline-none">
                    <option value="">— Selecione um diagnóstico —</option>
                    @foreach($this->availableTools as $tool)
                    <option value="{{ $tool->id }}">{{ $tool->name }}</option>
                    @endforeach
                </select>
                <button type="button"
                        wire:click="addItem('diagnostic', {{ $addItemRefId ?? 'null' }})"
                        class="tu-btn tu-btn-primary text-xs px-3 py-1.5 rounded-lg disabled:opacity-50"
                        @disabled(!$addItemRefId)>
                    Adicionar
                </button>
                <button type="button" wire:click="cancelAddItem()"
                        class="text-xs text-gray-400 hover:text-gray-600 px-2">Cancelar</button>
            </div>
            @else
            {{-- Botões de ação --}}
            <div class="grid grid-cols-2 gap-2">
                <button type="button"
                        wire:click="openAddItem('course')"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 border-dashed border-blue-200 text-blue-600 hover:bg-blue-50 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Curso específico
                </button>
                <button type="button"
                        wire:click="openAddItem('diagnostic')"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 border-dashed border-purple-200 text-purple-600 hover:bg-purple-50 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Diagnóstico específico
                </button>
                <button type="button"
                        wire:click="addItem('all_courses')"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 border-dashed border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Todos os cursos
                </button>
                <button type="button"
                        wire:click="addItem('all_diagnostics')"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 border-dashed border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Todos os diagnósticos
                </button>
            </div>
            @endif
        </div>
        @endif

        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('platform.products.index') }}" wire:navigate
               class="tu-btn border-2 border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold px-5 py-2.5 rounded-xl">
                Cancelar
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="tu-btn tu-btn-primary text-sm px-6 py-2.5 rounded-xl disabled:opacity-60 flex items-center gap-2">
                <span wire:loading.remove>Salvar produto</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Salvando...
                </span>
            </button>
        </div>

    </form>
</div>
