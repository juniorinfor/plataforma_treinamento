<div class="space-y-6 animate-fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">XP e Níveis</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure os níveis de progressão da plataforma</p>
        </div>
        <button wire:click="openCreate"
                class="tu-btn tu-btn-primary flex items-center gap-1.5 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo nível
        </button>
    </div>

    <div class="tu-card overflow-hidden">
        @if($this->levels->isEmpty())
        <div class="px-6 py-12 text-center text-sm text-gray-400">
            Nenhum nível configurado ainda.
        </div>
        @else
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">XP mín.</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">XP máx.</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($this->levels as $level)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm text-white"
                              style="background-color: {{ $level->color ?? '#6366F1' }}">
                            {{ $level->level_number }}
                        </span>
                    </td>
                    <td class="px-5 py-3 font-semibold text-gray-900 text-sm">{{ $level->name }}</td>
                    <td class="px-5 py-3 text-right text-sm text-gray-700 font-mono">{{ number_format($level->min_xp) }} XP</td>
                    <td class="px-5 py-3 text-right text-sm text-gray-700 font-mono">{{ number_format($level->max_xp) }} XP</td>
                    <td class="px-5 py-3 text-right">
                        <div class="inline-flex items-center gap-1">
                            <button wire:click="openEdit({{ $level->id }})"
                                    class="p-1.5 rounded hover:bg-indigo-50 text-gray-400 hover:text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button wire:click="delete({{ $level->id }})"
                                    wire:confirm="Remover o nível '{{ $level->name }}'?"
                                    class="p-1.5 rounded hover:bg-red-50 text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-on:keydown.escape.window="$wire.set('showModal', false)">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-4">
            <h3 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar nível' : 'Novo nível' }}</h3>

            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nível # *</label>
                        <input wire:model="levelNumber" type="number" min="1"
                               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        @error('levelNumber') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                        <input wire:model="color" type="color"
                               class="w-full h-10 rounded-xl border border-gray-200 px-1 py-1 cursor-pointer">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input wire:model="name" type="text" placeholder="Ex: Aprendiz"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">XP mínimo *</label>
                        <input wire:model="min_xp" type="number" min="0"
                               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        @error('min_xp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">XP máximo *</label>
                        <input wire:model="max_xp" type="number" min="1"
                               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        @error('max_xp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button wire:click="$set('showModal', false)"
                        class="tu-btn border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-xl">Cancelar</button>
                <button wire:click="save"
                        wire:loading.attr="disabled"
                        class="tu-btn tu-btn-primary text-sm px-5 py-2 rounded-xl disabled:opacity-60">
                    <span wire:loading.remove>Salvar</span>
                    <span wire:loading>Salvando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
