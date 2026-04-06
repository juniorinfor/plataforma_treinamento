<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Catalogo de Cursos</h1>
            <p class="text-gray-500 mt-1">Explore e inscreva-se nos cursos disponiveis</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
        <button wire:click="$set('tab', 'all')" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $tab === 'all' ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">Todos</button>
        <button wire:click="$set('tab', 'company')" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $tab === 'company' ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">Da Empresa</button>
        <button wire:click="$set('tab', 'platform')" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $tab === 'platform' ? 'bg-white shadow text-blue-600' : 'text-gray-500 hover:text-gray-700' }}">Plataforma</button>
        <button wire:click="$set('tab', 'mandatory')" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $tab === 'mandatory' ? 'bg-white shadow text-red-600' : 'text-gray-500 hover:text-gray-700' }}">Obrigatorios</button>
    </div>

    {{-- Search --}}
    <div class="relative max-w-md">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar cursos..."
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none text-sm">
    </div>

    {{-- Course Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 stagger-children">

        {{-- Curso Personalizado: OnBoarding LeveMente --}}
        @if($tab === 'all' || $tab === 'company')
        <a href="{{ route('curso.levemente.onboarding') }}" target="_blank"
           class="tu-card overflow-hidden hover-lift block animate-slide-up relative">
            <div class="h-36 flex items-center justify-center text-white text-5xl font-bold relative overflow-hidden"
                 style="background: linear-gradient(135deg, #2A5A42, #3D7A5E, #5A9E7C);">
                🌿
                <span class="absolute top-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/20 backdrop-blur text-white">PERSONALIZADO</span>
                <div style="position:absolute;bottom:-20px;right:-20px;width:80px;height:80px;background:rgba(244,162,97,0.2);border-radius:50%;"></div>
            </div>
            <div class="p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background:#F0FAF515;color:#3D7A5E;border:1px solid #D4E8DC;">
                        🍃 Alimentação
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                        Iniciante
                    </span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">OnBoarding — Grupo LeveMente</h3>
                <p class="text-xs text-gray-500 line-clamp-2">Conheça a história, missão, valores e estrutura do Grupo LeveMente. Com quiz interativo e certificado.</p>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                    <div class="flex items-center gap-3 text-xs text-gray-400">
                        <span>~20min</span>
                        <span>5 módulos</span>
                    </div>
                    <span class="text-xs font-bold" style="color: var(--tu-xp)">+250 XP</span>
                </div>
            </div>
        </a>
        @endif

        @forelse($courses as $course)
        <a href="{{ route('courses.show', $course->slug) }}" class="tu-card overflow-hidden hover-lift block animate-slide-up" wire:navigate>
            <div class="h-36 flex items-center justify-center text-white text-5xl font-bold relative"
                 style="background: linear-gradient(135deg, {{ $course->category?->color ?? '#3B82F6' }}, {{ $course->category?->color ?? '#3B82F6' }}aa)">
                {{ substr($course->title, 0, 1) }}
                @if($course->is_platform_course)
                <span class="absolute top-2 right-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/20 backdrop-blur text-white">TRAINUP</span>
                @endif
            </div>
            <div class="p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background: {{ $course->category?->color ?? '#3B82F6' }}15; color: {{ $course->category?->color ?? '#3B82F6' }}">
                        {{ $course->category?->name ?? 'Geral' }}
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $course->difficulty->value === 'beginner' ? 'bg-emerald-50 text-emerald-600' : ($course->difficulty->value === 'intermediate' ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-600') }}">
                        {{ $course->difficulty->label() }}
                    </span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ $course->title }}</h3>
                <p class="text-xs text-gray-500 line-clamp-2">{{ $course->short_description ?? $course->description }}</p>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                    <div class="flex items-center gap-3 text-xs text-gray-400">
                        <span>{{ $course->estimated_hours }}h</span>
                        <span>{{ $course->modules_count ?? $course->modules()->count() }} modulos</span>
                    </div>
                    <span class="text-xs font-bold" style="color: var(--tu-xp)">+{{ $course->xp_reward }} XP</span>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full tu-card p-12 text-center">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <h3 class="text-lg font-semibold text-gray-700">Nenhum curso encontrado</h3>
            <p class="text-gray-400 mt-1">Tente ajustar seus filtros de busca</p>
        </div>
        @endforelse
    </div>
</div>