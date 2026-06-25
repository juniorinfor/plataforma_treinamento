@php
    $s = $this->stats;
    $maxEnroll = $this->topCourses->max('total') ?: 1;
    $maxXp     = $this->topCollaborators->max(fn ($u) => $u->points?->total_xp ?? 0) ?: 1;
@endphp

<div class="space-y-6 animate-fade-in">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Painel do Gestor</h1>
            <p class="text-sm text-gray-500 mt-0.5">Visão consolidada da empresa · {{ now()->format('d \d\e F \d\e Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.diagnostics') }}" wire:navigate
               class="tu-btn tu-btn-primary flex items-center gap-1.5 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Relatório de Diagnósticos
            </a>
        </div>
    </div>

    {{-- ── Alertas ── --}}
    @if(!empty($this->alerts))
    <div class="tu-card p-4 border-l-4 border-amber-400 bg-amber-50/40">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <h2 class="text-sm font-bold text-amber-800">Precisa de atenção</h2>
        </div>
        <ul class="space-y-1">
            @foreach($this->alerts as $alert)
            <li class="text-sm text-amber-800/90 flex items-start gap-2">
                <span class="text-amber-400 mt-0.5">•</span><span>{{ $alert }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Cards acionáveis ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colaboradores inativos --}}
        <div class="tu-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-gray-900">Colaboradores inativos</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Sem login há +{{ 14 }} dias</p>
                </div>
                <span class="text-2xl font-black {{ $this->inactive['count'] > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                    {{ $this->inactive['count'] }}
                </span>
            </div>
            @forelse($this->inactive['list'] as $u)
            <div class="flex items-center gap-2.5 py-1.5">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
                    {{ substr($u->name, 0, 2) }}
                </div>
                <span class="text-sm text-gray-700 flex-1 truncate">{{ $u->name }}</span>
                <span class="text-xs text-gray-400">
                    {{ $u->last_login_at ? \Illuminate\Support\Carbon::parse($u->last_login_at)->diffForHumans(short: true) : 'nunca' }}
                </span>
            </div>
            @empty
            <p class="text-sm text-emerald-600 py-2">Todos ativos recentemente. 👏</p>
            @endforelse
            <a href="{{ route('admin.users') }}" wire:navigate
               class="text-xs text-indigo-500 font-semibold mt-3 inline-flex items-center gap-1 hover:underline">Ver colaboradores →</a>
        </div>

        {{-- Compliance de obrigatórios --}}
        <div class="tu-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">Cursos obrigatórios</h2>
                @if($this->mandatoryCompliance['overall'] !== null)
                <span class="text-2xl font-black {{ $this->mandatoryCompliance['overall'] >= 70 ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $this->mandatoryCompliance['overall'] }}%
                </span>
                @endif
            </div>
            @forelse($this->mandatoryCompliance['rows'] as $row)
            <div class="mb-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm text-gray-700 truncate max-w-[60%]">{{ $row['title'] }}</span>
                    <span class="text-xs text-gray-500">{{ $row['completed'] }}/{{ $row['total'] }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full transition-all {{ $row['pct'] >= 70 ? 'bg-emerald-500' : 'bg-amber-500' }}"
                         style="width: {{ $row['pct'] }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 py-2">Nenhum curso obrigatório definido.</p>
            @endforelse
            <a href="{{ route('admin.courses') }}" wire:navigate
               class="text-xs text-indigo-500 font-semibold mt-2 inline-flex items-center gap-1 hover:underline">Gerenciar cursos →</a>
        </div>

        {{-- Diagnósticos pendentes --}}
        <div class="tu-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-gray-900">Diagnósticos pendentes</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Iniciados, não concluídos</p>
                </div>
                <span class="text-2xl font-black {{ $this->pendingDiagnostics['count'] > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                    {{ $this->pendingDiagnostics['count'] }}
                </span>
            </div>
            @forelse($this->pendingDiagnostics['list'] as $a)
            <div class="flex items-center gap-2.5 py-1.5">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
                    {{ substr($a->user?->name ?? '?', 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-700 truncate">{{ $a->user?->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $a->tool?->name ?? '—' }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-emerald-600 py-2">Nenhum diagnóstico parado. ✅</p>
            @endforelse
            <a href="{{ route('admin.diagnostics') }}" wire:navigate
               class="text-xs text-indigo-500 font-semibold mt-3 inline-flex items-center gap-1 hover:underline">Ver diagnósticos →</a>
        </div>
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Colaboradores --}}
        <div class="tu-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Colaboradores</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $s['totalUsers'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.users') }}" wire:navigate
               class="text-xs text-blue-500 font-semibold mt-3 inline-flex items-center gap-1 hover:underline">
                Ver todos →
            </a>
        </div>

        {{-- Inscrições --}}
        <div class="tu-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Inscrições</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $s['totalEnrollments'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.courses') }}" wire:navigate
               class="text-xs text-purple-500 font-semibold mt-3 inline-flex items-center gap-1 hover:underline">
                Ver cursos →
            </a>
        </div>

        {{-- Taxa de conclusão --}}
        <div class="tu-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Conclusão</p>
                    <p class="text-3xl font-black mt-1
                        {{ $s['completionRate'] >= 70 ? 'text-emerald-600' : ($s['completionRate'] >= 40 ? 'text-amber-600' : 'text-red-600') }}">
                        {{ $s['completionRate'] }}%
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full bg-emerald-500 transition-all"
                     style="width: {{ $s['completionRate'] }}%"></div>
            </div>
        </div>

        {{-- Diagnósticos --}}
        <div class="tu-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Diagnósticos</p>
                    <p class="text-3xl font-black text-indigo-600 mt-1">{{ $s['diagnosticsDone'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.diagnostics') }}" wire:navigate
               class="text-xs text-indigo-500 font-semibold mt-3 inline-flex items-center gap-1 hover:underline">
                Ver relatório →
            </a>
        </div>
    </div>

    {{-- ── Linha 2: Top cursos + Top colaboradores ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top Cursos --}}
        <div class="tu-card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-bold text-gray-900">Cursos mais acessados</h2>
                <a href="{{ route('admin.courses') }}" wire:navigate
                   class="text-xs text-indigo-500 font-semibold hover:underline">Ver todos →</a>
            </div>

            @forelse($this->topCourses as $ec)
            <div class="mb-4">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-medium text-gray-700 truncate max-w-[65%]">
                        {{ $ec->course?->title ?? '—' }}
                    </span>
                    <span class="text-xs text-gray-500 shrink-0">{{ $ec->total }} inscritos</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all" style="width: {{ round(($ec->total/$maxEnroll)*100) }}%; background: var(--tu-primary)"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-6">Nenhuma inscrição registrada ainda.</p>
            @endforelse
        </div>

        {{-- Top Colaboradores por XP --}}
        <div class="tu-card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-bold text-gray-900">Ranking de engajamento</h2>
                <a href="{{ route('admin.users') }}" wire:navigate
                   class="text-xs text-indigo-500 font-semibold hover:underline">Ver todos →</a>
            </div>

            <div class="space-y-3">
                @forelse($this->topCollaborators as $rank => $u)
                @php $xp = $u->points?->total_xp ?? 0; @endphp
                <div class="flex items-center gap-3">
                    <span class="w-6 text-center text-xs font-black
                        {{ $rank === 0 ? 'text-yellow-500' : ($rank === 1 ? 'text-gray-400' : ($rank === 2 ? 'text-amber-700' : 'text-gray-300')) }}">
                        {{ $rank + 1 }}
                    </span>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-400 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ substr($u->name, 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $u->name }}</p>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                            <div class="h-1.5 rounded-full bg-yellow-400 transition-all"
                                 style="width: {{ round(($xp/$maxXp)*100) }}%"></div>
                        </div>
                    </div>
                    <span class="text-xs font-black text-yellow-600 shrink-0">{{ number_format($xp) }} XP</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-6">Nenhum colaborador encontrado.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Diagnósticos recentes ── --}}
    <div class="tu-card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Diagnósticos recentes</h2>
            <a href="{{ route('admin.diagnostics') }}" wire:navigate
               class="text-xs text-indigo-500 font-semibold hover:underline">Ver todos →</a>
        </div>

        @if($this->recentAssessments->isEmpty())
        <div class="px-6 py-10 text-center text-gray-400 text-sm">
            Nenhum diagnóstico concluído ainda.
        </div>
        @else
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Colaborador</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Ferramenta</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Score</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Label</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($this->recentAssessments as $a)
                @php
                    $tc     = $a->tool?->color ?? '#6366F1';
                    $score  = (float) $a->global_score;
                    $lcolor = match(true) {
                        $score >= 85 => '#10B981',
                        $score >= 70 => '#3B82F6',
                        $score >= 55 => '#F59E0B',
                        $score >= 40 => '#EF4444',
                        default      => '#6B7280',
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ substr($a->user?->name ?? '?', 0, 2) }}
                            </div>
                            <span class="text-sm font-medium text-gray-800">{{ $a->user?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $tc }}"></span>
                            <span class="text-sm text-gray-600">{{ $a->tool?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-lg font-black" style="color:{{ $lcolor }}">{{ number_format($score, 0) }}</span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                              style="background:{{ $lcolor }}18; color:{{ $lcolor }}">
                            {{ $a->global_label }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right text-xs text-gray-400">
                        {{ $a->completed_at?->format('d/m/Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>
