@php
$typeMeta = [
    'text'              => ['icon' => '📝', 'label' => 'Texto simples',    'color' => 'text-blue-600'],
    'rich'              => ['icon' => '🎨', 'label' => 'Texto rico',       'color' => 'text-indigo-600'],
    'video'             => ['icon' => '🎬', 'label' => 'Vídeo',            'color' => 'text-purple-600'],
    'video_placeholder' => ['icon' => '🎥', 'label' => 'Vídeo em breve',   'color' => 'text-purple-400'],
    'pdf'               => ['icon' => '📄', 'label' => 'PDF',              'color' => 'text-red-500'],
    'image'             => ['icon' => '🖼️', 'label' => 'Imagem',           'color' => 'text-teal-600'],
    'callout'           => ['icon' => '💡', 'label' => 'Destaque',         'color' => 'text-amber-600'],
    'quote'             => ['icon' => '❝',  'label' => 'Citação',          'color' => 'text-slate-600'],
    'comparison'        => ['icon' => '⚖️', 'label' => 'Comparativo',      'color' => 'text-fuchsia-600'],
    'flashcards'        => ['icon' => '🃏', 'label' => 'Flashcards',       'color' => 'text-cyan-600'],
    'scale'             => ['icon' => '📊', 'label' => 'Autoavaliação',    'color' => 'text-orange-600'],
    'reflection'        => ['icon' => '✍️', 'label' => 'Reflexão',         'color' => 'text-emerald-600'],
    'accordion'         => ['icon' => '📂', 'label' => 'Acordeão',         'color' => 'text-lime-600'],
];
$calloutStyles = [
    'info'    => ['label' => 'Informação', 'bg' => 'bg-blue-50',    'border' => 'border-blue-200',    'text' => 'text-blue-800',    'icon' => 'ℹ️'],
    'tip'     => ['label' => 'Dica',        'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'icon' => '💡'],
    'warning' => ['label' => 'Atenção',     'bg' => 'bg-amber-50',   'border' => 'border-amber-200',   'text' => 'text-amber-800',   'icon' => '⚠️'],
    'success' => ['label' => 'Sucesso',     'bg' => 'bg-green-50',   'border' => 'border-green-200',   'text' => 'text-green-800',   'icon' => '✅'],
];
@endphp

<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">

    {{-- Navegação --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.courses') }}" wire:navigate class="hover:text-gray-700">Cursos</a>
        <span>/</span>
        <a href="{{ route('admin.courses.builder', $this->lesson->module->course_id) }}" wire:navigate class="hover:text-gray-700">{{ $this->lesson->module->course->title }}</a>
        <span>/</span>
        <span class="text-gray-700 font-medium">{{ $this->lesson->title }}</span>
    </nav>

    {{-- Cabeçalho --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editor de conteúdo</h1>
            <p class="text-sm text-gray-500 mt-1">
                <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full
                    {{ $this->lesson->type->value === 'quiz' ? 'bg-amber-50 text-amber-600' : 'bg-indigo-50 text-indigo-600' }}">
                    {{ $this->lesson->type->label() }}
                </span>
                &nbsp;{{ $this->lesson->duration_minutes }} min
            </p>
        </div>
        <a href="{{ route('admin.courses.builder', $this->lesson->module->course_id) }}" wire:navigate
           class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50 shrink-0">
            ← Voltar ao construtor
        </a>
    </div>

    {{-- Blocos de conteúdo --}}
    @forelse($this->blocks as $bi => $block)
    @php $meta = $typeMeta[$block->type] ?? ['icon' => '❔', 'label' => $block->type, 'color' => 'text-gray-500']; @endphp
    <div class="tu-card overflow-hidden" wire:key="block-{{ $block->id }}">

        {{-- Header do bloco --}}
        <div class="flex items-center justify-between gap-2 px-4 py-2.5 bg-gray-50 border-b border-gray-100">
            <span class="text-xs font-bold uppercase tracking-wider {{ $meta['color'] }}">
                {{ $meta['icon'] }} {{ $meta['label'] }}
            </span>
            <div class="flex items-center gap-1">
                <button wire:click="moveBlock({{ $block->id }}, 'up')" @disabled($bi === 0)
                        class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30" title="Subir">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                </button>
                <button wire:click="moveBlock({{ $block->id }}, 'down')" @disabled($bi === $this->blocks->count() - 1)
                        class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30" title="Descer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                @if($block->type !== 'pdf')
                <button wire:click="startEdit({{ $block->id }})" @disabled($editingBlockId === $block->id)
                        class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-500 disabled:opacity-40" title="Editar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                @endif
                <button wire:click="deleteBlock({{ $block->id }})"
                        wire:confirm="Remover este bloco de conteúdo?"
                        class="p-1.5 rounded-lg hover:bg-red-50 text-red-400" title="Excluir">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Corpo do bloco --}}
        <div class="p-5">
            @if($editingBlockId === $block->id)

                {{-- ══ Formulários de edição ══ --}}
                @if($block->type === 'video')
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">URL do vídeo (YouTube ou Vimeo)</label>
                    <input type="url" wire:model="editVideoUrl" placeholder="https://www.youtube.com/watch?v=..."
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @error('editVideoUrl') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    @include('livewire.admin.partials.lesson-editor-save-cancel')
                </div>

                @elseif($block->type === 'text')
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">Conteúdo do texto</label>
                    <textarea wire:model="editContent" rows="8" placeholder="Digite o conteúdo..."
                              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y font-mono"></textarea>
                    @error('editContent') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    @include('livewire.admin.partials.lesson-editor-save-cancel')
                </div>

                @elseif($block->type === 'image')
                <div class="space-y-3">
                    <p class="text-xs text-gray-400">A imagem não pode ser substituída aqui — apenas a legenda. Exclua e crie um novo bloco para trocar a imagem.</p>
                    <label class="block text-sm font-semibold text-gray-700">Legenda</label>
                    <input type="text" wire:model="bufCaption" placeholder="Legenda da imagem (opcional)"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @include('livewire.admin.partials.lesson-editor-save-cancel')
                </div>

                @elseif($block->type === 'rich')
                @include('livewire.admin.partials.lesson-editor-rich-form', ['saveAction' => 'saveEdit'])

                @elseif($block->type === 'callout')
                @include('livewire.admin.partials.lesson-editor-callout-form', ['saveAction' => 'saveEdit', 'calloutStyles' => $calloutStyles])

                @elseif($block->type === 'quote')
                @include('livewire.admin.partials.lesson-editor-quote-form', ['saveAction' => 'saveEdit'])

                @elseif($block->type === 'comparison')
                @include('livewire.admin.partials.lesson-editor-comparison-form', ['saveAction' => 'saveEdit'])

                @elseif($block->type === 'flashcards')
                @include('livewire.admin.partials.lesson-editor-flashcards-form', ['saveAction' => 'saveEdit'])

                @elseif($block->type === 'scale')
                @include('livewire.admin.partials.lesson-editor-scale-form', ['saveAction' => 'saveEdit'])

                @elseif($block->type === 'reflection')
                @include('livewire.admin.partials.lesson-editor-reflection-form', ['saveAction' => 'saveEdit'])

                @elseif($block->type === 'accordion')
                @include('livewire.admin.partials.lesson-editor-accordion-form', ['saveAction' => 'saveEdit'])

                @elseif($block->type === 'video_placeholder')
                @include('livewire.admin.partials.lesson-editor-video-placeholder-form', ['saveAction' => 'saveEdit'])
                @endif

            @else

                {{-- ══ Exibição (preview) ══ --}}
                @include('livewire.admin.partials.lesson-block-preview', ['block' => $block, 'calloutStyles' => $calloutStyles])

            @endif
        </div>
    </div>
    @empty
    <div class="tu-card p-12 text-center">
        <p class="text-gray-400 text-sm">Esta aula ainda não tem conteúdo. Adicione um bloco abaixo.</p>
    </div>
    @endforelse

    {{-- ── Adicionar bloco ── --}}
    <div class="tu-card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50">
            <p class="text-sm font-semibold text-gray-600">Adicionar conteúdo</p>
        </div>

        @if(!$adding)
        {{-- Botões de escolha de tipo, agrupados --}}
        <div class="p-5 space-y-4">

            @php
                $addBtns = [
                    'Texto & mídia' => [
                        ['rich', '🎨', 'Texto rico', 'border-indigo-200 text-indigo-700 hover:bg-indigo-50'],
                        ['image', '🖼️', 'Imagem', 'border-teal-200 text-teal-700 hover:bg-teal-50'],
                        ['pdf', '📄', 'Anexar PDF', 'border-red-200 text-red-700 hover:bg-red-50'],
                        ['video', '🎬', 'Vídeo externo', 'border-purple-200 text-purple-700 hover:bg-purple-50'],
                        ['video_placeholder', '🎥', 'Vídeo em breve', 'border-purple-200 text-purple-400 hover:bg-purple-50'],
                    ],
                    'Destaques' => [
                        ['callout', '💡', 'Destaque', 'border-amber-200 text-amber-700 hover:bg-amber-50'],
                        ['quote', '❝', 'Citação', 'border-slate-200 text-slate-700 hover:bg-slate-50'],
                    ],
                    'Interativo' => [
                        ['comparison', '⚖️', 'Comparativo', 'border-fuchsia-200 text-fuchsia-700 hover:bg-fuchsia-50'],
                        ['flashcards', '🃏', 'Flashcards', 'border-cyan-200 text-cyan-700 hover:bg-cyan-50'],
                        ['accordion', '📂', 'Acordeão', 'border-lime-200 text-lime-700 hover:bg-lime-50'],
                    ],
                    'O aluno responde' => [
                        ['reflection', '✍️', 'Reflexão', 'border-emerald-200 text-emerald-700 hover:bg-emerald-50'],
                        ['scale', '📊', 'Autoavaliação', 'border-orange-200 text-orange-700 hover:bg-orange-50'],
                    ],
                ];
            @endphp
            @foreach($addBtns as $groupLabel => $buttons)
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">{{ $groupLabel }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($buttons as [$btnType, $btnIcon, $btnLabel, $btnClasses])
                    <button wire:click="startAdding('{{ $btnType }}')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 {{ $btnClasses }} text-sm font-semibold">
                        <span>{{ $btnIcon }}</span> {{ $btnLabel }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        @elseif($adding === 'text')
        <div class="p-5 space-y-3">
            <label class="block text-sm font-semibold text-gray-700">Conteúdo do texto</label>
            <textarea wire:model="newText" rows="8" autofocus placeholder="Digite o texto da aula aqui..."
                      class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y font-mono"></textarea>
            @error('newText') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                <button wire:click="addText" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">Adicionar bloco</button>
                <button wire:click="cancelAdding" class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
            </div>
        </div>

        @elseif($adding === 'video')
        <div class="p-5 space-y-3">
            <label class="block text-sm font-semibold text-gray-700">URL do vídeo</label>
            <p class="text-xs text-gray-400">Suporte para YouTube e Vimeo.</p>
            <input type="url" wire:model="newVideoUrl" autofocus placeholder="https://www.youtube.com/watch?v=..."
                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
            @error('newVideoUrl') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                <button wire:click="addVideo" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">Adicionar vídeo</button>
                <button wire:click="cancelAdding" class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
            </div>
        </div>

        @elseif($adding === 'pdf')
        <div class="p-5 space-y-3">
            <label class="block text-sm font-semibold text-gray-700">Arquivo PDF</label>
            <p class="text-xs text-gray-400">Máximo 20 MB.</p>
            <input type="file" wire:model="newPdf" accept=".pdf"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
            <div wire:loading wire:target="newPdf" class="text-xs text-gray-400">Carregando...</div>
            @error('newPdf') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            <div class="flex gap-2">
                <button wire:click="addPdf" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">Anexar PDF</button>
                <button wire:click="cancelAdding" class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
            </div>
        </div>

        @elseif($adding === 'image')
        <div class="p-5 space-y-3">
            <label class="block text-sm font-semibold text-gray-700">Imagem</label>
            <p class="text-xs text-gray-400">JPG, PNG ou WEBP. Máximo 5 MB.</p>
            <input type="file" wire:model="bufImage" accept="image/*"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
            <div wire:loading wire:target="bufImage" class="text-xs text-gray-400">Carregando...</div>
            @error('bufImage') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            @if($bufImage)
            <img src="{{ $bufImage->temporaryUrl() }}" class="max-h-48 rounded-xl border border-gray-100">
            @endif
            <label class="block text-sm font-semibold text-gray-700">Legenda (opcional)</label>
            <input type="text" wire:model="bufCaption" placeholder="Legenda da imagem"
                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
            <div class="flex gap-2">
                <button wire:click="addImage" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">Adicionar imagem</button>
                <button wire:click="cancelAdding" class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
            </div>
        </div>

        @elseif($adding === 'video_placeholder')
        <div class="p-5">@include('livewire.admin.partials.lesson-editor-video-placeholder-form', ['saveAction' => 'addVideoPlaceholder'])</div>

        @elseif($adding === 'rich')
        <div class="p-5">@include('livewire.admin.partials.lesson-editor-rich-form', ['saveAction' => 'addRich'])</div>

        @elseif($adding === 'callout')
        <div class="p-5">@include('livewire.admin.partials.lesson-editor-callout-form', ['saveAction' => 'addCallout', 'calloutStyles' => $calloutStyles])</div>

        @elseif($adding === 'quote')
        <div class="p-5">@include('livewire.admin.partials.lesson-editor-quote-form', ['saveAction' => 'addQuote'])</div>

        @elseif($adding === 'comparison')
        <div class="p-5">@include('livewire.admin.partials.lesson-editor-comparison-form', ['saveAction' => 'addComparison'])</div>

        @elseif($adding === 'flashcards')
        <div class="p-5">@include('livewire.admin.partials.lesson-editor-flashcards-form', ['saveAction' => 'addFlashcards'])</div>

        @elseif($adding === 'accordion')
        <div class="p-5">@include('livewire.admin.partials.lesson-editor-accordion-form', ['saveAction' => 'addAccordion'])</div>

        @elseif($adding === 'reflection')
        <div class="p-5">@include('livewire.admin.partials.lesson-editor-reflection-form', ['saveAction' => 'addReflection'])</div>

        @elseif($adding === 'scale')
        <div class="p-5">@include('livewire.admin.partials.lesson-editor-scale-form', ['saveAction' => 'addScale'])</div>
        @endif
    </div>

</div>
