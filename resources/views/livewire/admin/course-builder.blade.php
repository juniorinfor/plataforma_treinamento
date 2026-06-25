<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">

    {{-- Voltar --}}
    <a href="{{ route('admin.courses') }}" wire:navigate
       class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar aos cursos
    </a>

    {{-- Cabeçalho --}}
    <div class="flex items-start justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $this->course->title }}</h1>
            <p class="text-gray-500 mt-1">Organize os módulos e as aulas do curso.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.courses.edit', $this->course->id) }}" wire:navigate
               class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">
                Editar info
            </a>
            <button wire:click="openModuleModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar módulo
            </button>
        </div>
    </div>

    {{-- Módulos --}}
    @forelse($this->modules as $mi => $module)
    <div class="tu-card overflow-hidden" wire:key="mod-{{ $module->id }}">
        {{-- Header do módulo --}}
        <div class="flex items-center justify-between gap-3 px-5 py-3.5 bg-gray-50 border-b border-gray-100">
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-xs font-bold text-gray-400">{{ $mi + 1 }}.</span>
                <h2 class="font-bold text-gray-900 truncate">{{ $module->title }}</h2>
                <span class="text-xs text-gray-400 shrink-0">({{ $module->lessons->count() }} aulas)</span>
            </div>
            <div class="flex items-center gap-1 shrink-0">
                <button wire:click="moveModule({{ $module->id }}, 'up')" @disabled($mi === 0)
                        class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30" title="Subir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                </button>
                <button wire:click="moveModule({{ $module->id }}, 'down')" @disabled($mi === $this->modules->count() - 1)
                        class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30" title="Descer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <button wire:click="openModuleModal({{ $module->id }})"
                        class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-500" title="Renomear">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button wire:click="deleteModule({{ $module->id }})"
                        wire:confirm="Excluir este módulo e todas as suas aulas?"
                        class="p-1.5 rounded-lg hover:bg-red-50 text-red-400" title="Excluir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Aulas --}}
        <div class="divide-y divide-gray-50">
            @forelse($module->lessons as $li => $lesson)
            <div class="flex items-center justify-between gap-3 px-5 py-3" wire:key="les-{{ $lesson->id }}">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full
                                 {{ $lesson->type->value === 'quiz' ? 'bg-amber-50 text-amber-600' : 'bg-indigo-50 text-indigo-600' }}">
                        {{ $lesson->type->label() }}
                    </span>
                    <span class="text-sm text-gray-800 truncate">{{ $lesson->title }}</span>
                    <span class="text-xs text-gray-400 shrink-0">{{ $lesson->duration_minutes }} min</span>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button wire:click="moveLesson({{ $module->id }}, {{ $lesson->id }}, 'up')" @disabled($li === 0)
                            class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 disabled:opacity-30" title="Subir">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    <button wire:click="moveLesson({{ $module->id }}, {{ $lesson->id }}, 'down')" @disabled($li === $module->lessons->count() - 1)
                            class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 disabled:opacity-30" title="Descer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    @if($lesson->type->value === 'quiz')
                    <a href="{{ route('admin.courses.quiz.builder', [$this->course->id, $lesson->id]) }}" wire:navigate
                       class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-500" title="Construtor de prova">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </a>
                    @else
                    <a href="{{ route('admin.courses.lesson.editor', [$this->course->id, $lesson->id]) }}" wire:navigate
                       class="p-1.5 rounded-lg hover:bg-indigo-50 text-indigo-500" title="Editar conteúdo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    </a>
                    @endif
                    <button wire:click="openLessonModal({{ $module->id }}, {{ $lesson->id }})"
                            class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500" title="Renomear aula">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="deleteLesson({{ $lesson->id }})" wire:confirm="Excluir esta aula?"
                            class="p-1.5 rounded-lg hover:bg-red-50 text-red-400" title="Excluir">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="px-5 py-4 text-sm text-gray-400">Nenhuma aula neste módulo ainda.</div>
            @endforelse

            <div class="px-5 py-3">
                <button wire:click="openLessonModal({{ $module->id }})"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Adicionar aula
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="tu-card p-12 text-center">
        <p class="text-gray-400 mb-4">Este curso ainda não tem módulos.</p>
        <button wire:click="openModuleModal" class="tu-btn tu-btn-primary">Adicionar o primeiro módulo</button>
    </div>
    @endforelse

    {{-- ── Modal: Módulo ── --}}
    @if($showModuleModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="$set('showModuleModal', false)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-lg font-bold text-gray-900">{{ $moduleId ? 'Renomear módulo' : 'Novo módulo' }}</h3>
            <div>
                <input type="text" wire:model="moduleTitle" wire:keydown.enter="saveModule" placeholder="Título do módulo" autofocus
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                @error('moduleTitle') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-end gap-2">
                <button wire:click="$set('showModuleModal', false)" class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button wire:click="saveModule" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">Salvar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Modal: Aula ── --}}
    @if($showLessonModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="$set('showLessonModal', false)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
            <h3 class="text-lg font-bold text-gray-900">{{ $lessonId ? 'Editar aula' : 'Nova aula' }}</h3>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título</label>
                <input type="text" wire:model="lessonTitle" placeholder="Título da aula"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                @error('lessonTitle') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipo</label>
                    <select wire:model="lessonType" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                        @foreach($this->lessonTypes() as $t)
                        <option value="{{ $t['value'] }}">{{ $t['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Duração (min)</label>
                    <input type="number" min="1" wire:model="lessonDuration" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @error('lessonDuration') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="text-xs text-gray-400">O conteúdo da aula (texto, vídeo, PDF) e as provas serão editados nas próximas etapas.</p>
            <div class="flex justify-end gap-2">
                <button wire:click="$set('showLessonModal', false)" class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button wire:click="saveLesson" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">Salvar</button>
            </div>
        </div>
    </div>
    @endif
</div>
