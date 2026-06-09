<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('platform.diagnostics.index') }}" wire:navigate
           class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $toolId ? 'Editar Ferramenta' : 'Nova Ferramenta' }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">As dimensões definidas aqui organizam as perguntas do questionário.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- ── Identificação ── --}}
        <div class="tu-card p-6 space-y-5">
            <h2 class="font-bold text-gray-900 border-b border-gray-100 pb-3">Identificação</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Código <span class="text-gray-400 text-xs">(ex: IO, LTI)</span></label>
                    <input wire:model="code" type="text" maxlength="10"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 uppercase"
                           placeholder="IO">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome <span class="text-red-400">*</span></label>
                    <input wire:model.live="name" type="text"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                           placeholder="Diagnóstico de Inteligência Organizacional">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Slug <span class="text-red-400">*</span></label>
                    <input wire:model="slug" type="text"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-mono"
                           placeholder="diagnostico-inteligencia-organizacional">
                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição curta</label>
                    <input wire:model="short_description" type="text"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                           placeholder="Frase de impacto para o card">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição completa</label>
                <textarea wire:model="description" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"
                          placeholder="Explique o propósito e metodologia desta ferramenta..."></textarea>
            </div>
        </div>

        {{-- ── Configurações ── --}}
        <div class="tu-card p-6 space-y-5">
            <h2 class="font-bold text-gray-900 border-b border-gray-100 pb-3">Configurações</h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo</label>
                    <select wire:model="type"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @foreach($toolTypes as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Entrada</label>
                    <select wire:model="input_source"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @foreach($inputSources as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tempo (min)</label>
                    <input wire:model="estimated_minutes" type="number" min="1"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">XP Recompensa</label>
                    <input wire:model="xp_reward" type="number" min="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cor</label>
                    <div class="flex items-center gap-2">
                        <input wire:model.live="color" type="color"
                               class="h-10 w-12 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                        <input wire:model="color" type="text" maxlength="7"
                               class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    @error('color') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ícone</label>
                    <select wire:model="icon"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @foreach($iconOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ordem</label>
                    <input wire:model="sort_order" type="number" min="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                <div class="flex flex-col gap-3 pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="requires_review" type="checkbox"
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-300">
                        <span class="text-sm text-gray-700">Requer revisão</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="is_published" type="checkbox"
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-300">
                        <span class="text-sm text-gray-700">Publicada</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Dimensões ── --}}
        <div class="tu-card p-6 space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="font-bold text-gray-900">Dimensões</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    As dimensões organizam as perguntas em grupos temáticos (ex.: para IO use uma dimensão por índice).
                </p>
            </div>

            {{-- Lista de dimensões --}}
            @forelse($dimensions as $i => $dim)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl" wire:key="dim-{{ $i }}">
                <div class="w-8 h-8 rounded-lg shrink-0"
                     style="background: {{ $dim['color'] }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">
                        @if($dim['code']) <span class="text-gray-400 mr-1">{{ $dim['code'] }}</span> @endif
                        {{ $dim['name'] }}
                    </p>
                    <p class="text-xs text-gray-400">Peso: {{ $dim['weight'] }}</p>
                </div>
                <button type="button" wire:click="removeDimension({{ $i }})"
                        class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-3">
                Nenhuma dimensão adicionada. Você pode adicionar depois ou deixar sem dimensões para um questionário simples.
            </p>
            @endforelse

            {{-- Formulário nova dimensão --}}
            <div class="border border-dashed border-gray-200 rounded-xl p-4 space-y-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Adicionar dimensão</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <input wire:model="dimCode" type="text" maxlength="8" placeholder="Código (LTI)"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 uppercase">
                    </div>
                    <div class="sm:col-span-2">
                        <input wire:model="dimName" type="text" placeholder="Nome da dimensão *"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @error('dimName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input wire:model="dimColor" type="color"
                               class="h-9 w-10 rounded-lg border border-gray-200 cursor-pointer p-0.5 shrink-0">
                        <input wire:model="dimWeight" type="number" step="0.1" min="0.1" placeholder="Peso"
                               class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                </div>
                <button type="button" wire:click="addDimension"
                        class="tu-btn text-sm font-semibold px-4 py-2 rounded-xl border-2 border-indigo-200 text-indigo-600 hover:bg-indigo-50 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Adicionar dimensão
                </button>
            </div>
        </div>

        {{-- Ações --}}
        <div class="flex justify-end gap-3 pb-8">
            <a href="{{ route('platform.diagnostics.index') }}" wire:navigate
               class="tu-btn px-5 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                    class="tu-btn px-6 py-2.5 text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 flex items-center gap-2">
                <svg wire:loading wire:target="save" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span wire:loading.remove wire:target="save">Salvar e ir para Perguntas →</span>
                <span wire:loading wire:target="save">Salvando...</span>
            </button>
        </div>

    </form>
</div>
