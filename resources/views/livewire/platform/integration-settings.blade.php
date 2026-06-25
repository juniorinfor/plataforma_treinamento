<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Integrações</h1>
        <p class="text-gray-500 mt-1">Credenciais de serviços externos da plataforma.</p>
    </div>

    {{-- ── Asaas (gateway de pagamento) ── --}}
    <div class="tu-card p-6 space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900">Asaas — Pagamentos</h2>
                <p class="text-xs text-gray-400">PIX, boleto e assinaturas dos clientes.</p>
            </div>
        </div>

        {{-- Ambiente --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ambiente</label>
            <select wire:model="asaasEnv"
                    class="w-full sm:w-60 rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                <option value="sandbox">Sandbox (testes)</option>
                <option value="production">Produção</option>
            </select>
            @error('asaasEnv') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Chave de API --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Chave de API (Access Token)
                @if($hasKey)
                <span class="ml-2 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">configurada</span>
                @endif
            </label>
            <input type="password" wire:model="asaasKey" autocomplete="off"
                   placeholder="{{ $hasKey ? '•••••••••• (deixe em branco para manter)' : 'Cole aqui sua chave do Asaas' }}"
                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-mono focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
            <p class="text-xs text-gray-400 mt-1">
                A chave é guardada <strong>criptografada</strong>. Pegue em: Asaas → Integrações → Chave de API.
            </p>
            @error('asaasKey') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Webhook --}}
        <div class="border-t border-gray-100 pt-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">URL do Webhook</label>
            <div x-data="{ copied:false, copy(){ navigator.clipboard.writeText(@js($this->webhookUrl())); this.copied=true; setTimeout(()=>this.copied=false,1500);} }"
                 class="flex items-center gap-2 flex-wrap">
                <input type="text" readonly value="{{ $this->webhookUrl() }}"
                       class="flex-1 min-w-[240px] px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-600 font-mono">
                <button type="button" @click="copy()"
                        class="px-4 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white">
                    <span x-show="!copied">Copiar</span><span x-show="copied" x-cloak>Copiado!</span>
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-1">Cadastre esta URL no painel do Asaas (Notificações → Webhooks).</p>

            <label class="block text-sm font-semibold text-gray-700 mb-1.5 mt-4">Token do Webhook</label>
            <input type="text" wire:model="asaasWebhookToken" autocomplete="off"
                   placeholder="Token de segurança (o mesmo configurado no Asaas)"
                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-mono focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
            <p class="text-xs text-gray-400 mt-1">Validado no cabeçalho <code>asaas-access-token</code> de cada chamada.</p>
            @error('asaasWebhookToken') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Ações --}}
        <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-5">
            <div class="flex items-center gap-3">
                <button wire:click="testConnection" wire:loading.attr="disabled" wire:target="testConnection"
                        class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-60">
                    <span wire:loading.remove wire:target="testConnection">Testar conexão</span>
                    <span wire:loading wire:target="testConnection">Testando…</span>
                </button>
                @if($testResult === 'ok')
                <span class="text-sm font-semibold text-emerald-600">✓ Conexão OK</span>
                @elseif($testResult === 'fail')
                <span class="text-sm font-semibold text-red-500">✕ Falhou — verifique a chave/ambiente</span>
                @endif
            </div>
            <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                    class="px-6 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-60">
                <span wire:loading.remove wire:target="save">Salvar</span>
                <span wire:loading wire:target="save">Salvando…</span>
            </button>
        </div>
    </div>

    {{-- ── Inteligência Artificial (geração de relatórios) ── --}}
    <div class="tu-card p-6 space-y-5">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900">Inteligência Artificial</h2>
                    <p class="text-xs text-gray-400">Geração automática dos relatórios de diagnóstico.</p>
                </div>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <span class="text-sm text-gray-600">{{ $aiActive ? 'Ativa' : 'Inativa' }}</span>
                <button type="button" wire:click="$toggle('aiActive')"
                        class="relative w-12 h-6 rounded-full transition-colors {{ $aiActive ? 'bg-emerald-500' : 'bg-gray-200' }}">
                    <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform {{ $aiActive ? 'translate-x-6' : 'translate-x-0.5' }}"></span>
                </button>
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Provedor</label>
                <select wire:model="aiDriver"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    <option value="claude">Claude (Anthropic)</option>
                    <option value="openai">OpenAI</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Modelo</label>
                <input type="text" wire:model="aiModel"
                       placeholder="{{ $aiDriver === 'openai' ? 'ex.: gpt-4o' : 'ex.: claude-3-5-sonnet-20241022' }}"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-mono focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                @error('aiModel') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Chave de API
                @if($aiHasKey)
                <span class="ml-2 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">configurada</span>
                @endif
            </label>
            <input type="password" wire:model="aiKey" autocomplete="off"
                   placeholder="{{ $aiHasKey ? '•••••••••• (deixe em branco para manter)' : 'Cole aqui sua chave de API' }}"
                   class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-mono focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
            <p class="text-xs text-gray-400 mt-1">Guardada <strong>criptografada</strong>. Anthropic Console (Claude) ou OpenAI Platform.</p>
            @error('aiKey') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4 max-w-xs">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Máx. tokens</label>
                <input type="number" min="256" max="32000" wire:model="aiMaxTokens"
                       class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                @error('aiMaxTokens') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Temperatura</label>
                <input type="number" step="0.05" min="0" max="2" wire:model="aiTemperature"
                       class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                @error('aiTemperature') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-5">
            <div class="flex items-center gap-3">
                <button wire:click="testAi" wire:loading.attr="disabled" wire:target="testAi"
                        class="px-4 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-60">
                    <span wire:loading.remove wire:target="testAi">Testar IA</span>
                    <span wire:loading wire:target="testAi">Testando…</span>
                </button>
                @if($aiTestResult === 'ok')
                <span class="text-sm font-semibold text-emerald-600">✓ Resposta recebida</span>
                @elseif($aiTestResult === 'fail')
                <span class="text-sm font-semibold text-red-500">✕ Falhou — verifique a chave/modelo</span>
                @endif
            </div>
            <button wire:click="saveAi" wire:loading.attr="disabled" wire:target="saveAi"
                    class="px-6 py-2.5 text-sm font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-60">
                <span wire:loading.remove wire:target="saveAi">Salvar</span>
                <span wire:loading wire:target="saveAi">Salvando…</span>
            </button>
        </div>

        <p class="text-xs text-gray-400 border-t border-gray-100 pt-4">
            Os prompts de cada diagnóstico ficam em
            <a href="{{ route('platform.diagnostics.prompts') }}" wire:navigate class="text-indigo-500 font-medium">Diagnósticos → Prompts de IA</a>.
        </p>
    </div>
</div>
