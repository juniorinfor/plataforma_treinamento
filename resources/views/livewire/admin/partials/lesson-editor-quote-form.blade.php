<div class="space-y-3">
    <label class="block text-sm font-semibold text-gray-700">Citação</label>
    <textarea wire:model="bufContent" rows="3" autofocus placeholder="A frase da citação..."
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y italic"></textarea>
    @error('bufContent') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

    <label class="block text-sm font-semibold text-gray-700">Autor (opcional)</label>
    <input type="text" wire:model="bufAuthor" placeholder="Ex: Stephen Covey"
           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">

    @include('livewire.admin.partials.lesson-editor-save-cancel')
</div>
