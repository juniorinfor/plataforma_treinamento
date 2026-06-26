<div class="space-y-6 animate-fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Planos</h1>
            <p class="text-sm text-gray-500 mt-0.5">Assinaturas disponíveis para empresas</p>
        </div>
        <a href="{{ route('platform.plans.create') }}" wire:navigate
           class="tu-btn tu-btn-primary flex items-center gap-1.5 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo plano
        </a>
    </div>

    <div class="tu-card overflow-hidden">
        @if($this->plans->isEmpty())
        <div class="px-6 py-12 text-center text-gray-400 text-sm">
            Nenhum plano cadastrado ainda.
        </div>
        @else
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nome</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Mensal</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Anual</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Usuários</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Produtos</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($this->plans as $plan)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-gray-900 text-sm">{{ $plan->name }}</p>
                        @if($plan->description)
                        <p class="text-xs text-gray-400 truncate max-w-xs">{{ $plan->description }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-gray-700">
                        R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}
                    </td>
                    <td class="px-5 py-3 text-right text-sm text-gray-700">
                        R$ {{ number_format($plan->price_yearly, 2, ',', '.') }}
                    </td>
                    <td class="px-5 py-3 text-center text-sm text-gray-700">
                        {{ $plan->max_users > 9999 ? '∞' : $plan->max_users }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-sm font-semibold {{ $plan->products_count > 0 ? 'text-indigo-600' : 'text-gray-400' }}">
                            {{ $plan->products_count }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <button wire:click="toggleActive({{ $plan->id }})"
                                class="text-xs font-bold px-2.5 py-1 rounded-full transition-colors
                                       {{ $plan->is_active
                                           ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                                           : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                        </button>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <button wire:click="moveUp({{ $plan->id }})"
                                    class="p-1.5 rounded hover:bg-gray-100 text-gray-400 hover:text-gray-600" title="Subir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>
                            <button wire:click="moveDown({{ $plan->id }})"
                                    class="p-1.5 rounded hover:bg-gray-100 text-gray-400 hover:text-gray-600" title="Descer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <a href="{{ route('platform.plans.edit', $plan) }}" wire:navigate
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
