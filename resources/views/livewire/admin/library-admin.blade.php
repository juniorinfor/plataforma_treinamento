<div class="space-y-6 animate-fade-in">
    @php $authUser = auth()->user(); @endphp

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Biblioteca</h1>
        <button wire:click="openCreate"
                @if($authUser->isPlatformAdmin() && !$selectedCompany) disabled @endif
                class="tu-btn tu-btn-primary flex items-center gap-1.5 text-sm disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo documento
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
        <span class="text-sm text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg">Selecione uma empresa para gerenciar documentos</span>
        @endif
    </div>
    @endif

    @if(session('status'))
    <div class="tu-card p-3 bg-emerald-50 border border-emerald-200 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="tu-card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Documento</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Enviado por</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Tamanho</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($this->documents as $doc)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-gray-900 text-sm">{{ $doc->title }}</p>
                        @if($doc->description)
                        <p class="text-xs text-gray-400 truncate max-w-xs">{{ $doc->description }}</p>
                        @endif
                        @if($doc->file_path)
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                           class="text-xs text-indigo-500 hover:underline">Abrir arquivo</a>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $doc->uploader?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-right text-sm text-gray-500 font-mono">
                        {{ $doc->file_size_kb ? round($doc->file_size_kb / 1024, 1) . ' MB' : '—' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        <button wire:click="togglePublish({{ $doc->id }})"
                                class="text-xs font-bold px-2.5 py-1 rounded-full transition-colors
                                       {{ $doc->is_published ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            {{ $doc->is_published ? 'Publicado' : 'Rascunho' }}
                        </button>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="inline-flex items-center gap-1">
                            <button wire:click="openEdit({{ $doc->id }})"
                                    class="p-1.5 rounded hover:bg-indigo-50 text-gray-400 hover:text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button wire:click="delete({{ $doc->id }})"
                                    wire:confirm="Excluir '{{ $doc->title }}'?"
                                    class="p-1.5 rounded hover:bg-red-50 text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">
                        Nenhum documento {{ $selectedCompany || !$authUser->isPlatformAdmin() ? 'encontrado' : '— selecione uma empresa' }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($this->documents->hasPages())
        <div class="px-5 py-3 border-t border-gray-50">{{ $this->documents->links() }}</div>
        @endif
    </div>

    {{-- Modal upload --}}
    @if($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-on:keydown.escape.window="$wire.set('showForm', false)">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showForm', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-lg font-bold text-gray-900">{{ $editingId ? 'Editar documento' : 'Novo documento' }}</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                    <input wire:model="title" type="text"
                           class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <textarea wire:model="description" rows="2"
                              class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 outline-none resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Arquivo {{ $editingId ? '(deixe vazio para manter atual)' : '*' }}
                    </label>
                    <input wire:model="file" type="file"
                           class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('file') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    @if($existingPath)
                    <p class="text-xs text-gray-400 mt-1">Arquivo atual: {{ basename($existingPath) }}</p>
                    @endif
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="is_published" class="rounded text-indigo-600">
                    <span class="text-sm text-gray-700">Publicado (visível para colaboradores)</span>
                </label>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button wire:click="$set('showForm', false)"
                        class="tu-btn border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-xl">Cancelar</button>
                <button wire:click="save" wire:loading.attr="disabled"
                        class="tu-btn tu-btn-primary text-sm px-5 py-2 rounded-xl disabled:opacity-60">
                    <span wire:loading.remove>Salvar</span>
                    <span wire:loading>Salvando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
