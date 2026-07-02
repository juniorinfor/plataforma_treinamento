<div class="space-y-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Título (opcional)</label>
        <input type="text" wire:model="bufContent" placeholder="Ex: Perguntas frequentes"
               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
    </div>

    @error('bufSections') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

    @foreach($bufSections as $i => $section)
    <div class="rounded-xl border border-gray-200 p-4 space-y-2.5" wire:key="sec-{{ $i }}">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Seção {{ $i + 1 }}</span>
            @if(count($bufSections) > 1)
            <button wire:click="removeSection({{ $i }})" class="text-xs font-semibold text-red-400 hover:text-red-600">Remover</button>
            @endif
        </div>
        <input type="text" wire:model="bufSections.{{ $i }}.title" placeholder="Título da seção"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
        <textarea wire:model="bufSections.{{ $i }}.body" rows="3" placeholder="Conteúdo da seção..."
                  class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y"></textarea>
        @error("bufSections.$i") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
    </div>
    @endforeach

    <button wire:click="addSection" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Adicionar seção</button>

    @include('livewire.admin.partials.lesson-editor-save-cancel')
</div>
