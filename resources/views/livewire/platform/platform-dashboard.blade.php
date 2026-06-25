@php
    $s   = $this->stats;
    $act = $this->activity;
@endphp

<div class="space-y-6 animate-fade-in">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Painel da Plataforma</h1>
            <p class="text-sm text-gray-500 mt-0.5">Visão consolidada de todas as empresas · {{ now()->format('d \d\e F \d\e Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('platform.diagnostics.reports.index') }}" wire:navigate
               class="tu-btn tu-btn-primary flex items-center gap-1.5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Fila de Relatórios
                @if($this->reportQueueCount > 0)
                <span class="text-[10px] font-bold bg-white/25 px-1.5 py-0.5 rounded-full">{{ $this->reportQueueCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Empresas --}}
        <div class="tu-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Empresas</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $s['totalCompanies'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Usuários --}}
        <div class="tu-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Usuários</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $s['totalUsers'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Assinaturas ativas --}}
        <div class="tu-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Assinaturas ativas</p>
                    <p class="text-3xl font-black text-emerald-600 mt-1">{{ $s['activeSubscriptions'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- MRR estimado --}}
        <div class="tu-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">MRR estimado</p>
                    <p class="text-3xl font-black text-indigo-600 mt-1">R$ {{ number_format($s['estimatedMrr'], 0, ',', '.') }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">Soma mensal dos planos ativos</p>
        </div>
    </div>

    {{-- ── Linha 2: Fila de relatórios + atalhos de plataforma ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Fila de relatórios (recentes) --}}
        <div class="tu-card overflow-hidden lg:col-span-2">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-gray-900">Relatórios aguardando revisão</h2>
                    @if($this->reportQueueCount > 0)
                    <span class="text-[10px] font-bold bg-amber-500 text-white px-1.5 py-0.5 rounded-full">{{ $this->reportQueueCount }}</span>
                    @endif
                </div>
                <a href="{{ route('platform.diagnostics.reports.index') }}" wire:navigate
                   class="text-xs text-indigo-500 font-semibold hover:underline">Ver fila →</a>
            </div>

            @if($this->recentReports->isEmpty())
            <div class="px-6 py-10 text-center text-gray-400 text-sm">
                Nenhum relatório na fila. Tudo em dia. 🎉
            </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($this->recentReports as $r)
                @php
                    $isPending = $r->status === \App\Enums\DiagnosticReportStatus::Pending;
                    $sc = $isPending ? '#F59E0B' : '#6366F1';
                @endphp
                <a href="{{ route('platform.diagnostics.reports.edit', $r->id) }}" wire:navigate
                   class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ substr($r->assessment?->user?->name ?? '?', 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $r->assessment?->user?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $r->assessment?->tool?->name ?? '—' }}</p>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full shrink-0"
                          style="background:{{ $sc }}18; color:{{ $sc }}">
                        {{ $r->status->label() }}
                    </span>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Atalhos / números de plataforma --}}
        <div class="space-y-4">
            <a href="{{ route('platform.diagnostics.index') }}" wire:navigate class="tu-card p-5 block hover-lift">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ferramentas publicadas</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $this->publishedTools }}</p>
                <span class="text-xs text-indigo-500 font-semibold mt-2 inline-flex items-center gap-1">Gerenciar →</span>
            </a>
            <div class="tu-card p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Diagnósticos concluídos</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $act['completedAssessments'] }}</p>
            </div>
            <div class="tu-card p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Inscrições (global)</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $act['totalEnrollments'] }}</p>
            </div>
        </div>
    </div>

    {{-- ── Empresas recentes ── --}}
    <div class="tu-card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="font-bold text-gray-900">Empresas recentes</h2>
        </div>

        @if($this->recentCompanies->isEmpty())
        <div class="px-6 py-10 text-center text-gray-400 text-sm">Nenhuma empresa cadastrada ainda.</div>
        @else
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Empresa</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Plano</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Usuários</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Assinatura</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Cadastro</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($this->recentCompanies as $c)
                @php
                    $status = $c->subscription_status;
                    $scolor = match($status?->value) {
                        'active'   => '#10B981',
                        'trial'    => '#3B82F6',
                        'past_due' => '#F59E0B',
                        default    => '#EF4444',
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0"
                                 style="background: {{ $c->primary_color ?? '#3B82F6' }}">
                                {{ substr($c->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-800">{{ $c->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $c->plan?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-center text-sm font-semibold text-gray-700">{{ $c->users_count }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                              style="background:{{ $scolor }}18; color:{{ $scolor }}">
                            {{ $status?->label() ?? '—' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right text-xs text-gray-400">{{ $c->created_at?->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>
