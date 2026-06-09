<div class="space-y-6 animate-fade-in">
    {{-- Welcome + Stats Row --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ola, {{ explode(' ', $user->name)[0] }}! 👋</h1>
            <p class="text-gray-500 mt-1">Continue sua jornada de aprendizado</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Streak Card --}}
            <div class="tu-card px-4 py-2.5 flex items-center gap-2">
                <div class="tu-streak-fire {{ ($user->current_streak ?? 0) > 0 ? '' : 'inactive' }}">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900">{{ $user->current_streak ?? 0 }}</p>
                    <p class="text-xs text-gray-500 -mt-0.5">dias seguidos</p>
                </div>
            </div>
            {{-- XP Card --}}
            <div class="tu-card px-4 py-2.5 flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($points->total_xp ?? 0) }}</p>
                    <p class="text-xs text-gray-500 -mt-0.5">XP total</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Level Progress --}}
    @if($points)
    <div class="tu-card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <span class="tu-level-badge">
                    Nv. {{ $points->currentLevel?->level_number ?? 1 }} - {{ $points->currentLevel?->name ?? 'Novato' }}
                </span>
                <span class="text-sm text-gray-500">{{ number_format($points->total_xp) }} / {{ number_format($points->currentLevel?->max_xp ?? 100) }} XP</span>
            </div>
            <span class="text-sm font-semibold" style="color: var(--tu-xp)">
                {{ $points->currentLevel?->max_xp ? number_format(($points->total_xp / $points->currentLevel->max_xp) * 100) : 100 }}%
            </span>
        </div>
        <div class="tu-xp-bar h-3">
            <div class="tu-xp-bar-fill animate-progress-fill" style="width: {{ $points->currentLevel?->max_xp ? min(($points->total_xp / $points->currentLevel->max_xp) * 100, 100) : 100 }}%"></div>
        </div>
    </div>
    @endif

    {{-- Diagnostic Banners (dinâmicos — mesma fonte da página Diagnósticos) --}}
    @if($diagnosticTools->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 stagger-children">
        @foreach($diagnosticTools as $tool)
        @php $c = $tool->color ?? '#6366F1'; @endphp
        <a href="{{ route('diagnostics.index') }}" wire:navigate
           class="relative overflow-hidden rounded-2xl p-6 hover-lift block animate-slide-up group text-white"
           style="background-color: {{ $c }}; background-image: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, rgba(0,0,0,0.28) 100%);">
            {{-- Decorative circles --}}
            <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full opacity-20"
                 style="background: rgba(255,255,255,0.3)"></div>
            <div class="absolute -right-2 bottom-4 w-20 h-20 rounded-full opacity-10"
                 style="background: rgba(255,255,255,0.5)"></div>

            {{-- Badge com ícone por tipo --}}
            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-widest
                         bg-white/20 px-2.5 py-1 rounded-full mb-3">
                @if($tool->icon === 'shield-check')
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                @elseif($tool->icon === 'user-circle')
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @else
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                @endif
                {{ $tool->type === \App\Enums\DiagnosticToolType::Composite ? 'AS SCORE®' : 'Diagnóstico' }}
            </span>

            <h3 class="font-bold text-lg leading-tight mb-1">{{ $tool->name }}</h3>
            <p class="text-white/80 text-sm mb-4 leading-snug">{{ $tool->short_description }}</p>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 text-white/80 text-xs">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $tool->estimated_minutes }} min
                    </span>
                    <span class="flex items-center gap-1 text-yellow-300 font-semibold">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        +{{ $tool->xp_reward }} XP
                    </span>
                </div>
                <span class="inline-flex items-center gap-1.5 bg-white text-gray-800 text-xs font-bold
                             px-3 py-1.5 rounded-full group-hover:bg-gray-50 transition-colors">
                    Iniciar
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Grid: Continue Learning + Daily Challenges --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Continue Learning --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Continue Aprendendo</h2>
                <a href="{{ route('courses.index') }}" class="text-sm text-blue-600 font-medium hover:text-blue-700" wire:navigate>Ver todos</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 stagger-children">
                @forelse($enrollments as $enrollment)
                <a href="{{ route('courses.show', $enrollment->course->slug) }}" class="tu-card p-4 hover-lift block animate-slide-up" wire:navigate>
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-white text-lg font-bold"
                             style="background: {{ $enrollment->course->category?->color ?? '#3B82F6' }}">
                            {{ substr($enrollment->course->title, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $enrollment->course->title }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $enrollment->course->difficulty->label() }}</p>
                            <div class="mt-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-500">{{ number_format($enrollment->progress_percentage) }}% concluido</span>
                                    <span class="text-xs font-semibold" style="color: var(--tu-success)">+{{ $enrollment->course->xp_reward }} XP</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all" style="width: {{ $enrollment->progress_percentage }}%; background: var(--tu-success)"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="sm:col-span-2 tu-card p-8 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <p class="text-gray-500">Voce ainda nao esta inscrito em nenhum curso</p>
                    <a href="{{ route('courses.index') }}" class="tu-btn tu-btn-primary mt-4" wire:navigate>Explorar Cursos</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="space-y-4">
            {{-- Daily Challenge --}}
            <div class="tu-card p-5 border-l-4" style="border-left-color: var(--tu-streak)">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5" style="color: var(--tu-streak)" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
                    Desafio Diario
                </h3>
                <p class="text-sm text-gray-600 mt-2">Complete 3 aulas hoje</p>
                <div class="mt-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">1 de 3 aulas</span>
                        <span class="font-bold" style="color: var(--tu-xp)">+30 XP</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full" style="width: 33%; background: var(--tu-streak)"></div>
                    </div>
                </div>
            </div>

            {{-- Recent Badges --}}
            <div class="tu-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900">Conquistas Recentes</h3>
                    <a href="{{ route('badges') }}" class="text-xs text-blue-600 font-medium" wire:navigate>Ver todas</a>
                </div>
                @if($recentBadges->count())
                <div class="grid grid-cols-4 gap-2">
                    @foreach($recentBadges as $badge)
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"
                             style="background: {{ $badge->color }}20; border: 2px solid {{ $badge->color }}">
                            🏆
                        </div>
                        <span class="text-[10px] text-gray-600 mt-1 leading-tight">{{ $badge->name }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <p class="text-sm text-gray-400">Complete atividades para ganhar badges!</p>
                </div>
                @endif
            </div>

            {{-- Mini Leaderboard --}}
            <div class="tu-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900">Top 5 da Semana</h3>
                    <a href="{{ route('leaderboard') }}" class="text-xs text-blue-600 font-medium" wire:navigate>Ranking</a>
                </div>
                <div class="space-y-3">
                    @php
                        $topUsers = \App\Models\UserPoints::where('company_id', auth()->user()->company_id)
                            ->with('user')
                            ->orderByDesc('weekly_xp')
                            ->take(5)->get();
                    @endphp
                    @foreach($topUsers as $i => $up)
                    <div class="flex items-center gap-3 {{ $up->user_id === auth()->id() ? 'bg-blue-50 -mx-2 px-2 py-1 rounded-lg' : '' }}">
                        <span class="w-5 text-center font-bold text-sm {{ $i === 0 ? 'text-yellow-500' : ($i === 1 ? 'text-gray-400' : ($i === 2 ? 'text-orange-400' : 'text-gray-400')) }}">{{ $i + 1 }}</span>
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center text-white text-xs font-bold">
                            {{ substr($up->user?->name ?? '?', 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $up->user?->name ?? 'Usuario' }}</p>
                        </div>
                        <span class="text-sm font-bold" style="color: var(--tu-xp)">{{ $up->weekly_xp }} XP</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Recommended Courses --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">Cursos Recomendados</h2>
            <a href="{{ route('courses.index') }}" class="text-sm text-blue-600 font-medium hover:text-blue-700" wire:navigate>Ver catalogo</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger-children">
            @foreach($availableCourses as $course)
            <a href="{{ route('courses.show', $course->slug) }}" class="tu-card overflow-hidden hover-lift block animate-slide-up" wire:navigate>
                <div class="h-32 flex items-center justify-center text-white text-4xl font-bold"
                     style="background: linear-gradient(135deg, {{ $course->category?->color ?? '#3B82F6' }}, {{ $course->category?->color ?? '#3B82F6' }}dd)">
                    {{ substr($course->title, 0, 1) }}
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background: {{ $course->category?->color ?? '#3B82F6' }}15; color: {{ $course->category?->color ?? '#3B82F6' }}">
                            {{ $course->category?->name ?? 'Geral' }}
                        </span>
                        @if($course->is_mandatory)
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-red-50 text-red-600">Obrigatorio</span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ $course->title }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $course->short_description }}</p>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-400">{{ $course->estimated_hours }}h</span>
                        <span class="text-xs font-bold" style="color: var(--tu-xp)">+{{ $course->xp_reward }} XP</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
