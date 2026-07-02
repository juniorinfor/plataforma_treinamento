<div class="space-y-3">
    <label class="block text-sm font-semibold text-gray-700">Pergunta da autoavaliação</label>
    <p class="text-xs text-gray-400">O aluno responderá numa escala de 1 a 10.</p>
    <textarea wire:model="bufContent" rows="2" autofocus placeholder="Ex: Que nota você daria ao seu atendimento hoje?"
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y"></textarea>
    @error('bufContent') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Rótulo do 1 (opcional)</label>
            <input type="text" wire:model="bufMinLabel" placeholder="Ex: Precisa melhorar"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Rótulo do 10 (opcional)</label>
            <input type="text" wire:model="bufMaxLabel" placeholder="Ex: Excelente"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
        </div>
    </div>

    @include('livewire.admin.partials.lesson-editor-save-cancel')
</div>
