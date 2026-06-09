@php
    $report     = $this->report;
    $assessment = $report->assessment;
    $user       = $assessment->user;
    $tool       = $assessment->tool;
    $color      = $tool->color ?? '#6366F1';
    $score      = (float) ($assessment->global_score ?? 0);
    $scoreColor = match(true) {
        $score >= 85 => '#10B981',
        $score >= 70 => '#3B82F6',
        $score >= 55 => '#F59E0B',
        $score >= 40 => '#EF4444',
        default      => '#6B7280',
    };
@endphp

<div class="space-y-6 animate-fade-in">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('platform.diagnostics.reports.index') }}" wire:navigate
               class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Editor de Relatório</h1>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ $user->name }} — {{ $tool->name }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Status badge --}}
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold
                         {{ $this->statusBadge($report->status) }}">
                {{ $report->status->label() }}
            </span>

            {{-- Gerar com IA --}}
            <button wire:click="generateAiDraft"
                    wire:loading.attr="disabled"
                    wire:target="generateAiDraft"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-purple-200
                           bg-purple-50 text-purple-700 text-xs font-semibold
                           hover:bg-purple-100 transition-all disabled:opacity-60">
                <span wire:loading.remove wire:target="generateAiDraft">
                    <svg class="w-3.5 h-3.5 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"/>
                    </svg>
                    Gerar com IA
                </span>
                <span wire:loading wire:target="generateAiDraft" class="flex items-center gap-1">
                    <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Gerando...
                </span>
            </button>

            {{-- Baixar PDF --}}
            <a href="{{ route('platform.diagnostics.reports.pdf', $report) }}"
               target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-gray-200
                      bg-white text-gray-600 text-xs font-semibold hover:bg-gray-50 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PDF
            </a>

            {{-- Salvar --}}
            <button wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-gray-200
                           bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Salvar rascunho
            </button>

            @if($report->status !== \App\Enums\DiagnosticReportStatus::InReview &&
                $report->status !== \App\Enums\DiagnosticReportStatus::Published)
            {{-- Em revisão --}}
            <button wire:click="markInReview"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border-2 border-amber-200
                           bg-amber-50 text-amber-700 text-xs font-semibold hover:bg-amber-100 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Em revisão
            </button>
            @endif

            @if($report->status !== \App\Enums\DiagnosticReportStatus::Published)
            {{-- Publicar --}}
            <button wire:click="publish"
                    wire:confirm="Publicar o relatório? O colaborador poderá visualizá-lo imediatamente."
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-white text-xs font-semibold
                           shadow-sm transition-all hover:opacity-90"
                    style="background: linear-gradient(135deg, #10B981, #059669)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7"/>
                </svg>
                Publicar
            </button>
            @endif
        </div>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Layout Split ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 items-start">

        {{-- ── PAINEL ESQUERDO: Dados do Assessment ── --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Colaborador --}}
            <div class="tu-card p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Colaborador</p>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500
                                flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            {{-- Score global --}}
            <div class="tu-card p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Score Global</p>
                <div class="flex items-end gap-3 mb-3">
                    <span class="text-5xl font-black" style="color: {{ $scoreColor }}">
                        {{ number_format($score, 0) }}
                    </span>
                    <div class="pb-1">
                        <p class="text-gray-400 text-sm">de 100</p>
                        <p class="font-bold text-sm" style="color: {{ $scoreColor }}">
                            {{ $assessment->global_label }}
                        </p>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all"
                         style="width: {{ $score }}%; background: {{ $scoreColor }}"></div>
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    Concluído em {{ $assessment->completed_at?->format('d/m/Y') }}
                </p>
            </div>

            {{-- Índices --}}
            <div class="tu-card p-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Scores por Índice</p>
                <div class="space-y-3">
                    @foreach($assessment->results as $result)
                    @php
                        $rc     = $result->componentTool?->color ?? $result->dimension?->color ?? $color;
                        $rname  = $result->componentTool?->name  ?? $result->dimension?->name  ?? '—';
                        $rcode  = $result->componentTool?->code  ?? $result->dimension?->code  ?? '';
                        $rscore = (float) $result->normalized_score;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1 gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-[10px] font-black px-1.5 py-0.5 rounded shrink-0"
                                      style="background: {{ $rc }}20; color: {{ $rc }}">
                                    {{ $rcode ?: substr($rname,0,3) }}
                                </span>
                                <span class="text-xs text-gray-600 truncate">{{ $rname }}</span>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <span class="text-xs font-bold" style="color: {{ $rc }}">
                                    {{ $result->label }}
                                </span>
                                <span class="text-sm font-black text-gray-700">
                                    {{ number_format($rscore, 0) }}
                                </span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full"
                                 style="width: {{ $rscore }}%; background: {{ $rc }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- AI Provider info --}}
            @if($report->aiProvider)
            <div class="tu-card p-4 flex items-center gap-3 text-xs text-gray-500">
                <svg class="w-4 h-4 text-purple-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z"/>
                </svg>
                <span>Rascunho gerado via
                    <strong class="text-gray-700">{{ $report->aiProvider->name }}</strong>
                    ({{ $report->aiProvider->driver }})
                </span>
            </div>
            @endif
        </div>

        {{-- ── PAINEL DIREITO: Editor ── --}}
        <div class="xl:col-span-3 space-y-5">

            {{-- Arquétipo --}}
            <div class="tu-card p-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    Arquétipo Organizacional
                </label>
                <input wire:model="archetype"
                       type="text"
                       placeholder="Ex: Organização em Transição"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                              text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/30
                              focus:border-indigo-400 transition-all placeholder-gray-300">
            </div>

            {{-- Análise Qualitativa --}}
            <div class="tu-card p-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    Análise Qualitativa
                    <span class="text-gray-400 font-normal normal-case ml-1">
                        (visível ao colaborador)
                    </span>
                </label>
                <textarea wire:model="content"
                          rows="12"
                          placeholder="Escreva a análise qualitativa aqui. Use parágrafos separados por linhas em branco."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-gray-700
                                 leading-relaxed focus:outline-none focus:ring-2 focus:ring-indigo-500/30
                                 focus:border-indigo-400 transition-all resize-y placeholder-gray-300"></textarea>
                <p class="text-[11px] text-gray-400 mt-1.5">
                    {{ strlen($content) }} caracteres
                </p>
            </div>

            {{-- Highlights --}}
            <div class="tu-card p-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                    Destaques / Insights Principais
                    <span class="text-gray-400 font-normal normal-case ml-1">(até 3)</span>
                </label>
                <div class="space-y-2.5">
                    @foreach($highlights as $i => $hl)
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full text-white text-xs font-bold flex items-center
                                     justify-center shrink-0"
                              style="background: {{ $color }}">{{ $i + 1 }}</span>
                        <input wire:model="highlights.{{ $i }}"
                               type="text"
                               placeholder="Destaque ou insight {{ $i + 1 }}..."
                               class="flex-1 px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-700
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/30
                                      focus:border-indigo-400 transition-all placeholder-gray-300">
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- SWOT --}}
            <div class="tu-card p-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">
                    Análise SWOT
                </label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['strengths',     'Forças',         '#10B981', 'F'],
                        ['weaknesses',    'Fraquezas',      '#EF4444', 'W'],
                        ['opportunities', 'Oportunidades',  '#3B82F6', 'O'],
                        ['threats',       'Ameaças',        '#F59E0B', 'A'],
                    ] as [$key, $label, $clr, $icon])
                    <div>
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="w-5 h-5 rounded text-white text-[10px] font-black flex items-center
                                         justify-center shrink-0"
                                  style="background: {{ $clr }}">{{ $icon }}</span>
                            <span class="text-xs font-semibold text-gray-600">{{ $label }}</span>
                        </div>
                        <div class="space-y-1.5">
                            @foreach($swot[$key] as $j => $item)
                            <input wire:model="swot.{{ $key }}.{{ $j }}"
                                   type="text"
                                   placeholder="{{ $label }} {{ $j + 1 }}..."
                                   class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-700
                                          focus:outline-none focus:ring-1 focus:ring-indigo-500/30
                                          focus:border-indigo-400 transition-all placeholder-gray-300">
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Ações rápidas bottom --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button wire:click="save"
                        class="tu-btn px-5 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200
                               text-gray-700 hover:bg-gray-50">
                    Salvar rascunho
                </button>
                @if($report->status !== \App\Enums\DiagnosticReportStatus::Published)
                <button wire:click="publish"
                        wire:confirm="Publicar o relatório? O colaborador poderá visualizá-lo imediatamente."
                        class="tu-btn px-5 py-2.5 text-sm font-semibold rounded-xl text-white shadow-sm
                               hover:opacity-90 transition-opacity"
                        style="background: linear-gradient(135deg, #10B981, #059669)">
                    Publicar relatório
                </button>
                @else
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 bg-emerald-50
                             px-4 py-2.5 rounded-xl">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Publicado em {{ $report->published_at?->format('d/m/Y') }}
                </span>
                @endif
            </div>
        </div>
    </div>

</div>
