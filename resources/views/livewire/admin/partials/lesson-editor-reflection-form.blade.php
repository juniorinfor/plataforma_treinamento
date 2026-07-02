<div class="space-y-3">
    <label class="block text-sm font-semibold text-gray-700">Pergunta de reflexão</label>
    <p class="text-xs text-gray-400">O aluno verá um campo de texto aberto para escrever a resposta.</p>
    <textarea wire:model="bufContent" rows="3" autofocus placeholder="Ex: Qual é o seu 1% para esta semana?"
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y"></textarea>
    @error('bufContent') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

    @include('livewire.admin.partials.lesson-editor-save-cancel')
</div>
