<div class="space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('platform.diagnostics.index') }}" wire:navigate
           class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold"
                     style="background: {{ $tool->color ?? '#6366F1' }}">
                    {{ $tool->code ?? substr($tool->name, 0, 2) }}
                </div>
                <h1 class="text-xl font-bold text-gray-900 truncate">{{ $tool->name }}</h1>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                    {{ $tool->is_published ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                    {{ $tool->is_published ? 'Publicada' : 'Rascunho' }}
                </span>
            </div>
            <p class="text-sm text-gray-400 mt-0.5 ml-9">
                {{ $this->questions->count() }} {{ Str::plural('pergunta', $this->questions->count()) }}
                @if($this->dimensions->count())
                    · {{ $this->dimensions->count() }} dimensões
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('platform.diagnostics.edit', $tool) }}" wire:navigate
               class="tu-btn px-3 py-2 text-sm rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50">
                Editar ferramenta
            </a>
            <button wire:click="openNew"
                    class="tu-btn px-4 py-2 text-sm font-semibold rounded-xl text-white flex items-center gap-1.5"
                    style="background: {{ $tool->color ?? '#6366F1' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova pergunta
            </button>
        </div>
    </div>

    {{-- Lista de perguntas --}}
    <div class="space-y-3">
        @forelse($this->questions as $i => $question)
        @php $dim = $question->dimension; @endphp
        <div class="tu-card p-4 hover:shadow-md transition-shadow" wire:key="q-{{ $question->id }}">
            <div class="flex items-start gap-4">

                {{-- Número + ordem --}}
                <div class="flex flex-col items-center gap-1 shrink-0 pt-0.5">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white"
                          style="background: {{ $dim?->color ?? $tool->color ?? '#6366F1' }}">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex flex-col gap-0.5">
                        <button wire:click="moveUp({{ $question->id }})"
                                class="p-0.5 rounded hover:bg-gray-100 text-gray-300 hover:text-gray-600 transition-colors"
                                @if($i === 0) disabled @endif>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        </button>
                        <button wire:click="moveDown({{ $question->id }})"
                                class="p-0.5 rounded hover:bg-gray-100 text-gray-300 hover:text-gray-600 transition-colors"
                                @if($i === $this->questions->count() - 1) disabled @endif>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Conteúdo --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 leading-relaxed">
                        {{ $question->content }}
                        @if($question->is_required)
                            <span class="text-red-400 ml-0.5">*</span>
                        @endif
                        @if($question->reverse_scored)
                            <span class="ml-1 text-xs bg-amber-50 text-amber-600 px-1.5 py-0.5 rounded">Invertida</span>
                        @endif
                    </p>

                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        {{-- Dimensão --}}
                        @if($dim)
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                              style="background: {{ $dim->color ?? '#6366F1' }}15; color: {{ $dim->color ?? '#6366F1' }}">
                            <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $dim->color ?? '#6366F1' }}"></span>
                            {{ $dim->name }}
                        </span>
                        @endif
                        {{-- Tipo --}}
                        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">
                            {{ $question->type->label() }}
                        </span>
                        {{-- Peso --}}
                        <span class="text-xs text-gray-400">Peso: {{ $question->weight }}</span>
                    </div>

                    {{-- Opções (preview) --}}
                    @if($question->options->count())
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @foreach($question->options->sortBy('sort_order') as $opt)
                        <span class="text-[11px] bg-gray-50 border border-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                            {{ $opt->content }}
                            <span class="text-gray-400 ml-0.5">({{ $opt->value }})</span>
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Ações --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button wire:click="openEdit({{ $question->id }})"
                            class="text-blue-500 hover:text-blue-700 text-sm font-medium px-2 py-1 rounded-lg hover:bg-blue-50 transition-colors">
                        Editar
                    </button>
                    <button wire:click="$set('confirmDeleteQuestion', {{ $question->id }})"
                            class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="tu-card p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400 mb-3">Nenhuma pergunta cadastrada.</p>
            <button wire:click="openNew"
                    class="tu-btn px-4 py-2 text-sm font-semibold rounded-xl text-white"
                    style="background: {{ $tool->color ?? '#6366F1' }}">
                Adicionar primeira pergunta
            </button>
        </div>
        @endforelse
    </div>

    {{-- ═══ PAINEL LATERAL ═══ --}}
    @if($showPanel)
    <div class="fixed inset-0 z-50 flex justify-end"
         x-data x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showPanel', false)"></div>

        {{-- Painel --}}
        <div class="relative w-full max-w-lg bg-white shadow-2xl flex flex-col overflow-y-auto"
             x-transition:enter="transition duration-300" x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0">

            {{-- Header do painel --}}
            <div class="sticky top-0 z-10 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">
                    {{ $editingId ? 'Editar Pergunta' : 'Nova Pergunta' }}
                </h2>
                <button wire:click="$set('showPanel', false)"
                        class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 px-6 py-5 space-y-5">

                {{-- Enunciado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Enunciado <span class="text-red-400">*</span>
                    </label>
                    <textarea wire:model="qContent" rows="3"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"
                              placeholder="Ex: Confio nas decisões tomadas pela minha liderança direta."></textarea>
                    @error('qContent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Texto de ajuda --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Texto de ajuda <span class="text-gray-400 text-xs">(opcional)</span></label>
                    <input wire:model="qHelp" type="text"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                           placeholder="Explicação adicional exibida abaixo da pergunta">
                </div>

                {{-- Tipo + Dimensão --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo</label>
                        <select wire:model="qType"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @foreach($this->questionTypes as $t)
                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Dimensão</label>
                        <select wire:model="qDimensionId"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <option value="">— Sem dimensão —</option>
                            @foreach($this->dimensions as $dim)
                            <option value="{{ $dim->id }}">{{ $dim->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Peso + Ordem + Flags --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Peso</label>
                        <input wire:model="qWeight" type="number" step="0.1" min="0.1"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        @error('qWeight') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ordem</label>
                        <input wire:model="qSortOrder" type="number" min="0"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="qRequired" type="checkbox"
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-300">
                        <span class="text-sm text-gray-700">Obrigatória</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="qReverse" type="checkbox"
                               class="rounded border-gray-300 text-amber-500 focus:ring-amber-300">
                        <span class="text-sm text-gray-700">
                            Item invertido
                            <span class="text-xs text-gray-400">(valor 5 = negativo)</span>
                        </span>
                    </label>
                </div>

                {{-- ── Opções de resposta ── --}}
                <div class="border-t border-gray-100 pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-700">Opções de Resposta</h3>
                        <button type="button" wire:click="fillLikert"
                                class="text-xs text-indigo-600 font-medium hover:text-indigo-700 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Preencher Likert padrão
                        </button>
                    </div>

                    {{-- Lista de opções --}}
                    <div class="space-y-2 mb-3">
                        @forelse($options as $oi => $opt)
                        <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2" wire:key="opt-{{ $oi }}">
                            <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-bold flex items-center justify-center shrink-0">
                                {{ $opt['value'] }}
                            </span>
                            <span class="flex-1 text-sm text-gray-700">{{ $opt['content'] }}</span>
                            <button type="button" wire:click="removeOption({{ $oi }})"
                                    class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50 transition-colors shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @empty
                        <p class="text-xs text-gray-400 text-center py-2">Nenhuma opção. Use "Preencher Likert padrão" ou adicione manualmente.</p>
                        @endforelse
                    </div>

                    {{-- Adicionar opção manual --}}
                    <div class="flex gap-2">
                        <input wire:model="optContent" type="text" placeholder="Texto da opção"
                               class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <input wire:model="optValue" type="number" step="0.5" placeholder="Valor"
                               class="w-20 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <button type="button" wire:click="addOption"
                                class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition-colors">
                            +
                        </button>
                    </div>
                    @error('optContent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Footer fixo --}}
            <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex gap-3">
                <button wire:click="$set('showPanel', false)"
                        class="flex-1 tu-btn py-2.5 text-sm font-semibold border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button wire:click="saveQuestion" wire:loading.attr="disabled"
                        class="flex-1 tu-btn py-2.5 text-sm font-semibold rounded-xl text-white flex items-center justify-center gap-2"
                        style="background: {{ $tool->color ?? '#6366F1' }}">
                    <svg wire:loading wire:target="saveQuestion" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span wire:loading.remove wire:target="saveQuestion">Salvar pergunta</span>
                    <span wire:loading wire:target="saveQuestion">Salvando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal exclusão --}}
    @if($confirmDeleteQuestion)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full">
            <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Excluir pergunta?</h3>
            <p class="text-sm text-gray-500 text-center mb-6">As respostas já coletadas para esta pergunta também serão excluídas.</p>
            <div class="flex gap-3">
                <button wire:click="$set('confirmDeleteQuestion', null)"
                        class="flex-1 tu-btn py-2.5 text-sm font-semibold border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button wire:click="deleteQuestion({{ $confirmDeleteQuestion }})"
                        class="flex-1 tu-btn py-2.5 text-sm font-semibold rounded-xl bg-red-600 text-white hover:bg-red-700">
                    Excluir
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
