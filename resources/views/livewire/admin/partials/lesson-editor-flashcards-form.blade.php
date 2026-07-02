<div class="space-y-4">
    @error('bufCards') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

    @foreach($bufCards as $i => $card)
    <div class="rounded-xl border border-gray-200 p-4 space-y-2.5" wire:key="card-{{ $i }}">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cartão {{ $i + 1 }}</span>
            @if(count($bufCards) > 1)
            <button wire:click="removeCard({{ $i }})" class="text-xs font-semibold text-red-400 hover:text-red-600">Remover</button>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Frente</label>
                <textarea wire:model="bufCards.{{ $i }}.front" rows="2" placeholder="Pergunta ou termo..."
                          class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Verso</label>
                <textarea wire:model="bufCards.{{ $i }}.back" rows="2" placeholder="Resposta ou explicação..."
                          class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y"></textarea>
            </div>
        </div>
        @error("bufCards.$i") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
    </div>
    @endforeach

    <button wire:click="addCard" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Adicionar cartão</button>

    @include('livewire.admin.partials.lesson-editor-save-cancel')
</div>
