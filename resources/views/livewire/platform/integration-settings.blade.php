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
                    <p class="text-xs text-gray-400">Provedores usados para gerar os relatórios de diagnóstico. Configure Claude e OpenAI ao mesmo tempo e escolha qual fica ativo.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button wire:click="openCreateAi('claude')"
                        class="px-3.5 py-2 text-xs font-semibold rounded-xl border-2 border-violet-200 text-violet-700 hover:bg-violet-50">
                    + Claude
                </button>
                <button wire:click="openCreateAi('openai')"
                        class="px-3.5 py-2 text-xs font-semibold rounded-xl border-2 border-emerald-200 text-emerald-700 hover:bg-emerald-50">
                    + OpenAI (GPT)
                </button>
            </div>
        </div>

        {{-- Lista de provedores configurados --}}
        @if($this->aiProviders->isEmpty())
        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
            <p class="text-sm text-gray-400">Nenhum provedor de IA configurado ainda.</p>
            <p class="text-xs text-gray-400 mt-1">Adicione o Claude ou a OpenAI acima para começar a gerar relatórios com IA.</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($this->aiProviders as $provider)
            <div class="flex items-center justify-between gap-3 flex-wrap p-4 rounded-xl border {{ $provider->is_active ? 'border-emerald-200 bg-emerald-50/40' : 'border-gray-100' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $provider->driver === 'openai' ? 'bg-emerald-100 text-emerald-600' : 'bg-violet-100 text-violet-600' }}">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">
                            {{ $provider->name }}
                            @if($provider->is_active)
                            <span class="ml-1.5 text-[10px] font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full align-middle">ATIVO</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ $provider->driver === 'openai' ? 'OpenAI' : 'Claude (Anthropic)' }}
                            @if($provider->model) &middot; {{ $provider->model }} @endif
                            @if(filled($provider->api_key)) &middot; chave configurada @else &middot; sem chave @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($this->aiTestResults[$provider->id] ?? null)
                        @if($this->aiTestResults[$provider->id] === 'ok')
                        <span class="text-xs font-semibold text-emerald-600">✓ OK</span>
                        @else
                        <span class="text-xs font-semibold text-red-500">✕ Falhou</span>
                        @endif
                    @endif
                    <button wire:click="testAi({{ $provider->id }})" wire:loading.attr="disabled" wire:target="testAi({{ $provider->id }})"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 disabled:opacity-60">
                        Testar
                    </button>
                    @if(!$provider->is_active)
                    <button wire:click="setActiveAi({{ $provider->id }})"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">
                        Ativar
                    </button>
                    @endif
                    <button wire:click="openEditAi({{ $provider->id }})"
                            class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600" title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="deleteAi({{ $provider->id }})" wire:confirm="Remover o provedor '{{ $provider->name }}'?"
                            class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500" title="Excluir">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <p class="text-xs text-gray-400 border-t border-gray-100 pt-4">
            Os prompts de cada diagnóstico ficam em
            <a href="{{ route('platform.diagnostics.prompts') }}" wire:navigate class="text-indigo-500 font-medium">Diagnósticos → Prompts de IA</a>.
        </p>
    </div>

    {{-- ── Modal — Criar/editar provedor de IA ── --}}
    @if($showAiModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5);"
         x-data wire:click.self="$wire.set('showAiModal', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">{{ $editingAiId ? 'Editar provedor de IA' : 'Novo provedor de IA' }}</h3>
                <button wire:click="$set('showAiModal', false)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nome</label>
                    <input type="text" wire:model="aiName" placeholder="Ex.: Claude (Anthropic)"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    @error('aiName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
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
                               placeholder="{{ $aiDriver === 'openai' ? 'gpt-4o' : 'claude-3-5-sonnet-20241022' }}"
                               class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm font-mono focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    </div>
                </div>
                @error('aiModel') <p class="text-xs text-red-500 -mt-2">{{ $message }}</p> @enderror

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Chave de API
                        @if($aiHasKey)
                        <span class="ml-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">configurada</span>
                        @endif
                    </label>
                    <input type="password" wire:model="aiKey" autocomplete="off"
                           placeholder="{{ $aiHasKey ? '•••••••••• (deixe em branco para manter)' : ($aiDriver === 'openai' ? 'Chave da OpenAI Platform' : 'Chave do Anthropic Console') }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-mono focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 outline-none">
                    <p class="text-xs text-gray-400 mt-1">Guardada <strong>criptografada</strong> no banco de dados.</p>
                    @error('aiKey') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
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

                <label class="flex items-center gap-2 cursor-pointer pt-1">
                    <input type="checkbox" wire:model="aiActive" class="rounded text-indigo-600">
                    <span class="text-sm text-gray-700">Definir como provedor ativo (usado para gerar os relatórios)</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button wire:click="$set('showAiModal', false)" class="tu-btn border border-gray-200 text-gray-600 text-sm px-4 py-2 rounded-xl">Cancelar</button>
                <button wire:click="saveAi" wire:loading.attr="disabled" wire:target="saveAi"
                        class="tu-btn tu-btn-primary text-sm px-5 py-2 rounded-xl disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveAi">Salvar</span>
                    <span wire:loading wire:target="saveAi">Salvando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
