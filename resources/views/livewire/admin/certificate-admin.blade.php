<div class="space-y-6 animate-fade-in">
    @php $authUser = auth()->user(); @endphp

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Templates de Certificado</h1>
        <button wire:click="openCreate"
                @if($authUser->isPlatformAdmin() && !$selectedCompany) disabled @endif
                class="tu-btn tu-btn-primary flex items-center gap-1.5 text-sm disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo template
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
        <span class="text-sm text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg">Selecione uma empresa para gerenciar certificados</span>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->templates as $tpl)
        <div class="tu-card p-5 space-y-3">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="font-semibold text-gray-900">{{ $tpl->name }}</p>
                    @if($tpl->is_default)
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">Padrão</span>
                    @endif
                </div>
                <div class="flex gap-1">
                    <button wire:click="openEdit({{ $tpl->id }})" class="p-1.5 rounded hover:bg-indigo-50 text-gray-400 hover:text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="delete({{ $tpl->id }})" wire:confirm="Excluir '{{ $tpl->name }}'?" class="p-1.5 rounded hover:bg-red-50 text-gray-400 hover:text-red-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
            @if(!$tpl->is_default)
            <button wire:click="setDefault({{ $tpl->id }})"
                    class="w-full text-xs text-gray-500 hover:text-indigo-600 border border-dashed border-gray-200 hover:border-indigo-300 rounded-lg py-1.5 transition-colors">
                Definir como padrão
            </button>
            @endif
        </div>
        @empty
        <div class="col-span-3 py-12 text-center text-sm text-gray-400">Nenhum template encontrado.</div>
        @endforelse
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-on:keydown.escape.window="$wire.set('showModal', false)">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar template' : 'Novo template' }}</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                        <input wire:model="name" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer mt-5">
                        <input type="checkbox" wire:model="is_default" class="rounded text-indigo-600">
                        <span class="text-sm text-gray-700">Template padrão</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">HTML do certificado *</label>
                    <p class="text-xs text-gray-400 mb-1">Use <code class="bg-gray-50 px-1 rounded">{{"{{"}}student_name{{"}}"}}</code>, <code class="bg-gray-50 px-1 rounded">{{"{{"}}course_name{{"}}"}}</code>, <code class="bg-gray-50 px-1 rounded">{{"{{"}}completion_date{{"}}"}}</code> como variáveis.</p>
                    <textarea wire:model="html_template" rows="8"
                              class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-300 outline-none resize-y"></textarea>
                    @error('html_template') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CSS personalizado</label>
                    <textarea wire:model="css" rows="4"
                              class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-300 outline-none resize-y"
                              placeholder=".certificate { background: white; padding: 40px; }"></textarea>
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
