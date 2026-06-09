<div class="space-y-8 animate-fade-in">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ferramentas de Diagnóstico</h1>
            <p class="text-gray-500 mt-1">Avalie-se, descubra seus pontos fortes e trace seu plano de evolução.</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 bg-gray-50 rounded-xl px-4 py-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Os resultados são confidenciais
        </div>
    </div>

    {{-- Tool Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 stagger-children">
        @forelse($this->tools as $tool)
        @php
            $assessment = $this->myAssessments->get($tool->id);
            $isCompleted = $assessment?->status === \App\Enums\DiagnosticAssessmentStatus::Completed;
            $isInProgress = $assessment && !$isCompleted;
            $color = $tool->color ?? '#6366F1';
        @endphp

        <div class="tu-card overflow-hidden hover-lift animate-slide-up" wire:key="tool-{{ $tool->id }}">
            {{-- Color bar --}}
            <div class="h-2 w-full" style="background: {{ $color }}"></div>

            <div class="p-6">
                {{-- Top row: icon + badges --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-md"
                         style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}bb)">
                        @if($tool->icon === 'chart-pie')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                        @elseif($tool->icon === 'user-circle')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @elseif($tool->icon === 'shield-check')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if($tool->type === \App\Enums\DiagnosticToolType::Composite)
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full"
                              style="background: {{ $color }}15; color: {{ $color }}">
                            AS SCORE®
                        </span>
                        @endif
                        @if($isCompleted)
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider
                                     px-2 py-1 rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Concluído
                        </span>
                        @elseif($isInProgress)
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full bg-amber-50 text-amber-600">
                            Em andamento
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Name + description --}}
                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $tool->name }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-5">{{ $tool->short_description }}</p>

                {{-- Score (se concluído) --}}
                @if($isCompleted && $assessment->global_score !== null)
                <div class="mb-5 p-3 rounded-xl" style="background: {{ $color }}10">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold" style="color: {{ $color }}">
                            Seu {{ $assessment->global_label ?? 'Score' }}
                        </span>
                        <span class="text-lg font-black" style="color: {{ $color }}">
                            {{ number_format($assessment->global_score, 1) }}
                            <span class="text-xs font-normal text-gray-400">/ 100</span>
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-700"
                             style="width: {{ $assessment->global_score }}%; background: {{ $color }}"></div>
                    </div>
                </div>
                @endif

                {{-- Meta info --}}
                <div class="flex items-center gap-4 text-xs text-gray-400 mb-5">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $tool->estimated_minutes }} min
                    </span>
                    <span class="flex items-center gap-1 text-yellow-500 font-semibold">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        +{{ $tool->xp_reward }} XP
                    </span>
                    @if($tool->type === 'composite')
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        5 índices
                    </span>
                    @endif
                    @if($tool->requires_review)
                    <span class="flex items-center gap-1 text-sky-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Com consultor
                    </span>
                    @endif
                </div>

                {{-- CTA --}}
                @if($isCompleted)
                <button wire:click="viewResult({{ $assessment->id }})"
                        class="w-full tu-btn py-2.5 text-sm font-semibold rounded-xl text-white"
                        style="background: {{ $color }}">
                    Ver meu resultado
                </button>
                @elseif($isInProgress)
                <button wire:click="startDiagnostic({{ $tool->id }})"
                        class="w-full tu-btn py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50">
                    Continuar de onde parei
                </button>
                @else
                <button wire:click="startDiagnostic({{ $tool->id }})"
                        class="w-full tu-btn py-2.5 text-sm font-semibold rounded-xl text-white transition-all"
                        style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}cc)">
                    Iniciar diagnóstico
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="md:col-span-2 tu-card p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-400">Nenhuma ferramenta disponível no momento.</p>
        </div>
        @endforelse
    </div>

    {{-- O que são estes diagnósticos --}}
    <div class="tu-card p-6 border-l-4 border-indigo-400">
        <h2 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Como funcionam os diagnósticos?
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-600">
            <div class="flex items-start gap-3">
                <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                <div>
                    <p class="font-semibold text-gray-800 mb-0.5">Responda o questionário</p>
                    <p class="leading-relaxed">Perguntas simples e objetivas em escala de concordância.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                <div>
                    <p class="font-semibold text-gray-800 mb-0.5">Veja seu resultado</p>
                    <p class="leading-relaxed">Score por dimensão, gráficos e análise detalhada do seu perfil.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                <div>
                    <p class="font-semibold text-gray-800 mb-0.5">Receba seu plano</p>
                    <p class="leading-relaxed">Treinamentos recomendados e plano de ação personalizado.</p>
                </div>
            </div>
        </div>
    </div>

</div>
