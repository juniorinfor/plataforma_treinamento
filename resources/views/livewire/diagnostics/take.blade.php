@php
    $steps     = $this->steps;
    $total     = count($steps);
    $step      = $steps[$currentStep] ?? [];
    $questions = $step['questions'] ?? [];
    $color     = $step['color'] ?? '#6366F1';
    $isLast    = $currentStep === $total - 1;
@endphp

<div class="max-w-2xl mx-auto space-y-6 animate-fade-in"
     x-data="{}"
     x-on:scroll-top.window="window.scrollTo({top:0,behavior:'smooth'})">

    {{-- Cabeçalho + progresso --}}
    <div>
        <a href="{{ route('diagnostics.index') }}" wire:navigate
           class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar aos diagnósticos
        </a>

        <div class="tu-card p-5">
            {{-- Tool name --}}
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-base font-bold text-gray-900">
                    {{ $this->assessment->tool->name }}
                </h1>
                <span class="text-sm text-gray-500">
                    Etapa {{ $currentStep + 1 }} de {{ $total }}
                </span>
            </div>

            {{-- Barra de progresso --}}
            <div class="w-full bg-gray-100 rounded-full h-2.5 mb-4">
                <div class="h-2.5 rounded-full transition-all duration-500"
                     style="width: {{ $this->progressPercent() }}%; background: {{ $color }}"></div>
            </div>

            {{-- Step pills --}}
            <div class="flex gap-2 overflow-x-auto pb-1">
                @foreach($steps as $i => $s)
                <button wire:click="$set('currentStep', {{ $i }})"
                        title="{{ $s['name'] }}"
                        class="shrink-0 h-2 rounded-full transition-all duration-300 {{ $i === $currentStep ? 'w-8' : 'w-2' }}"
                        style="background: {{ $i <= $currentStep ? $s['color'] : '#E5E7EB' }}">
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Erro de validação --}}
    @error('step')
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        {{ $message }}
    </div>
    @enderror

    {{-- Seção atual --}}
    <div class="tu-card overflow-hidden animate-slide-up">
        {{-- Header da seção --}}
        <div class="px-6 py-4 flex items-center gap-3"
             style="background: linear-gradient(135deg, {{ $color }}18, {{ $color }}08)">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-sm"
                 style="background: {{ $color }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: {{ $color }}">
                    {{ $step['code'] ?? '' }}
                </p>
                <h2 class="font-bold text-gray-900 leading-tight">{{ $step['name'] ?? '' }}</h2>
            </div>
            <span class="ml-auto text-xs text-gray-400 shrink-0">
                {{ $this->answeredInStep($currentStep) }}/{{ count($questions) }} respondidas
            </span>
        </div>

        {{-- Perguntas --}}
        <div class="divide-y divide-gray-50">
            @foreach($questions as $qi => $question)
            <div class="px-6 py-5" wire:key="q-{{ $question->id }}">
                <p class="text-sm font-medium text-gray-900 mb-1 leading-relaxed">
                    <span class="inline-block w-6 h-6 rounded-full text-center text-xs font-bold mr-2 leading-6"
                          style="background: {{ $color }}18; color: {{ $color }}">
                        {{ $qi + 1 }}
                    </span>
                    {{ $question->content }}
                    @if($question->is_required)
                    <span class="text-red-400 ml-0.5">*</span>
                    @endif
                </p>
                @if($question->help_text)
                <p class="text-xs text-gray-400 mb-3 ml-8">{{ $question->help_text }}</p>
                @endif

                {{-- Opções Likert --}}
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-2 mt-3 ml-0 sm:ml-8">
                    @foreach($question->options->sortBy('sort_order') as $option)
                    @php $selected = isset($answers[$question->id]) && $answers[$question->id] == $option->id; @endphp
                    <button wire:click="selectAnswer({{ $question->id }}, {{ $option->id }})"
                            class="relative group text-center px-2 py-3 rounded-xl border-2 transition-all duration-200 text-xs font-medium
                                   {{ $selected
                                       ? 'border-transparent text-white shadow-md'
                                       : 'border-gray-100 text-gray-600 hover:border-gray-200 hover:bg-gray-50' }}"
                            style="{{ $selected ? "background: {$color}; border-color: {$color};" : '' }}">
                        <span class="block text-lg font-bold mb-0.5 {{ $selected ? 'text-white/90' : 'text-gray-300' }}">
                            {{ (int)$option->value }}
                        </span>
                        {{ $option->content }}
                        @if($selected)
                        <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-white rounded-full flex items-center justify-center shadow">
                            <svg class="w-2.5 h-2.5" style="color: {{ $color }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Navegação --}}
    <div class="flex items-center justify-between gap-4 pb-8">
        <button wire:click="prevStep"
                @if($currentStep === 0) disabled @endif
                class="tu-btn px-5 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600
                       hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Anterior
        </button>

        <div class="text-xs text-gray-400">
            {{ array_sum(array_map(fn($i) => $this->answeredInStep($i), range(0, $total - 1))) }}
            / {{ collect($steps)->sum(fn($s) => count($s['questions'])) }} respondidas
        </div>

        @if($isLast)
        <button wire:click="submit" wire:loading.attr="disabled"
                class="tu-btn px-6 py-2.5 text-sm font-semibold rounded-xl text-white shadow-md
                       flex items-center gap-2 transition-all"
                style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}cc)">
            <svg wire:loading wire:target="submit" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span wire:loading.remove wire:target="submit">Concluir e ver resultado</span>
            <span wire:loading wire:target="submit">Calculando...</span>
        </button>
        @else
        <button wire:click="nextStep"
                class="tu-btn px-6 py-2.5 text-sm font-semibold rounded-xl text-white shadow-md
                       flex items-center gap-2"
                style="background: {{ $color }}">
            Próxima etapa
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        @endif
    </div>

</div>
