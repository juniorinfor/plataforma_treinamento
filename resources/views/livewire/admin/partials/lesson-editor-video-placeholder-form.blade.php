<div class="space-y-3">
    <label class="block text-sm font-semibold text-gray-700">Descrição do vídeo (a ser gravado)</label>
    <p class="text-xs text-gray-400">Exibe um aviso "vídeo em breve" para o aluno. Quando o vídeo for gravado, exclua este bloco e adicione um bloco de "Vídeo externo" no lugar.</p>
    <input type="text" wire:model="bufContent" autofocus placeholder="Ex: Os 3 estilos de comunicação"
           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
    @error('bufContent') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
    @include('livewire.admin.partials.lesson-editor-save-cancel')
</div>
