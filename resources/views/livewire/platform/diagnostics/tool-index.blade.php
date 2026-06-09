<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ferramentas de Diagnóstico</h1>
            <p class="text-sm text-gray-500 mt-1">Gerencie as ferramentas disponíveis para os clientes.</p>
        </div>
        <a href="{{ route('platform.diagnostics.create') }}" wire:navigate
           class="tu-btn tu-btn-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Ferramenta
        </a>
    </div>

    {{-- Tabela --}}
    <div class="tu-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ferramenta</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Perguntas</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aplicações</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($this->tools as $tool)
                <tr class="hover:bg-gray-50 transition-colors" wire:key="tool-{{ $tool->id }}">

                    {{-- Nome --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0"
                                 style="background: {{ $tool->color ?? '#6366F1' }}">
                                {{ $tool->code ?? substr($tool->name, 0, 2) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $tool->name }}</p>
                                <p class="text-xs text-gray-400">{{ $tool->short_description }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Tipo --}}
                    <td class="px-5 py-4 text-center">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                            {{ $tool->type->value === 'composite' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600' }}">
                            {{ $tool->type->label() }}
                        </span>
                    </td>

                    {{-- Perguntas --}}
                    <td class="px-5 py-4 text-center">
                        @if($tool->type->value === 'composite')
                            <span class="text-xs text-gray-400 italic">via índices</span>
                        @else
                            <span class="font-semibold text-gray-900">{{ $tool->questions_count }}</span>
                        @endif
                    </td>

                    {{-- Aplicações --}}
                    <td class="px-5 py-4 text-center">
                        <span class="font-semibold text-gray-900">{{ $tool->assessments_count }}</span>
                    </td>

                    {{-- Status toggle --}}
                    <td class="px-5 py-4 text-center">
                        <button wire:click="togglePublish({{ $tool->id }})"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full transition-colors
                                    {{ $tool->is_published
                                        ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100'
                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $tool->is_published ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ $tool->is_published ? 'Publicada' : 'Rascunho' }}
                        </button>
                    </td>

                    {{-- Ações --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('platform.diagnostics.questions', $tool) }}" wire:navigate
                               class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Perguntas
                            </a>
                            <a href="{{ route('platform.diagnostics.edit', $tool) }}" wire:navigate
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Editar
                            </a>
                            <button wire:click="$set('confirmDelete', '{{ $tool->id }}')"
                                    class="text-red-400 hover:text-red-600 text-sm font-medium">
                                Excluir
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                        Nenhuma ferramenta criada ainda.
                        <a href="{{ route('platform.diagnostics.create') }}" wire:navigate class="text-indigo-500 font-medium ml-1">Criar agora</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal confirmação exclusão --}}
    @if($confirmDelete)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         x-data x-transition>
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Excluir ferramenta?</h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Todas as dimensões, perguntas e aplicações associadas serão excluídas permanentemente.
            </p>
            <div class="flex gap-3">
                <button wire:click="$set('confirmDelete', null)"
                        class="flex-1 tu-btn py-2.5 text-sm font-semibold border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button wire:click="deleteTool({{ $confirmDelete }})"
                        class="flex-1 tu-btn py-2.5 text-sm font-semibold rounded-xl bg-red-600 text-white hover:bg-red-700">
                    Excluir
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
