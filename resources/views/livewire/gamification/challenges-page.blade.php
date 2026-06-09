<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">

    {{-- ── Header ── --}}
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">Desafios</h1>
        <p class="text-sm text-gray-500 mt-1">
            Complete desafios para ganhar XP extra e conquistar badges especiais
        </p>
        @if($this->completedToday > 0)
        <span class="inline-flex items-center gap-1.5 mt-2 text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            {{ $this->completedToday }} desafio(s) concluído(s) hoje
        </span>
        @endif
    </div>

    {{-- ── Grupos de desafios ── --}}
    @forelse($this->challengeGroups as $type => $group)
    <div class="space-y-3">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">
            {{ $group['label'] }}
        </h2>

        @foreach($group['items'] as $entry)
        @php
            $ch   = $entry['challenge'];
            $done = $entry['done'];
            $pct  = $entry['pct'];
            $barColor = $done ? '#10B981' : ($type === 'weekly' ? '#8B5CF6' : '#F97316');
        @endphp
        <div class="tu-card p-5 {{ $done ? 'opacity-70' : '' }} transition-opacity"
             wire:key="ch-{{ $ch->id }}">
            <div class="flex items-start gap-4">

                {{-- Ícone --}}
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0
                    {{ $done ? 'bg-emerald-50' : ($type === 'weekly' ? 'bg-purple-50' : 'bg-orange-50') }}">
                    @if($done)
                        ✅
                    @elseif($type === 'weekly')
                        🏆
                    @else
                        ⚡
                    @endif
                </div>

                {{-- Conteúdo --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-semibold text-gray-900 {{ $done ? 'line-through text-gray-400' : '' }}">
                            {{ $ch->title }}
                        </h3>
                        <span class="text-sm font-black shrink-0" style="color: var(--tu-xp)">
                            +{{ $ch->xp_reward }} XP
                        </span>
                    </div>

                    @if($ch->description)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $ch->description }}</p>
                    @endif

                    {{-- Barra de progresso --}}
                    <div class="mt-3">
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-gray-500">
                                {{ $entry['current'] }} de {{ $entry['target'] }}
                            </span>
                            <span class="font-semibold" style="color: {{ $barColor }}">
                                {{ $pct }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full transition-all duration-700"
                                 style="width: {{ $pct }}%; background: {{ $barColor }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($done)
            <div class="mt-3 flex items-center gap-1.5 text-xs text-emerald-600 font-semibold">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Concluído! XP já creditado.
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @empty
    <div class="tu-card p-12 text-center">
        <p class="text-gray-400 text-sm">Nenhum desafio ativo no momento.</p>
    </div>
    @endforelse

</div>
