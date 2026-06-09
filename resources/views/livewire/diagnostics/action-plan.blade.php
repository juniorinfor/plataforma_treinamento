@php
    $assessment = $this->assessment;
    $tool       = $assessment->tool;
    $plan       = $this->plan;
    $color      = $tool->color ?? '#6366F1';
    $progress   = $plan ? $plan->progressPercent() : 0;
@endphp

<div class="space-y-6 animate-fade-in">

    {{-- ── Navegação ── --}}
    <a href="{{ route('diagnostics.result', $assessment) }}" wire:navigate
       class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar ao resultado
    </a>

    {{-- ── Header + Progresso Global ── --}}
    <div class="tu-card p-6" style="border-top: 4px solid {{ $color }}">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">

            {{-- Ícone + título --}}
            <div class="flex items-center gap-4 flex-1">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shrink-0"
                     style="background: {{ $color }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-gray-900">Plano de Ação</h1>
                    <p class="text-sm text-gray-500">{{ $tool->name }} · {{ $assessment->completed_at?->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Progresso circular --}}
            <div class="flex items-center gap-4 shrink-0">
                <div class="text-right">
                    <p class="text-3xl font-black" style="color: {{ $color }}">{{ $progress }}%</p>
                    <p class="text-xs text-gray-400">
                        {{ $plan?->items_done ?? 0 }} de {{ $plan?->items_total ?? 0 }} concluídos
                    </p>
                </div>

                {{-- Mini status badge --}}
                @if($plan)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                    @if($plan->status === 'completed') bg-emerald-50 text-emerald-700
                    @elseif($plan->status === 'in_progress') bg-amber-50 text-amber-700
                    @else bg-gray-100 text-gray-500 @endif">
                    @if($plan->status === 'completed')
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Concluído!
                    @elseif($plan->status === 'in_progress')
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        Em andamento
                    @else
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                        Não iniciado
                    @endif
                </span>
                @endif
            </div>
        </div>

        {{-- Barra de progresso --}}
        <div class="mt-5">
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-700"
                     style="width: {{ $progress }}%; background: linear-gradient(90deg, {{ $color }}, {{ $color }}bb)">
                </div>
            </div>
        </div>

        @if($plan?->status === 'completed')
        <div class="mt-4 flex items-center gap-2 text-sm text-emerald-600 bg-emerald-50 px-4 py-2.5 rounded-xl">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-semibold">Parabéns! Você completou todos os itens do plano.</span>
            <span class="text-emerald-500">Isso é o que diferencia organizações de alto desempenho!</span>
        </div>
        @endif
    </div>

    {{-- ── Guia rápido de ícones ── --}}
    <div class="flex flex-wrap gap-3 text-xs text-gray-500">
        @foreach([
            ['action',     '#10B981', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'Ação'],
            ['course',     '#6366F1', 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z', 'Treinamento'],
            ['reading',    '#F59E0B', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'Leitura'],
            ['reflection', '#8B5CF6', 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'Reflexão'],
        ] as [$type, $c, $path, $label])
        <span class="flex items-center gap-1.5 bg-white border border-gray-100 px-2.5 py-1 rounded-full shadow-sm">
            <svg class="w-3.5 h-3.5" style="color:{{ $c }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
            </svg>
            {{ $label }}
        </span>
        @endforeach
    </div>

    {{-- ── Seções por índice / dimensão ── --}}
    @if(!$plan || $plan->items->isEmpty())
    <div class="tu-card p-10 text-center text-gray-400">
        <p class="font-semibold">Nenhum item de plano encontrado.</p>
        <p class="text-sm mt-1">O plano é gerado automaticamente ao concluir o diagnóstico.</p>
    </div>
    @else

    @foreach($this->groupedItems as $group)
    @php
        $gc   = $group['color'];
        $done = $group['items']->where('status', 'done')->count();
        $total = $group['items']->count();
        $gpct = $total > 0 ? round(($done / $total) * 100) : 0;
    @endphp
    <div class="tu-card overflow-hidden">

        {{-- Cabeçalho da seção --}}
        <div class="px-5 py-4 flex items-center gap-3 border-b border-gray-50"
             style="background: {{ $gc }}08">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-black shrink-0"
                 style="background: {{ $gc }}">
                {{ $group['code'] ?: substr($group['label'], 0, 2) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-gray-900 truncate">{{ $group['label'] }}</p>
                @if($group['score'] !== null)
                <p class="text-xs text-gray-400">
                    Score: <span class="font-semibold" style="color:{{ $gc }}">{{ number_format($group['score'], 0) }}</span> / 100
                    @php $lbl = match(true) { $group['score'] >= 85 => 'Excelente', $group['score'] >= 70 => 'Saudável', $group['score'] >= 55 => 'Atenção', $group['score'] >= 40 => 'Crítico', default => 'Vulnerável' }; @endphp
                    · <span style="color:{{ $gc }}">{{ $lbl }}</span>
                </p>
                @endif
            </div>
            <div class="text-right shrink-0">
                <p class="text-sm font-bold" style="color:{{ $gc }}">{{ $done }}/{{ $total }}</p>
                <p class="text-xs text-gray-400">itens</p>
            </div>
        </div>

        {{-- Barra de progresso da seção --}}
        <div class="w-full bg-gray-100 h-1">
            <div class="h-1 transition-all duration-500" style="width:{{ $gpct }}%; background:{{ $gc }}"></div>
        </div>

        {{-- Lista de itens --}}
        <div class="divide-y divide-gray-50">
            @foreach($group['items'] as $item)
            @php
                $isDone = $item->status === 'done';
                $typeColors = [
                    'action'     => '#10B981',
                    'course'     => '#6366F1',
                    'reading'    => '#F59E0B',
                    'reflection' => '#8B5CF6',
                ];
                $ic = $typeColors[$item->type] ?? '#6B7280';
                $typePaths = [
                    'action'     => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                    'course'     => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                    'reading'    => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'reflection' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                ];
                $tp = $typePaths[$item->type] ?? $typePaths['action'];
            @endphp
            <div class="flex items-start gap-4 px-5 py-4 transition-colors {{ $isDone ? 'bg-gray-50/60' : 'hover:bg-gray-50/40' }}"
                 wire:key="item-{{ $item->id }}">

                {{-- Checkbox --}}
                <button wire:click="toggleItem({{ $item->id }})"
                        class="mt-0.5 shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-200
                               {{ $isDone ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 hover:border-emerald-400' }}">
                    @if($isDone)
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                    @endif
                </button>

                {{-- Ícone do tipo --}}
                <div class="shrink-0 mt-0.5">
                    <svg class="w-4 h-4 {{ $isDone ? 'opacity-40' : '' }}" style="color:{{ $ic }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $tp }}"/>
                    </svg>
                </div>

                {{-- Conteúdo --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold {{ $isDone ? 'line-through text-gray-400' : 'text-gray-800' }}">
                        {{ $item->title }}
                    </p>
                    @if($item->description)
                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed {{ $isDone ? 'opacity-60' : '' }}">
                        {{ $item->description }}
                    </p>
                    @endif

                    {{-- Botão "Acessar curso" --}}
                    @if($item->course)
                    <a href="{{ route('courses.show', $item->course->slug) }}" wire:navigate
                       class="inline-flex items-center gap-1.5 mt-2 text-xs font-semibold text-indigo-600 hover:text-indigo-700
                              bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Acessar treinamento
                    </a>
                    @endif

                    {{-- Badge tipo --}}
                    <span class="inline-block mt-1.5 text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded"
                          style="background:{{ $ic }}15; color:{{ $ic }}">
                        {{ $item->typeLabel() }}
                    </span>
                </div>

                {{-- Data de conclusão --}}
                @if($isDone && $item->completed_at)
                <div class="shrink-0 text-right text-[10px] text-gray-400">
                    <p>Feito</p>
                    <p class="font-semibold">{{ $item->completed_at->format('d/m') }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @endif

    {{-- ── Rodapé de ações ── --}}
    <div class="flex flex-col sm:flex-row gap-3 pb-8">
        <a href="{{ route('diagnostics.result', $assessment) }}" wire:navigate
           class="flex-1 tu-btn py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200
                  text-gray-700 hover:bg-gray-50 text-center">
            ← Ver resultado completo
        </a>
        <a href="{{ route('courses.index') }}" wire:navigate
           class="flex-1 tu-btn py-2.5 text-sm font-semibold rounded-xl text-white text-center shadow-sm"
           style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}cc)">
            Explorar todos os treinamentos
        </a>
    </div>

</div>
