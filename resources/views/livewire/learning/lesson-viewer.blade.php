<div class="max-w-3xl mx-auto animate-fade-in">

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('courses.show', $lesson->module->course->slug) }}" wire:navigate
           class="flex items-center gap-2 text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span class="text-sm font-medium">{{ $lesson->module->course->title }}</span>
        </a>
        @if($lesson->xp_reward > 0)
        <span class="text-xs font-bold px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">
            +{{ $lesson->xp_reward }} XP
        </span>
        @endif
    </div>

    {{-- Card principal --}}
    <div class="tu-card p-6 sm:p-8 mb-6">
        <div class="flex items-start justify-between gap-3 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $lesson->title }}</h1>
            @php
                $typeColor = match($lesson->type->value) {
                    'video' => 'bg-purple-50 text-purple-600',
                    'pdf'   => 'bg-red-50 text-red-600',
                    'quiz'  => 'bg-amber-50 text-amber-600',
                    default => 'bg-blue-50 text-blue-600',
                };
            @endphp
            <span class="shrink-0 text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full {{ $typeColor }}">
                {{ $lesson->type->label() }}
            </span>
        </div>

        {{-- Blocos de conteúdo --}}
        @forelse($lesson->contents as $block)
        <div class="mb-6">
            @include('livewire.admin.partials.lesson-block-preview', [
                'block' => $block,
                'interactive' => true,
                'interactionAnswers' => $interactionAnswers,
                'interactionSaved' => $interactionSaved,
            ])
        </div>
        @empty
        <div class="py-8 text-center">
            <p class="text-gray-400 text-sm">O conteúdo desta aula ainda não foi adicionado.</p>
        </div>
        @endforelse
    </div>

    {{-- Botão de conclusão --}}
    <div class="tu-card p-5 mb-6 flex items-center justify-between gap-4">
        @if($completed)
        <div class="flex items-center gap-2 text-green-700">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm font-semibold">Aula concluída</span>
        </div>
        @else
        <p class="text-sm text-gray-500">Leu o conteúdo? Marque a aula como concluída.</p>
        <button wire:click="completeLesson"
                class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Marcar como concluída
        </button>
        @endif
    </div>

    {{-- Navegação anterior / próxima --}}
    <div class="flex items-center justify-between gap-3">
        {{-- Anterior --}}
        @if($prev)
        <a href="{{ $prev->type->value === 'quiz' ? route('quiz.play', $prev->id) : route('lesson.view', $prev->id) }}"
           wire:navigate
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 min-w-0">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span class="truncate">Anterior</span>
        </a>
        @else
        <span></span>
        @endif

        {{-- Próxima --}}
        @if($next)
        <a href="{{ $next->type->value === 'quiz' ? route('quiz.play', $next->id) : route('lesson.view', $next->id) }}"
           wire:navigate
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold min-w-0">
            <span class="truncate">Próxima</span>
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif
    </div>

</div>
