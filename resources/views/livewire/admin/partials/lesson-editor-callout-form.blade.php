@php $styles = $calloutStyles ?? []; @endphp
<div class="space-y-3">
    <label class="block text-sm font-semibold text-gray-700">Estilo</label>
    <div class="flex flex-wrap gap-2">
        @foreach($styles as $key => $s)
        <label class="flex items-center gap-2 px-3 py-2 rounded-xl border-2 cursor-pointer text-sm font-semibold
                       {{ $bufStyle === $key ? $s['bg'] . ' ' . $s['border'] . ' ' . $s['text'] : 'border-gray-200 text-gray-500' }}">
            <input type="radio" wire:model="bufStyle" value="{{ $key }}" class="sr-only">
            <span>{{ $s['icon'] }}</span> {{ $s['label'] }}
        </label>
        @endforeach
    </div>
    @error('bufStyle') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

    <label class="block text-sm font-semibold text-gray-700">Título (opcional)</label>
    <input type="text" wire:model="bufTitle" placeholder="Ex: Reflexão"
           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">

    <label class="block text-sm font-semibold text-gray-700">Conteúdo</label>
    <textarea wire:model="bufContent" rows="4" autofocus placeholder="Texto do destaque..."
              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y"></textarea>
    @error('bufContent') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

    @include('livewire.admin.partials.lesson-editor-save-cancel')
</div>
