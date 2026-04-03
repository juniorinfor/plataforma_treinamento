<div class="max-w-lg mx-auto py-6 animate-fade-in">
    <h1 class="text-2xl font-bold text-gray-900 text-center mb-2">Mapa de Progresso</h1>
    <p class="text-gray-500 text-center mb-8">Sua jornada de aprendizado</p>

    @forelse($enrollments as $enrollment)
    <div class="mb-12">
        <div class="text-center mb-6">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold"
                  style="background: {{ $enrollment->course->category?->color ?? '#3B82F6' }}15; color: {{ $enrollment->course->category?->color ?? '#3B82F6' }}">
                {{ $enrollment->course->title }}
            </span>
        </div>

        <div class="relative">
            {{-- Winding Path SVG --}}
            <svg class="absolute left-1/2 -translate-x-1/2 top-0 h-full w-32 opacity-20" viewBox="0 0 100 800" fill="none">
                <path d="M50 0 Q80 50 50 100 Q20 150 50 200 Q80 250 50 300 Q20 350 50 400 Q80 450 50 500 Q20 550 50 600" stroke="#94A3B8" stroke-width="3" stroke-dasharray="8 8"/>
            </svg>

            <div class="relative space-y-6">
                @foreach($enrollment->course->modules as $mi => $module)
                    {{-- Module Header --}}
                    <div class="text-center">
                        <span class="inline-block px-3 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $module->title }}</span>
                    </div>

                    @foreach($module->lessons as $li => $lesson)
                    @php
                        $isFirst = $mi === 0 && $li === 0;
                        $nodeClass = $isFirst ? 'current' : ($li < 2 && $mi === 0 ? 'completed' : 'locked');
                        $offset = ($li % 2 === 0) ? 'ml-8' : 'mr-8 ml-auto';
                    @endphp
                    <div class="flex items-center gap-4 {{ $offset }} max-w-[280px] animate-slide-up" style="animation-delay: {{ ($mi * 3 + $li) * 80 }}ms">
                        <div class="tu-map-node {{ $nodeClass }} shrink-0">
                            @if($nodeClass === 'completed')
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @elseif($nodeClass === 'current')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            @endif
                        </div>
                        <div class="tu-card p-3 flex-1 {{ $nodeClass === 'locked' ? 'opacity-50' : '' }}">
                            <h4 class="text-sm font-semibold text-gray-900">{{ $lesson->title }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-gray-400">{{ $lesson->type->label() }}</span>
                                <span class="text-xs font-bold" style="color: var(--tu-xp)">+{{ $lesson->xp_reward }} XP</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
    @empty
    <div class="tu-card p-12 text-center">
        <svg class="w-20 h-20 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
        <h3 class="text-lg font-semibold text-gray-700">Nenhum curso em andamento</h3>
        <p class="text-gray-400 mt-1">Inscreva-se em um curso para ver seu mapa de progresso</p>
        <a href="{{ route('courses.index') }}" class="tu-btn tu-btn-primary mt-4" wire:navigate>Explorar Cursos</a>
    </div>
    @endforelse
</div>