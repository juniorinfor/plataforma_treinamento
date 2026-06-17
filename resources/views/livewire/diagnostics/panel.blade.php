<div class="max-w-5xl mx-auto space-y-6 animate-fade-in">

    {{-- Voltar --}}
    <a href="{{ route('diagnostics.index') }}" wire:navigate
       class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar aos diagnósticos
    </a>

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $this->tool->name }}</h1>
            <p class="text-gray-500 mt-1">
                {{ $this->isAdmin() ? 'Resultados consolidados — todas as empresas' : 'Resultados da sua empresa' }}
            </p>
        </div>
        @unless($this->isAdmin())
        <button wire:click="respondMine"
                class="tu-btn px-4 py-2.5 text-sm font-semibold rounded-xl text-white shrink-0"
                style="background: {{ $this->tool->color ?? '#4F46E5' }}">
            Responder o meu
        </button>
        @endunless
    </div>

    {{-- ───────────────── ADMIN: visão geral (todas as empresas) ───────────────── --}}
    @if($this->isAdmin() && !$selectedCompanyId)

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="tu-card p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Empresas</p>
                <p class="text-3xl font-black text-gray-900 mt-1">{{ count($this->companies) }}</p>
            </div>
            <div class="tu-card p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Respostas concluídas</p>
                <p class="text-3xl font-black text-gray-900 mt-1">{{ $this->completedCount }}</p>
            </div>
            <div class="tu-card p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Média global</p>
                <p class="text-3xl font-black mt-1" style="color: {{ $this->tool->color ?? '#4F46E5' }}">
                    {{ $this->completedCount ? number_format($this->globalAverage, 1) : '—' }}
                    <span class="text-sm font-normal text-gray-400">/100</span>
                </p>
            </div>
        </div>

        <div class="tu-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="font-bold text-gray-900">Empresas que responderam</h2>
            </div>
            @if(count($this->companies))
            <div class="divide-y divide-gray-50">
                @foreach($this->companies as $c)
                <button wire:click="selectCompany({{ $c['id'] }})"
                        class="w-full flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors text-left">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $c['name'] }}</p>
                        <p class="text-xs text-gray-400">{{ $c['n'] }} {{ $c['n'] == 1 ? 'resposta' : 'respostas' }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-lg font-black" style="color: {{ $this->tool->color ?? '#4F46E5' }}">
                            {{ number_format($c['avg'], 1) }}<span class="text-xs font-normal text-gray-400">/100</span>
                        </span>
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </button>
                @endforeach
            </div>
            @else
            <p class="px-6 py-10 text-center text-gray-400">Nenhuma empresa concluiu este diagnóstico ainda.</p>
            @endif
        </div>

    {{-- ───────────────── Escopo de uma empresa (gestor ou admin drill-down) ───────────────── --}}
    @else

        @if($this->isAdmin() && $selectedCompanyId)
        <button wire:click="clearCompany"
                class="inline-flex items-center gap-1.5 text-sm text-indigo-500 hover:text-indigo-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Todas as empresas
        </button>
        @endif

        {{-- Bloqueado por confidencialidade --}}
        @if($this->locked)
        <div class="tu-card p-8 text-center border-2 border-dashed border-gray-200">
            <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="font-bold text-gray-900 mb-1">Resultados protegidos por confidencialidade</h2>
            <p class="text-sm text-gray-500 max-w-md mx-auto">
                Para preservar o anonimato, o resultado agregado é liberado a partir de
                <strong>{{ $this->tool->min_responses }}</strong> respostas concluídas.
                Faltam <strong>{{ max(0, $this->tool->min_responses - $this->completedCount) }}</strong>.
            </p>
            <p class="text-xs text-gray-400 mt-2">{{ $this->completedCount }} de {{ $this->tool->min_responses }} respostas</p>
        </div>
        @else

        {{-- Score geral + dimensões --}}
        @if($this->completedCount)
        <div class="tu-card p-6">
            <div class="flex items-center justify-between mb-1">
                <h2 class="font-bold text-gray-900">Score geral</h2>
                <span class="text-xs text-gray-400">{{ $this->completedCount }} {{ $this->completedCount == 1 ? 'resposta' : 'respostas' }}</span>
            </div>
            <p class="text-4xl font-black mb-5" style="color: {{ $this->tool->color ?? '#4F46E5' }}">
                {{ number_format($this->globalAverage, 1) }}<span class="text-base font-normal text-gray-400">/100</span>
            </p>

            <div class="space-y-3">
                @foreach($this->dimensionAverages as $dim)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $dim['name'] }}</span>
                        <span class="text-sm font-bold" style="color: {{ $dim['color'] }}">{{ number_format($dim['score'], 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-700"
                             style="width: {{ $dim['score'] }}%; background: {{ $dim['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="tu-card p-10 text-center">
            <p class="text-gray-400">Nenhuma resposta concluída neste escopo ainda.</p>
        </div>
        @endif

        {{-- Resultados individuais (admin sempre; gestor só em não-confidencial) --}}
        @if($this->individuals->isNotEmpty())
        <div class="tu-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="font-bold text-gray-900">Resultados individuais</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($this->individuals as $ind)
                <a href="{{ route('diagnostics.result', $ind['id']) }}" wire:navigate
                   class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50 transition-colors">
                    <span class="text-sm font-medium text-gray-800">{{ $ind['name'] }}</span>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-400">{{ $ind['label'] }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ $ind['score'] !== null ? number_format($ind['score'], 1) : '—' }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @elseif($this->tool->is_confidential && !$this->isAdmin())
        <div class="tu-card p-4 bg-indigo-50/50 border border-indigo-100 flex items-center gap-3">
            <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-indigo-700">
                Este diagnóstico é confidencial: você vê o resultado <strong>agregado</strong> da empresa,
                sem respostas individuais identificadas.
            </p>
        </div>
        @endif

        @endif

        {{-- Acompanhamento de participação --}}
        @if(count($this->participation))
        <div class="tu-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="font-bold text-gray-900">Participação</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($this->participation as $p)
                <div class="flex items-center justify-between px-6 py-3">
                    <span class="text-sm text-gray-700">{{ $p['name'] }}</span>
                    @php
                        $badge = match($p['status']) {
                            'concluido'    => ['Concluído', 'bg-emerald-50 text-emerald-600'],
                            'em_andamento' => ['Em andamento', 'bg-amber-50 text-amber-600'],
                            default        => ['Pendente', 'bg-gray-100 text-gray-500'],
                        };
                    @endphp
                    <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-1 rounded-full {{ $badge[1] }}">
                        {{ $badge[0] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif
</div>
