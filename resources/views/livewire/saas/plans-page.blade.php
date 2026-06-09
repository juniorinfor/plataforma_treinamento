<div class="max-w-5xl mx-auto space-y-8 animate-fade-in">

    {{-- ── Header ── --}}
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900">Planos Executive Map</h1>
        <p class="text-gray-500 mt-2">Escolha o plano ideal para sua empresa</p>
    </div>

    {{-- ── Assinatura atual ── --}}
    @if($this->currentSubscription)
    @php $sub = $this->currentSubscription; @endphp
    <div class="tu-card p-4 flex items-center justify-between flex-wrap gap-3 bg-indigo-50 border border-indigo-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-indigo-900 text-sm">Plano atual: {{ $sub->plan?->name }}</p>
                <p class="text-xs text-indigo-500">
                    {{ $sub->cycleLabel() }}
                    @if($sub->next_due_date)
                     · Próximo vencimento: {{ $sub->next_due_date->format('d/m/Y') }}
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('billing') }}" wire:navigate
           class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">
            Gerenciar assinatura →
        </a>
    </div>

    {{-- Pagamento pendente --}}
    @if($this->pendingInvoice)
    @php $inv = $this->pendingInvoice; @endphp
    <div class="tu-card p-4 bg-amber-50 border border-amber-200 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Pagamento pendente — R$ {{ number_format($inv->amount, 2, ',', '.') }}</p>
                <p class="text-xs text-amber-600">Vencimento: {{ $inv->due_at?->format('d/m/Y') }}</p>
            </div>
        </div>
        @if($inv->payment_url)
        <a href="{{ $inv->payment_url }}" target="_blank"
           class="text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded-lg transition-colors">
            Pagar agora
        </a>
        @endif
    </div>
    @endif
    @endif

    {{-- ── Toggle ciclo ── --}}
    <div class="flex items-center justify-center gap-3">
        <span class="text-sm font-medium {{ $cycle === 'MONTHLY' ? 'text-gray-900' : 'text-gray-400' }}">Mensal</span>
        <button wire:click="$set('cycle', {{ $cycle === 'MONTHLY' ? "'YEARLY'" : "'MONTHLY'" }})"
                class="relative w-12 h-6 rounded-full transition-colors duration-200
                       {{ $cycle === 'YEARLY' ? 'bg-indigo-600' : 'bg-gray-200' }}">
            <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200
                         {{ $cycle === 'YEARLY' ? 'translate-x-6' : 'translate-x-0.5' }}"></span>
        </button>
        <span class="text-sm font-medium {{ $cycle === 'YEARLY' ? 'text-gray-900' : 'text-gray-400' }}">
            Anual
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full ml-1">-20%</span>
        </span>
    </div>

    {{-- ── Cards de planos ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($this->plans as $i => $plan)
        @php
            $price   = $cycle === 'YEARLY' ? $plan->price_yearly : $plan->price_monthly;
            $popular = $i === 1;
            $isCurrent = $this->currentSubscription?->plan_id === $plan->id;
        @endphp
        <div class="tu-card p-6 relative {{ $popular ? 'ring-2 ring-indigo-500' : '' }}
                    {{ $isCurrent ? 'ring-2 ring-emerald-400' : '' }}">

            @if($popular && !$isCurrent)
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1
                         bg-indigo-600 text-white text-[10px] font-bold rounded-full tracking-wide uppercase">
                Mais popular
            </span>
            @endif
            @if($isCurrent)
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1
                         bg-emerald-500 text-white text-[10px] font-bold rounded-full tracking-wide uppercase">
                Plano atual
            </span>
            @endif

            <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
            <p class="text-sm text-gray-500 mt-1 min-h-[36px]">{{ $plan->description }}</p>

            <div class="mt-4">
                <span class="text-4xl font-extrabold text-gray-900">
                    R${{ number_format($price, 0, ',', '.') }}
                </span>
                <span class="text-gray-400">/{{ $cycle === 'YEARLY' ? 'ano' : 'mês' }}</span>
            </div>
            @if($cycle === 'MONTHLY' && $plan->price_yearly)
            <p class="text-xs text-gray-400 mt-0.5">
                ou R${{ number_format($plan->price_yearly, 0, ',', '.') }}/ano
            </p>
            @endif

            {{-- Features --}}
            <ul class="mt-5 space-y-2.5">
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Até {{ $plan->max_users > 9999 ? 'ilimitados' : $plan->max_users }} usuários
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ $plan->max_courses > 9999 ? 'Cursos ilimitados' : $plan->max_courses . ' cursos' }}
                </li>
                @if($plan->features)
                @foreach(array_slice((array) $plan->features, 0, 4) as $feature)
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    {{ ucfirst(str_replace('_', ' ', $feature)) }}
                </li>
                @endforeach
                @endif
            </ul>

            {{-- CTA --}}
            @if($isCurrent)
            <div class="mt-6 text-center text-sm font-semibold text-emerald-600 bg-emerald-50 py-2.5 rounded-xl">
                ✓ Plano ativo
            </div>
            @else
            <button wire:click="openCheckout({{ $plan->id }})"
                    class="mt-6 w-full py-2.5 rounded-xl font-semibold text-sm transition-all
                           {{ $popular
                               ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm'
                               : 'border-2 border-gray-200 hover:border-indigo-300 text-gray-700 hover:text-indigo-700' }}">
                Assinar {{ $plan->name }}
            </button>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ── Modal de Checkout ── --}}
    @if($showCheckout)
    @php $plan = $this->plans->find($checkoutPlanId); @endphp
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         x-data wire:click.self="$wire.set('showCheckout', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5">

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Confirmar assinatura</h2>
                <button wire:click="$set('showCheckout', false)"
                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($plan)
            {{-- Resumo --}}
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Plano selecionado</p>
                <p class="font-bold text-gray-900">{{ $plan->name }}</p>
                <p class="text-2xl font-black text-indigo-600 mt-1">
                    R$ {{ number_format($cycle === 'YEARLY' ? $plan->price_yearly : $plan->price_monthly, 2, ',', '.') }}
                    <span class="text-sm font-normal text-gray-400">/{{ $cycle === 'YEARLY' ? 'ano' : 'mês' }}</span>
                </p>
            </div>

            {{-- Forma de pagamento --}}
            <div>
                <p class="text-sm font-semibold text-gray-700 mb-2">Forma de pagamento</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['PIX' => 'PIX', 'BOLETO' => 'Boleto'] as $val => $lbl)
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all
                                  {{ $billingType === $val ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" wire:model="billingType" value="{{ $val }}" class="sr-only">
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0
                                    {{ $billingType === $val ? 'border-indigo-500' : 'border-gray-300' }}">
                            @if($billingType === $val)
                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
                            @endif
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            @if($checkoutError)
            <p class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ $checkoutError }}</p>
            @endif

            <button wire:click="confirmSubscription"
                    wire:loading.attr="disabled"
                    wire:target="confirmSubscription"
                    class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold
                           transition-all disabled:opacity-60 flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="confirmSubscription">
                    Confirmar e gerar cobrança
                </span>
                <span wire:loading wire:target="confirmSubscription" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Processando...
                </span>
            </button>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Modal de Pagamento (PIX / Boleto) ── --}}
    @if($showPayment)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center mx-auto">
                <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Assinatura criada!</h2>
            <p class="text-sm text-gray-500">Realize o pagamento para ativar seu plano.</p>

            @if($pixQrCode)
            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs font-semibold text-gray-500 mb-2">QR Code PIX</p>
                <img src="data:image/png;base64,{{ $pixQrCode }}" alt="QR Code PIX"
                     class="w-48 h-48 mx-auto rounded-lg">
            </div>
            @endif

            @if($paymentUrl)
            <a href="{{ $paymentUrl }}" target="_blank"
               class="block w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition-colors">
                {{ $billingType === 'BOLETO' ? 'Abrir boleto' : 'Abrir link de pagamento' }}
            </a>
            @endif

            <button wire:click="$set('showPayment', false)"
                    class="block w-full py-2.5 rounded-xl border-2 border-gray-200 text-gray-600
                           hover:bg-gray-50 text-sm font-semibold transition-colors">
                Fechar
            </button>
        </div>
    </div>
    @endif
</div>
