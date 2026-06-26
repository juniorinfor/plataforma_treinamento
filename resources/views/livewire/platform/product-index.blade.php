<div class="space-y-6 animate-fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Produtos</h1>
            <p class="text-sm text-gray-500 mt-0.5">Cursos avulsos e pacotes disponíveis para venda</p>
        </div>
        <a href="{{ route('platform.products.create') }}" wire:navigate
           class="tu-btn tu-btn-primary flex items-center gap-1.5 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo produto
        </a>
    </div>

    <div class="tu-card overflow-hidden">
        @if($this->products->isEmpty())
        <div class="px-6 py-12 text-center text-gray-400 text-sm">
            Nenhum produto cadastrado ainda.
        </div>
        @else
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nome</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipo</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Avulso</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Mensal</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Anual</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Itens</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($this->products as $product)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-gray-900 text-sm">{{ $product->name }}</p>
                        @if($product->description)
                        <p class="text-xs text-gray-400 truncate max-w-xs">{{ $product->description }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                     {{ $product->type->value === 'pacote' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600' }}">
                            {{ $product->type->label() }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-gray-600">
                        {{ $product->price_once ? 'R$ ' . number_format($product->price_once, 2, ',', '.') : '—' }}
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-gray-600">
                        {{ $product->price_monthly ? 'R$ ' . number_format($product->price_monthly, 2, ',', '.') : '—' }}
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-gray-600">
                        {{ $product->price_yearly ? 'R$ ' . number_format($product->price_yearly, 2, ',', '.') : '—' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($product->type->value === 'pacote')
                        <span class="text-sm font-semibold {{ $product->items_count > 0 ? 'text-indigo-600' : 'text-gray-300' }}">
                            {{ $product->items_count }}
                        </span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <button wire:click="toggleActive({{ $product->id }})"
                                class="text-xs font-bold px-2.5 py-1 rounded-full transition-colors
                                       {{ $product->is_active
                                           ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                                           : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                        </button>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <button wire:click="moveUp({{ $product->id }})"
                                    class="p-1.5 rounded hover:bg-gray-100 text-gray-400 hover:text-gray-600" title="Subir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>
                            <button wire:click="moveDown({{ $product->id }})"
                                    class="p-1.5 rounded hover:bg-gray-100 text-gray-400 hover:text-gray-600" title="Descer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <a href="{{ route('platform.products.edit', $product) }}" wire:navigate
                               class="p-1.5 rounded hover:bg-indigo-50 text-gray-400 hover:text-indigo-600" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
