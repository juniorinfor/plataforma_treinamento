<div class="animate-fade-in">
    {{-- Hero --}}
    <div class="tu-card overflow-hidden mb-6">
        <div class="h-48 sm:h-56 flex items-end relative"
             style="background: linear-gradient(135deg, {{ $course->category?->color ?? '#3B82F6' }}, {{ $course->category?->color ?? '#3B82F6' }}99)">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="relative p-6 text-white">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-white/20 backdrop-blur">{{ $course->category?->name ?? 'Geral' }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-white/20 backdrop-blur">{{ $course->difficulty->label() }}</span>
                    @if($course->is_mandatory)<span class="text-xs px-2 py-0.5 rounded-full bg-red-500/80 backdrop-blur">Obrigatorio</span>@endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold">{{ $course->title }}</h1>
                <div class="flex items-center gap-4 mt-2 text-sm text-white/80">
                    <span>{{ $course->estimated_hours }}h de conteudo</span>
                    <span>{{ $course->modules->count() }} modulos</span>
                    <span>{{ $course->modules->sum(fn($m) => $m->lessons->count()) }} aulas</span>
                    <span class="font-bold text-yellow-300">+{{ $course->xp_reward }} XP</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            @if($enrollment)
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500">Seu progresso</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($enrollment->progress_percentage) }}%</p>
                </div>
                <a href="{{ $course->modules->first()?->lessons->first() ? route('lesson.view', $course->modules->first()->lessons->first()->id) : '#' }}" class="tu-btn tu-btn-primary tu-btn-lg">
                    Continuar Aprendendo
                </a>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="h-3 rounded-full animate-progress-fill" style="width: {{ $enrollment->progress_percentage }}%; background: var(--tu-success)"></div>
            </div>
            @else
            <div class="flex items-center justify-between">
                <p class="text-gray-600">{{ $course->description }}</p>
                <button wire:click="enroll" class="tu-btn tu-btn-primary tu-btn-lg shrink-0 ml-4">
                    Inscrever-se Grátis
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Modules --}}
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-gray-900">Conteudo do Curso</h2>
        @foreach($course->modules as $mi => $module)
        <div class="tu-card overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">{{ $mi + 1 }}</span>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $module->title }}</h3>
                        <p class="text-xs text-gray-500">{{ $module->lessons->count() }} aulas</p>
                    </div>
                </div>
                @if($module->xp_reward > 0)
                <span class="text-xs font-bold" style="color: var(--tu-xp)">+{{ $module->xp_reward }} XP</span>
                @endif
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($module->lessons as $li => $lesson)
                <a href="{{ $lesson->type->value === 'quiz' ? route('quiz.play', $lesson->id) : route('lesson.view', $lesson->id) }}"
                   class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors" wire:navigate>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $li === 0 && $mi === 0 ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                        @if($lesson->type->value === 'quiz')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($lesson->type->value === 'video')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">{{ $lesson->title }}</h4>
                        <div class="flex items-center gap-3 mt-0.5 text-xs text-gray-400">
                            <span>{{ $lesson->type->label() }}</span>
                            <span>{{ $lesson->duration_minutes }} min</span>
                        </div>
                    </div>
                    <span class="text-xs font-semibold" style="color: var(--tu-xp)">+{{ $lesson->xp_reward }} XP</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>