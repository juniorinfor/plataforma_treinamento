<div class="space-y-6 animate-fade-in">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Fila de Relatórios</h1>
            <p class="text-sm text-gray-500 mt-1">
                Gerencie e publique os relatórios de diagnóstico dos colaboradores
            </p>
        </div>
        <span class="text-3xl font-black text-gray-200">
            {{ $this->statusCounts['all'] }}
        </span>
    </div>

    {{-- ── Status Tabs ── --}}
    <div class="flex gap-2 flex-wrap">
        <button wire:click="$set('filterStatus', '')"
                class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all
                       {{ $filterStatus === ''
                           ? 'bg-indigo-600 text-white shadow-sm'
                           : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Todos ({{ $this->statusCounts['all'] }})
        </button>

        @foreach(\App\Enums\DiagnosticReportStatus::cases() as $st)
        @php
            $cnt = $this->statusCounts['counts'][$st->value] ?? 0;
            $active = $filterStatus === $st->value;
        @endphp
        <button wire:click="$set('filterStatus', '{{ $st->value }}')"
                class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all
                       {{ $active
                           ? 'bg-indigo-600 text-white shadow-sm'
                           : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $st->label() }} ({{ $cnt }})
        </button>
        @endforeach
    </div>

    {{-- ── Busca ── --}}
    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="Buscar por nome ou e-mail do colaborador..."
               class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl
                      focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400
                      bg-white transition-all">
    </div>

    {{-- ── Tabela ── --}}
    @if($this->reports->isEmpty())
    <div class="tu-card p-12 text-center">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-gray-400 text-sm">Nenhum relatório encontrado.</p>
    </div>
    @else
    <div class="tu-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/60">
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Colaborador</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Ferramenta</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Score</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-500 text-xs uppercase tracking-wider">Criado em</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($this->reports as $report)
                    @php
                        $assessment = $report->assessment;
                        $user       = $assessment?->user;
                        $tool       = $assessment?->tool;
                        $score      = (float) ($assessment?->global_score ?? 0);
                        $scoreColor = match(true) {
                            $score >= 85 => '#10B981',
                            $score >= 70 => '#3B82F6',
                            $score >= 55 => '#F59E0B',
                            $score >= 40 => '#EF4444',
                            default      => '#6B7280',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors" wire:key="rq-{{ $report->id }}">
                        {{-- Colaborador --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500
                                            flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ $user ? strtoupper(substr($user->name, 0, 2)) : '??' }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $user?->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $user?->email ?? '—' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Ferramenta --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($tool)
                                <span class="w-2 h-2 rounded-full shrink-0"
                                      style="background: {{ $tool->color ?? '#6366F1' }}"></span>
                                <span class="text-gray-700 font-medium">{{ $tool->name }}</span>
                                @if($tool->code)
                                <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">
                                    {{ $tool->code }}
                                </span>
                                @endif
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </div>
                        </td>

                        {{-- Score --}}
                        <td class="px-4 py-3">
                            <span class="font-black text-sm" style="color: {{ $scoreColor }}">
                                {{ number_format($score, 0) }}
                            </span>
                            <span class="text-gray-400 text-xs">/100</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @php $st = $report->status; @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                         {{ $this->statusColor($st->value) }}">
                                {{ $st->label() }}
                            </span>
                        </td>

                        {{-- Data --}}
                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                            {{ $report->created_at->format('d/m/Y') }}
                        </td>

                        {{-- Ação --}}
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('platform.diagnostics.reports.edit', $report) }}"
                               wire:navigate
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600
                                      hover:text-indigo-800 transition-colors px-3 py-1.5 rounded-lg
                                      hover:bg-indigo-50">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if($this->reports->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/40">
            {{ $this->reports->links() }}
        </div>
        @endif
    </div>
    @endif

</div>
