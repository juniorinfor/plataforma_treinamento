<div class="space-y-3">
    <label class="block text-sm font-semibold text-gray-700">Texto rico (Markdown)</label>
    <p class="text-xs text-gray-400">Suporta <code>**negrito**</code>, <code>*itálico*</code>, <code># Título</code>, listas com <code>-</code> e links.</p>
    <textarea wire:model="bufContent" rows="10" autofocus placeholder="## Título&#10;&#10;Seu texto formatado aqui..."
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y font-mono"></textarea>
    @error('bufContent') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
    @include('livewire.admin.partials.lesson-editor-save-cancel')
</div>
