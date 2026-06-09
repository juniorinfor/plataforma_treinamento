@php
    $assessment = $this->assessment;
    $tool       = $assessment->tool;
    $chart      = $this->chartData;
    $isComplete = $assessment->status === \App\Enums\DiagnosticAssessmentStatus::Completed;
    $color      = $tool->color ?? '#6366F1';
@endphp

{{-- Injeta dados dos gráficos de forma segura --}}
<script>
    window.__diagChart = @json($chart);
</script>

<div class="space-y-6 animate-fade-in">

    {{-- Voltar --}}
    <a href="{{ route('diagnostics.index') }}" wire:navigate
       class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Diagnósticos
    </a>

    @if(!$isComplete)
    {{-- ═══ AGUARDANDO ANÁLISE ═══ --}}
    <div class="max-w-lg mx-auto tu-card p-10 text-center">
        <div class="w-20 h-20 rounded-full mx-auto mb-5 flex items-center justify-center"
             style="background: {{ $color }}15">
            <svg class="w-10 h-10" style="color: {{ $color }}" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                      d="M12 6v6l4 2M12 2a10 10 0 110 20A10 10 0 0112 2z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Diagnóstico enviado</h2>
        <p class="text-gray-500 text-sm leading-relaxed">
            Suas respostas foram recebidas. Você será notificado quando o resultado estiver disponível.
        </p>
        <span class="mt-4 inline-flex items-center gap-1.5 text-xs text-gray-400 bg-gray-50 px-3 py-1.5 rounded-full">
            Status: {{ $assessment->status->label() }}
        </span>
    </div>

    @else
    {{-- ═══ RESULTADO COMPLETO ═══ --}}

    {{-- ── Seção 1: Hero (gauge + info) ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Gauge --}}
        <div class="lg:col-span-2 tu-card p-6 flex flex-col items-center justify-center"
             wire:ignore
             x-data="{
                 init() {
                     const d = window.__diagChart;
                     new ApexCharts(this.$refs.gauge, {
                         chart: {
                             type: 'radialBar',
                             height: 280,
                             sparkline: { enabled: true },
                             animations: { enabled: true, speed: 900 }
                         },
                         series: [Math.round(d.globalScore)],
                         plotOptions: {
                             radialBar: {
                                 startAngle: -135,
                                 endAngle:   135,
                                 hollow: { size: '62%', background: 'transparent' },
                                 track: { background: '#f3f4f6', strokeWidth: '100%', margin: 4 },
                                 dataLabels: {
                                     name: {
                                         show: true, offsetY: 20,
                                         fontSize: '13px', color: '#9ca3af',
                                         fontFamily: 'Inter, sans-serif'
                                     },
                                     value: {
                                         offsetY: -14, fontSize: '42px', fontWeight: 800,
                                         color: d.globalColor, fontFamily: 'Inter, sans-serif',
                                         formatter: v => v
                                     }
                                 }
                             }
                         },
                         fill: {
                             type: 'gradient',
                             gradient: {
                                 shade: 'light', type: 'horizontal',
                                 shadeIntensity: 0.3,
                                 colorStops: [
                                     { offset: 0,   color: d.globalColor, opacity: 1 },
                                     { offset: 100, color: d.globalColor + 'aa', opacity: 1 }
                                 ]
                             }
                         },
                         colors: [d.globalColor],
                         labels: ['AS SCORE®'],
                         stroke: { lineCap: 'round' }
                     }).render();
                 }
             }">
            <div x-ref="gauge" class="w-full"></div>
            <div class="text-center -mt-4">
                <p class="text-2xl font-black" style="color: {{ $chart['globalColor'] }}">
                    {{ $chart['globalLabel'] }}
                </p>
                <p class="text-sm text-gray-400 mt-1">{{ $tool->name }}</p>
            </div>
        </div>

        {{-- Info + mini bars --}}
        <div class="lg:col-span-3 tu-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white"
                         style="background: {{ $color }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">Diagnóstico concluído</p>
                        <p class="text-xs text-gray-400">
                            {{ $assessment->completed_at?->format('d \d\e F \d\e Y') }}
                        </p>
                    </div>
                    <span class="ml-auto inline-flex items-center gap-1 text-xs font-bold bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Concluído
                    </span>
                </div>

                {{-- Mini bars por índice --}}
                <div class="space-y-3">
                    @foreach($assessment->results as $i => $result)
                    @php
                        $rc    = $result->componentTool?->color ?? $result->dimension?->color ?? $color;
                        $rname = $result->componentTool?->name ?? $result->dimension?->name ?? '—';
                        $rcode = $result->componentTool?->code ?? '';
                        $rscore = (float) $result->normalized_score;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold w-10 text-center px-1 py-0.5 rounded"
                                      style="background: {{ $rc }}18; color: {{ $rc }}">
                                    {{ $rcode ?: substr($rname,0,3) }}
                                </span>
                                <span class="text-xs text-gray-600 truncate max-w-[140px]">{{ $rname }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-xs font-semibold" style="color: {{ $rc }}">
                                    {{ $result->label }}
                                </span>
                                <span class="text-sm font-black text-gray-800">{{ number_format($rscore,0) }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-700"
                                 style="width: {{ $rscore }}%; background: {{ $rc }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- XP ganho --}}
            <div class="mt-5 pt-4 border-t border-gray-50 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="text-gray-600">Você ganhou</span>
                <span class="font-black text-yellow-600">+{{ $tool->xp_reward }} XP</span>
                <span class="text-gray-400">por completar este diagnóstico</span>
            </div>
        </div>
    </div>

    {{-- ── Seção 2: Radar chart ── --}}
    @if(count($chart['scores']) >= 3)
    <div class="tu-card p-6">
        <h2 class="font-bold text-gray-900 mb-1">Mapa de Índices — AS SCORE®</h2>
        <p class="text-sm text-gray-400 mb-4">Visão 360° dos 5 eixos da inteligência organizacional</p>

        <div wire:ignore
             x-data="{
                 init() {
                     const d = window.__diagChart;
                     new ApexCharts(this.$refs.radar, {
                         chart: {
                             type: 'radar',
                             height: 380,
                             toolbar: { show: false },
                             animations: { enabled: true, speed: 1000 },
                             dropShadow: { enabled: true, blur: 4, left: 1, top: 1, opacity: 0.12 }
                         },
                         series: [{
                             name: 'Sua organização',
                             data: d.scores
                         }],
                         labels: d.labels,
                         colors: ['{{ $color }}'],
                         fill: { opacity: 0.22 },
                         stroke: { width: 2.5, curve: 'smooth' },
                         markers: { size: 5, strokeWidth: 2, hover: { size: 7 } },
                         yaxis: { show: false, min: 0, max: 100 },
                         xaxis: {
                             labels: {
                                 style: {
                                     fontSize: '11px',
                                     fontFamily: 'Inter, sans-serif',
                                     colors: Array(d.labels.length).fill('#6b7280')
                                 }
                             }
                         },
                         plotOptions: {
                             radar: {
                                 size: 130,
                                 polygons: {
                                     strokeColors: '#e5e7eb',
                                     connectorColors: '#e5e7eb',
                                     fill: { colors: ['#f9fafb', '#ffffff'] }
                                 }
                             }
                         },
                         tooltip: {
                             y: { formatter: v => v + ' / 100' }
                         },
                         legend: { show: false }
                     }).render();
                 }
             }">
            <div x-ref="radar"></div>
        </div>

        {{-- Legenda de faixas --}}
        <div class="flex flex-wrap justify-center gap-4 mt-2 text-xs text-gray-500">
            @foreach([['#10B981','Excelente','85-100'],['#3B82F6','Saudável','70-84'],['#F59E0B','Atenção','55-69'],['#EF4444','Crítico','40-54'],['#6B7280','Vulnerável','0-39']] as [$c,$l,$r])
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full" style="background:{{ $c }}"></span>
                <span>{{ $l }} ({{ $r }})</span>
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Seção 3: Relatório do Consultor (só se publicado) ── --}}
    @if($assessment->report?->status === \App\Enums\DiagnosticReportStatus::Published)
    @php $rep = $assessment->report; @endphp
    <div class="tu-card overflow-hidden" style="border-top: 4px solid {{ $color }}">
        {{-- Header do relatório --}}
        <div class="px-6 pt-5 pb-4 border-b border-gray-50">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <h2 class="font-bold text-gray-900 text-lg">Análise do Consultor</h2>
                    </div>
                    @if($rep->archetype)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold"
                          style="background: {{ $color }}15; color: {{ $color }}">
                        {{ $rep->archetype }}
                    </span>
                    @endif
                </div>
                @if($rep->published_at)
                <span class="text-xs text-gray-400 shrink-0 mt-1">
                    Publicado em {{ $rep->published_at->format('d/m/Y') }}
                </span>
                @endif
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Análise qualitativa --}}
            @if($rep->content)
            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                @foreach(explode("\n\n", $rep->content) as $para)
                <p>{{ $para }}</p>
                @endforeach
            </div>
            @endif

            {{-- Highlights --}}
            @if(!empty($rep->highlights))
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                    Destaques Principais
                </h4>
                <div class="space-y-2">
                    @foreach($rep->highlights as $i => $hl)
                    @if($hl)
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full text-white text-xs font-bold shrink-0 mt-0.5
                                     flex items-center justify-center"
                              style="background: {{ $color }}">{{ $i + 1 }}</span>
                        <p class="text-sm text-gray-700">{{ $hl }}</p>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- SWOT --}}
            @if(!empty($rep->swot))
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">
                    Análise SWOT
                </h4>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['strengths',     'Forças',         '#10B981'],
                        ['weaknesses',    'Fraquezas',      '#EF4444'],
                        ['opportunities', 'Oportunidades',  '#3B82F6'],
                        ['threats',       'Ameaças',        '#F59E0B'],
                    ] as [$key, $label, $clr])
                    @if(!empty($rep->swot[$key]))
                    <div class="rounded-xl p-4" style="background: {{ $clr }}0d; border: 1px solid {{ $clr }}25">
                        <p class="text-xs font-bold mb-2" style="color: {{ $clr }}">{{ $label }}</p>
                        <ul class="space-y-1.5">
                            @foreach($rep->swot[$key] as $item)
                            @if($item)
                            <li class="flex items-start gap-2 text-xs text-gray-600">
                                <span class="w-1 h-1 rounded-full mt-1.5 shrink-0"
                                      style="background: {{ $clr }}"></span>
                                {{ $item }}
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @elseif($assessment->report && $assessment->tool->requires_review)
    {{-- Relatório em preparação --}}
    <div class="tu-card p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-gray-800">Análise do consultor em preparação</p>
            <p class="text-sm text-gray-400 mt-0.5">
                Um especialista está revisando seu diagnóstico. Você será notificado quando o relatório for publicado.
            </p>
        </div>
    </div>
    @endif

    {{-- ── Seção 4 (antiga 3): Cards de índice com interpretação ── --}}
    <div>
        <h2 class="font-bold text-gray-900 mb-4">Análise por Índice</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($assessment->results as $result)
            @php
                $rc     = $result->componentTool?->color ?? $result->dimension?->color ?? $color;
                $rname  = $result->componentTool?->name ?? $result->dimension?->name ?? '—';
                $rcode  = $result->componentTool?->code ?? '';
                $rshort = $result->componentTool?->short_description ?? $result->dimension?->description ?? '';
                $rscore = (float) $result->normalized_score;
                $interp = $this->interpretation($rscore);
            @endphp
            <div class="tu-card p-5 hover-lift">
                {{-- Header --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0"
                         style="background: {{ $rc }}">
                        <span class="text-xs font-black">{{ $rcode ?: substr($rname,0,2) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ $rname }}</p>
                        @if($rshort)
                        <p class="text-[11px] text-gray-400 truncate">{{ $rshort }}</p>
                        @endif
                    </div>
                </div>

                {{-- Score gauge mini --}}
                <div class="flex items-end justify-between mb-3">
                    <div>
                        <p class="text-3xl font-black" style="color: {{ $rc }}">
                            {{ number_format($rscore, 0) }}
                        </p>
                        <p class="text-xs text-gray-400 -mt-0.5">de 100</p>
                    </div>
                    <span class="text-sm font-bold px-3 py-1 rounded-full"
                          style="background: {{ $rc }}15; color: {{ $rc }}">
                        {{ $result->label }}
                    </span>
                </div>

                {{-- Barra --}}
                <div class="w-full bg-gray-100 rounded-full h-2.5 mb-3">
                    <div class="h-2.5 rounded-full" style="width: {{ $rscore }}%; background: {{ $rc }}"></div>
                </div>

                {{-- Interpretação --}}
                <p class="text-xs text-gray-500 leading-relaxed">{{ $interp }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Seção 5: Próximos passos ── --}}
    <div class="tu-card p-6" style="border-left: 4px solid {{ $color }}">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" style="color:{{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            O que fazer agora?
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5 text-sm">
            <div class="flex items-start gap-3">
                <span class="w-7 h-7 rounded-full text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5"
                      style="background:{{ $color }}">1</span>
                <div>
                    <p class="font-semibold text-gray-800">Revise os índices críticos</p>
                    <p class="text-gray-500 text-xs mt-0.5">Foque nos eixos abaixo de 70 — são seus maiores alavancadores de melhoria.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-7 h-7 rounded-full text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5"
                      style="background:{{ $color }}">2</span>
                <div>
                    <p class="font-semibold text-gray-800">Plano de ação personalizado</p>
                    <p class="text-gray-500 text-xs mt-0.5">Um plano com ações concretas, treinamentos e reflexões foi gerado para cada índice do seu diagnóstico.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-7 h-7 rounded-full text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5"
                      style="background:{{ $color }}">3</span>
                <div>
                    <p class="font-semibold text-gray-800">Treinamentos recomendados</p>
                    <p class="text-gray-500 text-xs mt-0.5">A plataforma sugere cursos alinhados ao seu diagnóstico para acelerar a evolução.</p>
                </div>
            </div>
        </div>

        @if($tool->requires_review)
        <div class="flex items-center gap-2 text-xs text-sky-600 bg-sky-50 px-4 py-2.5 rounded-xl mb-5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Um consultor especialista revisará e enriquecerá esta análise com insights qualitativos.
        </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('diagnostics.index') }}" wire:navigate
               class="tu-btn py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200
                      text-gray-700 hover:bg-gray-50 text-center">
                Ver outros diagnósticos
            </a>
            <a href="{{ route('diagnostics.action-plan', $assessment) }}" wire:navigate
               class="flex-1 tu-btn py-2.5 text-sm font-semibold rounded-xl border-2 text-center
                      flex items-center justify-center gap-2"
               style="border-color:{{ $color }}; color:{{ $color }}; background:{{ $color }}0f">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Ver plano de ação
            </a>
            {{-- PDF — link normal (sem wire:navigate), abre download direto --}}
            <a href="{{ route('diagnostics.result.pdf', $assessment) }}"
               target="_blank"
               class="tu-btn py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200
                      text-gray-600 hover:bg-gray-50 text-center flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Baixar PDF
            </a>
            <a href="{{ route('courses.index') }}" wire:navigate
               class="tu-btn py-2.5 text-sm font-semibold rounded-xl text-white text-center shadow-sm"
               style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}cc)">
                Explorar treinamentos
            </a>
        </div>
    </div>

    @endif {{-- isComplete --}}
</div>
