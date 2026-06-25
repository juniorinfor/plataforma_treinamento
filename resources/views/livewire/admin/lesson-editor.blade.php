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
    <div class="tu-card overflow-hidden" wire:key="block-{{ $block->id }}">

        {{-- Header do bloco --}}
        <div class="flex items-center justify-between gap-2 px-4 py-2.5 bg-gray-50 border-b border-gray-100">
            <span class="text-xs font-bold uppercase tracking-wider
                {{ $block->type === 'text'  ? 'text-blue-600' : '' }}
                {{ $block->type === 'video' ? 'text-purple-600' : '' }}
                {{ $block->type === 'pdf'   ? 'text-red-500' : '' }}">
                @if($block->type === 'text')  📝 Texto
                @elseif($block->type === 'video') 🎬 Vídeo
                @elseif($block->type === 'pdf')   📄 PDF
                @endif
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

                {{-- Formulário de edição --}}
                @if($block->type === 'video')
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">URL do vídeo (YouTube ou Vimeo)</label>
                    <input type="url" wire:model="editVideoUrl" placeholder="https://www.youtube.com/watch?v=..."
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @error('editVideoUrl') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    <div class="flex gap-2">
                        <button wire:click="saveEdit" class="px-4 py-2 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">Salvar</button>
                        <button wire:click="cancelEdit" class="px-4 py-2 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
                    </div>
                </div>
                @else
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">Conteúdo do texto</label>
                    <textarea wire:model="editContent" rows="8" placeholder="Digite o conteúdo..."
                              class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-y font-mono"></textarea>
                    @error('editContent') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    <div class="flex gap-2">
                        <button wire:click="saveEdit" class="px-4 py-2 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">Salvar</button>
                        <button wire:click="cancelEdit" class="px-4 py-2 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
                    </div>
                </div>
                @endif

            @elseif($block->type === 'text')

                {{-- Exibição de texto --}}
                <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap">{{ $block->content }}</div>

            @elseif($block->type === 'video')

                {{-- Embed de vídeo --}}
                @php $embedUrl = $block->settings['embed_url'] ?? null; @endphp
                @if($embedUrl)
                <div class="rounded-xl overflow-hidden bg-black aspect-video">
                    <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
                <p class="mt-2 text-xs text-gray-400 truncate">{{ $block->content }}</p>
                @else
                <p class="text-sm text-gray-400">URL inválida: {{ $block->content }}</p>
                @endif

            @elseif($block->type === 'pdf')

                {{-- Exibição de PDF --}}
                @php
                    $filename = $block->settings['filename'] ?? basename($block->content);
                    $size     = $block->settings['size'] ?? null;
                    $kb       = $size ? round($size / 1024) : null;
                @endphp
                <div class="flex items-center gap-4 p-3 rounded-xl bg-red-50 border border-red-100">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $filename }}</p>
                        @if($kb) <p class="text-xs text-gray-400">{{ $kb }} KB</p> @endif
                    </div>
                    <a href="{{ asset('storage/' . $block->content) }}" target="_blank"
                       class="ml-auto text-sm font-semibold text-red-600 hover:text-red-700 shrink-0">
                        Visualizar
                    </a>
                </div>

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
        {{-- Botões de escolha de tipo --}}
        <div class="p-5 flex flex-wrap gap-3">
            <button wire:click="startAdding('text')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-blue-200 text-blue-700 hover:bg-blue-50 text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                Bloco de texto
            </button>
            <button wire:click="startAdding('video')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-purple-200 text-purple-700 hover:bg-purple-50 text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Vídeo externo
            </button>
            <button wire:click="startAdding('pdf')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-red-200 text-red-700 hover:bg-red-50 text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Anexar PDF
            </button>
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
        @endif
    </div>

</div>
