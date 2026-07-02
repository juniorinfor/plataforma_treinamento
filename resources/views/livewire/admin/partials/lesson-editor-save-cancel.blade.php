@php
    $action = $saveAction ?? 'saveEdit';
    $cancel = $cancelAction ?? ($action === 'saveEdit' ? 'cancelEdit' : 'cancelAdding');
@endphp
<div class="flex gap-2">
    <button wire:click="{{ $action }}" class="px-4 py-2 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">Salvar</button>
    <button wire:click="{{ $cancel }}" class="px-4 py-2 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
</div>
