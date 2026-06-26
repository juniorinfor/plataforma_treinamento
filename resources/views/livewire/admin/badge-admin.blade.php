<div class="space-y-6 animate-fade-in">
    @php $authUser = auth()->user(); @endphp

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Conquistas</h1>
        <button wire:click="openCreate"
                @if($authUser->isPlatformAdmin() && !$selectedCompany) disabled @endif
                class="tu-btn tu-btn-primary flex items-center gap-1.5 text-sm disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova conquista
        </button>
    </div>

    @if($authUser->isPlatformAdmin())
    <div class="flex items-center gap-3">
        <select wire:model.live="selectedCompany"
                class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
            <option value="">— Selecione uma empresa —</option>
            @foreach($this->companies as $company)
            <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>
        @if(!$selectedCompany)
        <span class="text-sm text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg">Selecione uma empresa para gerenciar conquistas</span>
        @endif
    </div>
    @endif

    <div class="tu-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Conquista</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Raridade</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">XP</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($this->badges as $badge)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs"
                                 style="background-color: {{ $badge->color ?? '#6366F1' }}">
                                🏆
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $badge->name }}</p>
                                @if($badge->description)
                                <p class="text-xs text-gray-400 truncate max-w-xs">{{ $badge->description }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full text-white"
                              style="background-color: {{ $badge->rarity->color() }}">
                            {{ $badge->rarity->label() }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right text-sm font-semibold text-gray-700">{{ $badge->xp_reward }} XP</td>
                    <td class="px-5 py-3 text-center">
                        <button wire:click="toggleActive({{ $badge->id }})"
                                class="text-xs font-bold px-2.5 py-1 rounded-full transition-colors
                                       {{ $badge->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            {{ $badge->is_active ? 'Ativa' : 'Inativa' }}
                        </button>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="inline-flex items-center gap-1">
                            <button wire:click="openEdit({{ $badge->id }})"
                                    class="p-1.5 rounded hover:bg-indigo-50 text-gray-400 hover:text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="delete({{ $badge->id }})"
                                    wire:confirm="Excluir '{{ $badge->name }}'?"
                                    class="p-1.5 rounded hover:bg-red-50 text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">Nenhuma conquista encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-on:keydown.escape.window="$wire.set('showModal', false)">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-4">
            <h3 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar conquista' : 'Nova conquista' }}</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input wire:model="name" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <textarea wire:model="description" rows="2" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Raridade *</label>
                        <select wire:model="rarity" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                            @foreach($this->rarities as $r)
                            <option value="{{ $r['value'] }}">{{ $r['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">XP</label>
                        <input wire:model="xp_reward" type="number" min="0" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                        <input wire:model="color" type="color" class="w-full h-10 rounded-xl border border-gray-200 px-1 py-1 cursor-pointer">
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer mt-5">
                        <input type="checkbox" wire:model="is_active" class="rounded text-indigo-600">
                        <span class="text-sm text-gray-700">Ativa</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button wire:click="$set('showModal', false)" class="tu-btn border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-xl">Cancelar</button>
                <button wire:click="save" wire:loading.attr="disabled" class="tu-btn tu-btn-primary text-sm px-5 py-2 rounded-xl disabled:opacity-60">
                    <span wire:loading.remove>Salvar</span><span wire:loading>Salvando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
