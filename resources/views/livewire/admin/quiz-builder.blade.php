<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">

    {{-- Navegação --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.courses') }}" wire:navigate class="hover:text-gray-700">Cursos</a>
        <span>/</span>
        <a href="{{ route('admin.courses.builder', $this->lesson->module->course_id) }}" wire:navigate class="hover:text-gray-700">{{ $this->lesson->module->course->title }}</a>
        <span>/</span>
        <span class="text-gray-700 font-medium">{{ $this->lesson->title }}</span>
    </nav>

    {{-- Cabeçalho --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Construtor de prova</h1>
            <p class="text-sm text-gray-500 mt-1">Configure a prova e adicione as questões.</p>
        </div>
        <a href="{{ route('admin.courses.builder', $this->lesson->module->course_id) }}" wire:navigate
           class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50 shrink-0">
            ← Voltar ao construtor
        </a>
    </div>

    {{-- ── Configurações da prova ── --}}
    <div class="tu-card overflow-hidden">
        <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-bold text-gray-700">Configurações da prova</p>
            <div x-data="{ saved: false }" x-init="$wire.on('settings-saved', () => { saved = true; setTimeout(() => saved = false, 3000) })">
                <span x-show="saved" x-transition class="text-xs font-semibold text-green-600">✓ Salvo!</span>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título da prova</label>
                    <input type="text" wire:model="quizTitle" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @error('quizTitle') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nota mínima (%)</label>
                    <input type="number" min="1" max="100" wire:model="passingScore"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @error('passingScore') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">XP ao concluir</label>
                    <input type="number" min="0" wire:model="xpReward"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tempo limite (min)</label>
                    <input type="number" min="1" max="300" wire:model="timeLimitMinutes" placeholder="Sem limite"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tentativas máximas</label>
                    <input type="number" min="1" max="99" wire:model="maxAttempts" placeholder="Ilimitado"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                </div>
            </div>

            <div class="flex flex-wrap gap-6 pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" wire:model="shuffleQuestions" class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-sm font-medium text-gray-700">Embaralhar questões</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" wire:model="heartsEnabled" class="w-4 h-4 rounded text-indigo-600">
                    <span class="text-sm font-medium text-gray-700">Usar corações</span>
                </label>
                @if($heartsEnabled)
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Corações:</span>
                    <input type="number" min="1" max="10" wire:model="heartsCount"
                           class="w-16 rounded-lg border border-gray-200 px-2.5 py-1.5 text-sm text-center focus:border-indigo-400 outline-none">
                </div>
                @endif
            </div>

            <div class="pt-1">
                <button wire:click="saveSettings"
                        class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">
                    Salvar configurações
                </button>
            </div>
        </div>
    </div>

    {{-- ── Questões ── --}}
    <div class="tu-card overflow-hidden">
        <div class="px-5 py-3.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-bold text-gray-700">
                Questões
                @if($this->questions->count())
                <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-700">{{ $this->questions->count() }}</span>
                @endif
            </p>
            <button wire:click="openNewQuestion"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nova questão
            </button>
        </div>

        @if($this->questions->isEmpty())
        <div class="p-10 text-center text-sm text-gray-400">
            Nenhuma questão ainda. Clique em "Nova questão" para começar.
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($this->questions as $qi => $question)
            <div class="px-5 py-4 hover:bg-gray-50" wire:key="q-{{ $question->id }}">
                <div class="flex items-start gap-3">
                    {{-- Número + tipo --}}
                    <div class="shrink-0 text-center w-8 pt-0.5">
                        <span class="text-xs font-bold text-gray-400">{{ $qi + 1 }}</span>
                    </div>

                    {{-- Conteúdo --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            @php
                                $typeColors = [
                                    'multiple_choice' => 'bg-blue-50 text-blue-600',
                                    'true_false'      => 'bg-green-50 text-green-600',
                                    'fill_blank'      => 'bg-orange-50 text-orange-600',
                                ];
                                $typeLabels = [
                                    'multiple_choice' => 'Múltipla Escolha',
                                    'true_false'      => 'V / F',
                                    'fill_blank'      => 'Lacuna',
                                ];
                            @endphp
                            <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $typeColors[$question->type->value] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $typeLabels[$question->type->value] ?? $question->type->value }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $question->points }} pt{{ $question->points !== 1 ? 's' : '' }}</span>
                        </div>
                        <p class="text-sm text-gray-800 line-clamp-2">{{ $question->content }}</p>

                        {{-- Preview das alternativas --}}
                        @if($question->type->value === 'multiple_choice')
                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                            @foreach($question->options as $opt)
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $opt->is_correct ? 'bg-green-50 text-green-700 font-semibold' : 'bg-gray-100 text-gray-500' }}">
                                {{ $opt->is_correct ? '✓' : '·' }} {{ Str::limit($opt->content, 30) }}
                            </span>
                            @endforeach
                        </div>
                        @elseif($question->type->value === 'true_false')
                        @php $correctTF = $question->options->firstWhere('is_correct', true); @endphp
                        <p class="mt-1 text-xs text-green-600 font-medium">✓ {{ $correctTF?->content }}</p>
                        @elseif($question->type->value === 'fill_blank')
                        @php $answer = $question->options->first(); @endphp
                        <p class="mt-1 text-xs text-orange-600 font-medium">Resposta: {{ $answer?->content }}</p>
                        @endif
                    </div>

                    {{-- Ações --}}
                    <div class="flex items-center gap-1 shrink-0 mt-0.5">
                        <button wire:click="moveQuestion({{ $question->id }}, 'up')" @disabled($qi === 0)
                                class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button wire:click="moveQuestion({{ $question->id }}, 'down')" @disabled($qi === $this->questions->count() - 1)
                                class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-400 disabled:opacity-30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <button wire:click="openEditQuestion({{ $question->id }})"
                                class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button wire:click="deleteQuestion({{ $question->id }})"
                                wire:confirm="Excluir esta questão?"
                                class="p-1.5 rounded-lg hover:bg-red-50 text-red-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Resumo de pontuação --}}
        @php $totalPoints = $this->questions->sum('points'); @endphp
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-500">{{ $this->questions->count() }} questões · {{ $totalPoints }} pontos no total</span>
            <span class="text-xs text-gray-500">Mínimo: {{ round($totalPoints * $passingScore / 100, 1) }} pts ({{ $passingScore }}%)</span>
        </div>
        @endif
    </div>

    {{-- ── Modal de questão ── --}}
    @if($showQuestionModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-start justify-center p-4 pt-16 overflow-y-auto"
         wire:click.self="$set('showQuestionModal', false)">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mb-8">

            {{-- Cabeçalho do modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">
                    @if($questionStep === 'type')
                        Nova questão — escolha o tipo
                    @else
                        {{ $questionId ? 'Editar questão' : 'Nova questão' }}
                        @if($questionType)
                        <span class="ml-2 text-sm font-normal text-gray-400">
                            — {{ ['multiple_choice' => 'Múltipla Escolha', 'true_false' => 'Verdadeiro/Falso', 'fill_blank' => 'Preencher Lacuna'][$questionType] ?? '' }}
                        </span>
                        @endif
                    @endif
                </h3>
                <button wire:click="$set('showQuestionModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6">

                {{-- STEP 1: seleção de tipo --}}
                @if($questionStep === 'type')
                <div class="grid grid-cols-1 gap-3">
                    <button wire:click="selectType('multiple_choice')"
                            class="flex items-start gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-blue-400 hover:bg-blue-50 text-left transition-colors">
                        <span class="text-2xl mt-0.5">🔵</span>
                        <div>
                            <p class="font-bold text-gray-900">Múltipla Escolha</p>
                            <p class="text-sm text-gray-500 mt-0.5">2 a 4 alternativas, uma ou mais corretas</p>
                        </div>
                    </button>
                    <button wire:click="selectType('true_false')"
                            class="flex items-start gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-green-400 hover:bg-green-50 text-left transition-colors">
                        <span class="text-2xl mt-0.5">✅</span>
                        <div>
                            <p class="font-bold text-gray-900">Verdadeiro ou Falso</p>
                            <p class="text-sm text-gray-500 mt-0.5">O aluno escolhe entre Verdadeiro e Falso</p>
                        </div>
                    </button>
                    <button wire:click="selectType('fill_blank')"
                            class="flex items-start gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-orange-400 hover:bg-orange-50 text-left transition-colors">
                        <span class="text-2xl mt-0.5">✏️</span>
                        <div>
                            <p class="font-bold text-gray-900">Preencher Lacuna</p>
                            <p class="text-sm text-gray-500 mt-0.5">O aluno digita a resposta. Comparação automática (sem distinção de maiúsculas)</p>
                        </div>
                    </button>
                </div>

                {{-- STEP 2: formulário de questão --}}
                @elseif($questionStep === 'form')
                <div class="space-y-4">

                    {{-- Enunciado --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Enunciado</label>
                        <textarea wire:model="questionContent" rows="3" autofocus
                                  placeholder="Digite a pergunta aqui..."
                                  class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none resize-none"></textarea>
                        @error('questionContent') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pontos --}}
                    <div class="flex items-center gap-4">
                        <div class="w-28">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pontos</label>
                            <input type="number" min="1" max="100" wire:model="questionPoints"
                                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none text-center">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Explicação (exibida após resposta)</label>
                            <input type="text" wire:model="questionExplain" placeholder="Opcional..."
                                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                        </div>
                    </div>

                    {{-- Alternativas por tipo --}}
                    @if($questionType === 'multiple_choice')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alternativas <span class="text-gray-400 font-normal">(marque a(s) correta(s))</span></label>
                        @error('questionOptions') <p class="text-xs text-red-500 mb-2">{{ $message }}</p> @enderror
                        <div class="space-y-2">
                            @foreach($questionOptions as $i => $opt)
                            <div class="flex items-center gap-2" wire:key="opt-{{ $i }}">
                                <input type="checkbox" wire:model="questionOptions.{{ $i }}.is_correct"
                                       class="w-4 h-4 rounded text-green-600 shrink-0">
                                <input type="text" wire:model="questionOptions.{{ $i }}.content"
                                       placeholder="Texto da alternativa {{ chr(65 + $i) }}"
                                       class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                                @if(count($questionOptions) > 2)
                                <button wire:click="removeOption({{ $i }})" class="p-1.5 text-gray-300 hover:text-red-400 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                @else
                                <span class="w-7 shrink-0"></span>
                                @endif
                            </div>
                            @error("questionOptions.{$i}.content") <p class="text-xs text-red-500 ml-6">{{ $message }}</p> @enderror
                            @endforeach
                        </div>
                        @if(count($questionOptions) < 4)
                        <button wire:click="addOption" class="mt-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                            + Adicionar alternativa
                        </button>
                        @endif
                    </div>

                    @elseif($questionType === 'true_false')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Resposta correta</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer
                                {{ $tfCorrect === 'true' ? 'border-green-400 bg-green-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <input type="radio" wire:model="tfCorrect" value="true" class="text-green-600">
                                <span class="text-sm font-semibold {{ $tfCorrect === 'true' ? 'text-green-700' : 'text-gray-700' }}">Verdadeiro</span>
                            </label>
                            <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 cursor-pointer
                                {{ $tfCorrect === 'false' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                <input type="radio" wire:model="tfCorrect" value="false" class="text-red-500">
                                <span class="text-sm font-semibold {{ $tfCorrect === 'false' ? 'text-red-700' : 'text-gray-700' }}">Falso</span>
                            </label>
                        </div>
                    </div>

                    @elseif($questionType === 'fill_blank')
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Resposta esperada</label>
                        <p class="text-xs text-gray-400 mb-2">A comparação ignorará maiúsculas/minúsculas.</p>
                        <input type="text" wire:model="fillAnswer" placeholder="Ex: fotossíntese"
                               class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                        @error('fillAnswer') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    {{-- Botões --}}
                    <div class="flex justify-between items-center pt-1">
                        @if(!$questionId)
                        <button wire:click="$set('questionStep', 'type')"
                                class="text-sm text-gray-400 hover:text-gray-600">← Voltar</button>
                        @else
                        <span></span>
                        @endif
                        <div class="flex gap-2">
                            <button wire:click="$set('showQuestionModal', false)"
                                    class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50">Cancelar</button>
                            <button wire:click="saveQuestion"
                                    class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">
                                {{ $questionId ? 'Salvar alterações' : 'Adicionar questão' }}
                            </button>
                        </div>
                    </div>

                </div>
                @endif

            </div>
        </div>
    </div>
    @endif

</div>
