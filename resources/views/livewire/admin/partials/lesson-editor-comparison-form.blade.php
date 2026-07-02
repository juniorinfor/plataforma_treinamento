<div class="space-y-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Título (opcional)</label>
        <input type="text" wire:model="bufContent" placeholder="Ex: Os 3 estilos de comunicação"
               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
    </div>

    @error('bufColumns') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($bufColumns as $i => $col)
        <div class="rounded-xl border border-gray-200 p-4 space-y-2.5" wire:key="col-{{ $i }}">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Coluna {{ $i + 1 }}</span>
                @if(count($bufColumns) > 2)
                <button wire:click="removeColumn({{ $i }})" class="text-xs font-semibold text-red-400 hover:text-red-600">Remover</button>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <input type="color" wire:model="bufColumns.{{ $i }}.color" class="w-9 h-9 rounded-lg border border-gray-200 shrink-0 cursor-pointer">
                <input type="text" wire:model="bufColumns.{{ $i }}.title" placeholder="Título da coluna"
                       class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
            </div>
            <textarea wire:model="bufColumns.{{ $i }}.itemsText" rows="4" placeholder="Um item por linha..."
                      class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y"></textarea>
            @error("bufColumns.$i") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        @endforeach
    </div>

    <button wire:click="addColumn" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Adicionar coluna</button>

    @include('livewire.admin.partials.lesson-editor-save-cancel')
</div>
