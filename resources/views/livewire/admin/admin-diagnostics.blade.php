@php
    $s = $this->summary;
    $labelColors = [
        'Excelente'  => '#10B981',
        'Saudável'   => '#3B82F6',
        'Atenção'    => '#F59E0B',
        'Crítico'    => '#EF4444',
        'Vulnerável' => '#6B7280',
    ];
    $avgColor = match(true) {
        $s['avg'] >= 85 => '#10B981',
        $s['avg'] >= 70 => '#3B82F6',
        $s['avg'] >= 55 => '#F59E0B',
        $s['avg'] >= 40 => '#EF4444',
        default         => '#6B7280',
    };
@endphp

<div class="space-y-6 animate-fade-in">

    {{-- ── Header ── --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" wire:navigate
           class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Relatório de Diagnósticos</h1>
            <p class="text-sm text-gray-500 mt-0.5">Resultados consolidados de todos os colaboradores</p>
        </div>
    </div>

    {{-- ── Cards de resumo ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total --}}
        <div class="tu-card p-5 text-center">
            <p class="text-3xl font-black text-indigo-600">{{ $s['total'] }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">Diagnósticos realizados</p>
        </div>

        {{-- Média AS SCORE --}}
        <div class="tu-card p-5 text-center">
            <p class="text-3xl font-black" style="color:{{ $avgColor }}">{{ $s['avg'] }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">Score médio</p>
        </div>

        {{-- Melhor resultado --}}
        <div class="tu-card p-5 text-center">
            @php $best = ($s['dist']['Excelente'] ?? 0) + ($s['dist']['Saudável'] ?? 0); @endphp
            <p class="text-3xl font-black text-emerald-600">{{ $best }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">Excelente + Saudável</p>
        </div>

        {{-- Em atenção --}}
        <div class="tu-card p-5 text-center">
            @php $atRisk = ($s['dist']['Atenção'] ?? 0) + ($s['dist']['Crítico'] ?? 0) + ($s['dist']['Vulnerável'] ?? 0); @endphp
            <p class="text-3xl font-black {{ $atRisk > 0 ? 'text-amber-600' : 'text-gray-300' }}">{{ $atRisk }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wide">Precisam de atenção</p>
        </div>
    </div>

    {{-- ── Distribuição de labels ── --}}
    @if($s['total'] > 0)
    <div class="tu-card p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Distribuição de resultados</p>
        <div class="flex items-end gap-2 h-10">
            @foreach(['Excelente','Saudável','Atenção','Crítico','Vulnerável'] as $lbl)
            @php
                $cnt = $s['dist'][$lbl] ?? 0;
                $pct = $s['total'] > 0 ? round(($cnt/$s['total'])*100) : 0;
                $lc  = $labelColors[$lbl];
            @endphp
            @if($cnt > 0)
            <div class="flex flex-col items-center gap-1" style="flex:{{ $pct }}; min-width: 32px">
                <div class="w-full rounded-t text-center text-[10px] font-bold text-white flex items-end justify-center pb-1"
                     style="height: 40px; background:{{ $lc }}">
                    {{ $cnt }}
                </div>
                <span class="text-[10px] text-gray-500 truncate w-full text-center">{{ $lbl }}</span>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Filtros ── --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Filtrar por colaborador..."
                   class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300">
        </div>
        <select wire:model.live="filterTool"
                class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
            <option value="">Todas as ferramentas</option>
            @foreach($this->tools as $t)
            <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterLabel"
                class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
            <option value="">Todos os resultados</option>
            @foreach(['Excelente','Saudável','Atenção','Crítico','Vulnerável'] as $lbl)
            <option value="{{ $lbl }}">{{ $lbl }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── Cards de assessment ── --}}
    @if($this->assessments->isEmpty())
    <div class="tu-card p-10 text-center text-gray-400 text-sm">
        Nenhum diagnóstico encontrado com os filtros aplicados.
    </div>
    @else
    <div class="space-y-3">
        @foreach($this->assessments as $a)
        @php
            $tc     = $a->tool?->color ?? '#6366F1';
            $score  = (float) $a->global_score;
            $lc     = $labelColors[$a->global_label] ?? '#6B7280';
            $plan   = $a->actionPlan;
            $pct    = $plan?->progressPercent() ?? 0;
        @endphp
        <div class="tu-card p-5" wire:key="assess-{{ $a->id }}">
            <div class="flex items-start gap-4 flex-wrap sm:flex-nowrap">

                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold shrink-0">
                    {{ substr($a->user?->name ?? '?', 0, 2) }}
                </div>

                {{-- Info principal --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-bold text-gray-900">{{ $a->user?->name ?? '—' }}</p>
                        <span class="text-xs text-gray-400">·</span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $tc }}"></span>
                            <span class="text-xs text-gray-500">{{ $a->tool?->name }}</span>
                        </div>
                        <span class="text-xs text-gray-400">· {{ $a->completed_at?->format('d/m/Y') }}</span>
                    </div>

                    {{-- Mini barras por índice --}}
                    @if($a->results->isNotEmpty())
                    <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-x-4 gap-y-2">
                        @foreach($a->results as $r)
                        @php
                            $rc    = $r->componentTool?->color ?? $r->dimension?->color ?? $tc;
                            $rname = $r->componentTool?->code   ?? $r->dimension?->code   ?? substr($r->componentTool?->name ?? $r->dimension?->name ?? '?', 0, 3);
                            $rs    = (float) $r->normalized_score;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="text-[10px] font-bold" style="color:{{ $rc }}">{{ $rname }}</span>
                                <span class="text-[10px] font-black text-gray-700">{{ number_format($rs, 0) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full" style="width:{{ $rs }}%; background:{{ $rc }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Score global + plano --}}
                <div class="flex flex-col items-end gap-2 shrink-0">
                    <div class="text-center">
                        <p class="text-3xl font-black" style="color:{{ $lc }}">{{ number_format($score, 0) }}</p>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                              style="background:{{ $lc }}18; color:{{ $lc }}">{{ $a->global_label }}</span>
                    </div>

                    {{-- Progresso do plano --}}
                    @if($plan)
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <div class="w-20 bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-indigo-400" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="font-medium">{{ $pct }}% do plano</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Paginação --}}
    @if($this->assessments->hasPages())
    <div>{{ $this->assessments->links() }}</div>
    @endif

    @endif

</div>
