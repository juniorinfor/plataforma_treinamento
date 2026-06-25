<div class="max-w-2xl mx-auto animate-fade-in">

    {{-- ── ERRO: sem prova configurada ── --}}
    @if($noQuiz)
    <div class="tu-card p-10 text-center">
        <p class="text-gray-500 mb-4">Esta aula ainda não tem prova configurada.</p>
        @if($courseSlug)
        <a href="{{ route('courses.show', $courseSlug) }}" wire:navigate class="tu-btn tu-btn-primary">
            Voltar ao curso
        </a>
        @endif
    </div>

    {{-- ── ERRO: tentativas esgotadas ── --}}
    @elseif($noAttempts)
    <div class="tu-card p-10 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900 mb-2">Tentativas esgotadas</h2>
        <p class="text-gray-500 mb-6">Você atingiu o número máximo de tentativas permitidas para esta prova.</p>
        @if($courseSlug)
        <a href="{{ route('courses.show', $courseSlug) }}" wire:navigate class="tu-btn tu-btn-primary">
            Voltar ao curso
        </a>
        @endif
    </div>

    {{-- ── FAZENDO A PROVA ── --}}
    @elseif(!$submitted)

    {{-- Header da prova --}}
    <div class="flex items-center gap-4 mb-6">
        @if($courseSlug)
        <a href="{{ route('courses.show', $courseSlug) }}" wire:navigate
           class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 shrink-0" title="Sair da prova">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </a>
        @endif
        <div class="flex-1">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold text-gray-500">{{ $this->answeredCount }} / {{ $this->questions->count() }} respondidas</span>
                @if($this->quiz?->hearts_enabled)
                <div class="flex items-center gap-0.5">
                    @php $attempt = \App\Models\QuizAttempt::find($attemptId); @endphp
                    @for($h = 0; $h < ($this->quiz->hearts_count ?? 3); $h++)
                    <svg class="w-4 h-4 {{ $h < ($attempt?->hearts_remaining ?? 0) ? 'text-red-500' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    @endfor
                </div>
                @endif
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5">
                @php $pct = $this->questions->count() > 0 ? ($this->answeredCount / $this->questions->count()) * 100 : 0; @endphp
                <div class="h-2.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%; background: var(--tu-primary)"></div>
            </div>
        </div>
    </div>

    {{-- Navegação por pontos --}}
    @if($this->questions->count() > 1)
    <div class="flex flex-wrap items-center gap-2 mb-5">
        @foreach($this->questions as $qi => $q)
        @php
            $qAns = $answers[$q->id] ?? [];
            $isAnswered = $q->type->value === 'fill_blank' ? !empty($qAns['value']) : !empty($qAns['option_id']);
            $isCurrent  = $qi === $currentIdx;
        @endphp
        <button wire:click="goTo({{ $qi }})"
                class="w-8 h-8 rounded-full text-xs font-bold transition-all
                    {{ $isCurrent ? 'ring-2 ring-offset-1 ring-indigo-500' : '' }}
                    {{ $isAnswered ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500 hover:bg-gray-300' }}">
            {{ $qi + 1 }}
        </button>
        @endforeach
    </div>
    @endif

    {{-- Card da questão atual --}}
    @if($this->currentQuestion)
    @php $q = $this->currentQuestion; @endphp
    <div class="tu-card p-6 sm:p-8 mb-5" wire:key="question-{{ $q->id }}">

        {{-- Tipo + número --}}
        <div class="flex items-center gap-2 mb-4">
            @php
                $typeColor = match($q->type->value) {
                    'multiple_choice' => 'bg-blue-50 text-blue-600',
                    'true_false'      => 'bg-green-50 text-green-600',
                    'fill_blank'      => 'bg-orange-50 text-orange-600',
                    default           => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $typeColor }}">
                {{ $q->type->label() }}
            </span>
            <span class="text-xs text-gray-400">Questão {{ $currentIdx + 1 }} de {{ $this->questions->count() }}</span>
            <span class="ml-auto text-xs text-gray-400">{{ $q->points }} pt{{ $q->points !== 1 ? 's' : '' }}</span>
        </div>

        {{-- Enunciado --}}
        <p class="text-lg font-semibold text-gray-900 mb-6 leading-relaxed">{{ $q->content }}</p>

        {{-- Opções para MC e TF --}}
        @if($q->type->value !== 'fill_blank')
        <div class="space-y-3">
            @foreach($q->options as $oi => $opt)
            @php $isSelected = ($answers[$q->id]['option_id'] ?? null) === $opt->id; @endphp
            <button wire:click="selectOption({{ $q->id }}, {{ $opt->id }})"
                    class="w-full text-left flex items-center gap-3 px-4 py-3.5 rounded-xl border-2 transition-all
                        {{ $isSelected
                            ? 'border-indigo-500 bg-indigo-50 text-indigo-800'
                            : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-800' }}">
                <span class="w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs font-bold shrink-0
                    {{ $isSelected ? 'border-indigo-500 bg-indigo-600 text-white' : 'border-gray-300 text-gray-500' }}">
                    {{ chr(65 + $oi) }}
                </span>
                <span class="text-sm font-medium">{{ $opt->content }}</span>
                @if($isSelected)
                <svg class="w-4 h-4 ml-auto text-indigo-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                @endif
            </button>
            @endforeach
        </div>

        {{-- Input para lacuna --}}
        @else
        <div>
            <label class="block text-sm font-semibold text-gray-600 mb-2">Sua resposta:</label>
            <input type="text"
                   wire:model.live="answers.{{ $q->id }}.value"
                   placeholder="Digite aqui..."
                   class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
        </div>
        @endif

    </div>
    @endif

    {{-- Navegação entre questões --}}
    <div class="flex items-center justify-between gap-3">
        <button wire:click="prev" @disabled($currentIdx === 0)
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Anterior
        </button>

        @if($this->allAnswered)
        <button wire:click="submit"
                wire:confirm="Enviar a prova agora? As respostas não poderão ser alteradas."
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-bold shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Enviar prova
        </button>
        @endif

        <button wire:click="next" @disabled($currentIdx === $this->questions->count() - 1)
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed">
            Próxima
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    {{-- Dica de envio (quando quase tudo respondido) --}}
    @if(!$this->allAnswered && $this->answeredCount > 0)
    <p class="mt-3 text-center text-xs text-gray-400">
        Responda todas as questões para liberar o envio.
        {{ $this->questions->count() - $this->answeredCount }} restante(s).
    </p>
    @endif

    {{-- ── RESULTADO ── --}}
    @else
    @php $r = $resultData; @endphp

    {{-- Placar principal --}}
    <div class="tu-card p-8 text-center mb-6">
        <div class="w-28 h-28 mx-auto mb-4 rounded-full flex items-center justify-center text-3xl font-black
            {{ $r['passed'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
            {{ round($r['percentage']) }}%
        </div>
        <h2 class="text-2xl font-bold {{ $r['passed'] ? 'text-green-700' : 'text-red-600' }} mb-1">
            {{ $r['passed'] ? 'Aprovado!' : 'Não aprovado' }}
        </h2>
        <p class="text-sm text-gray-500 mb-5">
            {{ $r['score'] }} de {{ $r['max_score'] }} pontos
        </p>

        @if($r['xp_earned'] > 0)
        <div class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-yellow-50 border border-yellow-200">
            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
            <span class="text-sm font-bold text-yellow-700">+{{ $r['xp_earned'] }} XP</span>
        </div>
        @endif
    </div>

    {{-- Revisão das questões --}}
    <div class="tu-card overflow-hidden mb-6">
        <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-100">
            <p class="text-sm font-bold text-gray-700">Revisão das questões</p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($r['review'] as $ri => $item)
            <div class="px-5 py-4 {{ $item['is_correct'] ? 'bg-green-50/30' : 'bg-red-50/30' }}">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5
                        {{ $item['is_correct'] ? 'bg-green-500' : 'bg-red-500' }}">
                        @if($item['is_correct'])
                        <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @else
                        <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 mb-1.5">{{ $ri + 1 }}. {{ $item['content'] }}</p>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs">
                            <span class="{{ $item['is_correct'] ? 'text-green-700' : 'text-red-600' }}">
                                Sua resposta: <strong>{{ $item['given'] }}</strong>
                            </span>
                            @if(!$item['is_correct'])
                            <span class="text-green-700">
                                Correta: <strong>{{ $item['correct'] }}</strong>
                            </span>
                            @endif
                        </div>
                        @if($item['explanation'])
                        <p class="mt-2 text-xs text-gray-500 italic">{{ $item['explanation'] }}</p>
                        @endif
                    </div>
                    <span class="text-xs font-bold {{ $item['is_correct'] ? 'text-green-600' : 'text-gray-400' }} shrink-0">
                        {{ $item['earned'] }}/{{ $item['points'] }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Ações --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        @if(!$noAttempts)
        <button wire:click="retry"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Tentar novamente
        </button>
        @endif
        @if($courseSlug)
        <a href="{{ route('courses.show', $courseSlug) }}" wire:navigate
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold">
            Voltar ao curso
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endif
    </div>

    @endif
</div>
