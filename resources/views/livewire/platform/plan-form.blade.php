<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">

    <div class="flex items-center gap-3">
        <a href="{{ route('platform.plans.index') }}" wire:navigate
           class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $planId ? 'Editar plano' : 'Novo plano' }}</h1>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- Dados básicos --}}
        <div class="tu-card p-6 space-y-5">
            <h2 class="font-semibold text-gray-800">Dados do plano</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input wire:model.live="name" type="text"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none"
                           placeholder="Ex: Plano Profissional">
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
                          placeholder="Breve descrição exibida na página de planos"></textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Preços --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço mensal (R$) *</label>
                    <input wire:model="price_monthly" type="number" min="0" step="0.01"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none">
                    @error('price_monthly') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preço anual (R$) *</label>
                    <input wire:model="price_yearly" type="number" min="0" step="0.01"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none">
                    @error('price_yearly') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Limites --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Máx. usuários *</label>
                    <input wire:model="max_users" type="number" min="1"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none">
                    @error('max_users') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Máx. cursos *</label>
                    <input wire:model="max_courses" type="number" min="1"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none">
                    @error('max_courses') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Storage (MB) *</label>
                    <input wire:model="max_storage_mb" type="number" min="1"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none">
                    @error('max_storage_mb') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
                    <span class="text-sm font-medium text-gray-700">Plano ativo (visível na página de planos)</span>
                </label>
            </div>
        </div>

        {{-- Produtos incluídos --}}
        <div class="tu-card p-6">
            <h2 class="font-semibold text-gray-800 mb-1">Produtos incluídos neste plano</h2>
            <p class="text-xs text-gray-400 mb-4">Marque os cursos avulsos ou pacotes que fazem parte desta assinatura.</p>

            @if($this->availableProducts->isEmpty())
            <p class="text-sm text-gray-400">Nenhum produto cadastrado ainda. <a href="{{ route('platform.products.create') }}" wire:navigate class="text-indigo-500 underline">Criar produto →</a></p>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($this->availableProducts as $product)
                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                              {{ in_array($product->id, $selectedProductIds) ? 'border-indigo-400 bg-indigo-50' : 'border-gray-100 hover:border-gray-200' }}">
                    <input type="checkbox" wire:model="selectedProductIds" value="{{ $product->id }}"
                           class="rounded text-indigo-600 focus:ring-indigo-400 sr-only">
                    <div class="w-4 h-4 rounded border-2 flex items-center justify-center shrink-0 transition-colors
                                {{ in_array($product->id, $selectedProductIds) ? 'bg-indigo-600 border-indigo-600' : 'border-gray-300' }}">
                        @if(in_array($product->id, $selectedProductIds))
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $product->name }}</p>
                        <span class="text-[11px] font-medium px-1.5 py-0.5 rounded-full
                                     {{ $product->type->value === 'pacote' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600' }}">
                            {{ $product->type->label() }}
                        </span>
                    </div>
                </label>
                @endforeach
            </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3 pb-4">
            <a href="{{ route('platform.plans.index') }}" wire:navigate
               class="tu-btn border-2 border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold px-5 py-2.5 rounded-xl">
                Cancelar
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="tu-btn tu-btn-primary text-sm px-6 py-2.5 rounded-xl disabled:opacity-60 flex items-center gap-2">
                <span wire:loading.remove>Salvar plano</span>
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
