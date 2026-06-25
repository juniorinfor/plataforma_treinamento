<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Prompts de IA</h1>
            <p class="text-sm text-gray-500 mt-1">
                Defina o prompt usado para gerar o relatório de cada diagnóstico.
            </p>
        </div>
        <a href="{{ route('platform.diagnostics.index') }}" wire:navigate
           class="text-sm text-gray-400 hover:text-gray-700 inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Ferramentas
        </a>
    </div>

    {{-- Como funciona --}}
    <div class="tu-card p-4 border-l-4 border-indigo-400 bg-indigo-50/40">
        <p class="text-sm text-indigo-900 font-semibold mb-1">Como o prompt é usado</p>
        <p class="text-xs text-indigo-700 leading-relaxed">
            O sistema combina o seu prompt com os <strong>dados do diagnóstico</strong>
            (pontuação global, scores por índice e as <strong>perguntas aplicadas</strong>) e adiciona
            automaticamente o <strong>formato de saída em JSON</strong>. Escreva aqui apenas a instrução/persona
            e a orientação de análise. Em branco, usamos uma instrução padrão.
        </p>
    </div>

    {{-- Lista de diagnósticos --}}
    @forelse($tools as $tool)
    <div class="tu-card p-5" wire:key="prompt-{{ $tool->id }}">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-xs shrink-0"
                 style="background: {{ $tool->color ?? '#6366F1' }}">
                {{ $tool->code ?? substr($tool->name, 0, 2) }}
            </div>
            <div>
                <p class="font-semibold text-gray-900">{{ $tool->name }}</p>
                <p class="text-xs text-gray-400">{{ $tool->short_description }}</p>
            </div>
        </div>

        <textarea wire:model="prompts.{{ $tool->id }}" rows="6"
                  placeholder="{{ \App\Services\DiagnosticReportService::DEFAULT_INSTRUCTION }}"
                  class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm font-mono leading-relaxed
                         focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none"></textarea>

        <div class="flex items-center justify-end gap-3 mt-3">
            @if($justSaved === $tool->id)
            <span class="text-xs text-emerald-600 font-semibold">✓ Salvo</span>
            @endif
            <button wire:click="save({{ $tool->id }})" wire:loading.attr="disabled" wire:target="save({{ $tool->id }})"
                    class="px-5 py-2 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white
                           disabled:opacity-60">
                Salvar prompt
            </button>
        </div>
    </div>
    @empty
    <div class="tu-card p-12 text-center text-gray-400">
        Nenhum diagnóstico cadastrado ainda.
    </div>
    @endforelse
</div>
