<div class="space-y-6 animate-fade-in">
@php
    $user       = auth()->user();
    $canBilling = $user->canManageBilling();
    $isGestor   = $user->isGestor();
    $hasCompany = $user->company_id !== null;
@endphp

{{-- Flash --}}
@if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ╔══════════════════════════════════════════════════════════════════
     RAMO 1 — GESTOR / COMPANY_ADMIN
     Vê plano atual, invoices, pode trocar plano e cancelar
     ══════════════════════════════════════════════════════════════════ --}}
@if($isGestor)

<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Minha Assinatura</h1>
        <p class="text-sm text-gray-500 mt-0.5">Plano da empresa {{ $user->company?->name }}</p>
    </div>
</div>

{{-- Pagamento pendente --}}
@if($this->pendingInvoice)
@php $inv = $this->pendingInvoice; @endphp
<div class="tu-card p-4 bg-amber-50 border border-amber-200 flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
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

{{-- Card do plano atual --}}
@if($this->subscription)
@php
    $sub    = $this->subscription;
    $plan   = $sub->plan;
    $status = $sub->status;
    $statusColor = match($status?->value) {
        'active'   => 'text-emerald-700 bg-emerald-50 border-emerald-200',
        'trial'    => 'text-blue-700 bg-blue-50 border-blue-200',
        'past_due' => 'text-amber-700 bg-amber-50 border-amber-200',
        default    => 'text-red-700 bg-red-50 border-red-200',
    };
@endphp
<div class="tu-card p-6 space-y-5">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Plano atual</p>
            <p class="text-2xl font-black text-gray-900">{{ $plan?->name ?? '—' }}</p>
            <p class="text-sm text-gray-500 mt-0.5">{{ $sub->cycleLabel() }}</p>
        </div>
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border {{ $statusColor }}">
            {{ $status?->label() ?? '—' }}
        </span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        @if($plan && $canBilling)
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-400 font-semibold mb-1">Valor</p>
            <p class="text-xl font-black text-gray-900">
                R$ {{ number_format($sub->cycle === 'YEARLY' ? $plan->price_yearly : $plan->price_monthly, 2, ',', '.') }}
                <span class="text-xs font-normal text-gray-400">/{{ $sub->cycle === 'YEARLY' ? 'ano' : 'mês' }}</span>
            </p>
        </div>
        @endif
        @if($sub->next_due_date)
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-400 font-semibold mb-1">Próximo vencimento</p>
            <p class="text-xl font-bold text-gray-900">{{ $sub->next_due_date->format('d/m/Y') }}</p>
        </div>
        @endif
        @if($plan && $plan->max_users)
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-400 font-semibold mb-1">Colaboradores</p>
            <p class="text-xl font-bold text-gray-900">
                {{ $user->company->activeUsersCount() }}
                <span class="text-sm font-normal text-gray-400">/ {{ $plan->max_users > 9999 ? '∞' : $plan->max_users }}</span>
            </p>
        </div>
        @endif
    </div>

    @if($canBilling && $status?->isActive())
    @if(!$confirmCancel)
    <button wire:click="$set('confirmCancel', true)"
            class="text-sm font-semibold text-red-400 hover:text-red-600 hover:underline transition-colors">
        Cancelar assinatura
    </button>
    @else
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 space-y-3">
        <p class="text-sm font-semibold text-red-800">Tem certeza que deseja cancelar?</p>
        <p class="text-xs text-red-600">O acesso permanece até o final do período pago. Esta ação não pode ser desfeita.</p>
        <div class="flex gap-3">
            <button wire:click="cancelSubscription" wire:loading.attr="disabled"
                    class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-colors">
                Sim, cancelar
            </button>
            <button wire:click="$set('confirmCancel', false)"
                    class="px-4 py-2 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                Manter
            </button>
        </div>
    </div>
    @endif
    @endif
</div>

{{-- Histórico de cobranças --}}
@if($canBilling && $this->invoices->isNotEmpty())
<div class="tu-card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-50">
        <h2 class="font-bold text-gray-900">Histórico de Cobranças</h2>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($this->invoices as $invoice)
        <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">{{ $invoice->billing_type }} · Venc. {{ $invoice->due_at?->format('d/m/Y') ?? '—' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $invoice->statusColor() }}">
                    {{ $invoice->statusLabel() }}
                </span>
                @if($invoice->isPending() && $invoice->payment_url)
                <a href="{{ $invoice->payment_url }}" target="_blank"
                   class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Pagar →</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@else
{{-- Sem assinatura --}}
<div class="tu-card p-8 text-center space-y-3">
    <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
    </svg>
    <p class="text-gray-500 text-sm">Nenhuma assinatura ativa.</p>
</div>
@endif

{{-- Planos disponíveis (apenas gestor com billing) --}}
@if($canBilling)
<div>
    <h2 class="text-lg font-bold text-gray-900 mb-4">
        {{ $this->subscription ? 'Trocar de plano' : 'Escolher um plano' }}
    </h2>

    {{-- Toggle ciclo --}}
    <div class="flex items-center gap-3 mb-5">
        <span class="text-sm font-medium {{ $cycle === 'MONTHLY' ? 'text-gray-900' : 'text-gray-400' }}">Mensal</span>
        <button wire:click="$set('cycle', {{ $cycle === 'MONTHLY' ? "'YEARLY'" : "'MONTHLY'" }})"
                class="relative w-12 h-6 rounded-full transition-colors duration-200 {{ $cycle === 'YEARLY' ? 'bg-indigo-600' : 'bg-gray-200' }}">
            <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 {{ $cycle === 'YEARLY' ? 'translate-x-6' : 'translate-x-0.5' }}"></span>
        </button>
        <span class="text-sm font-medium {{ $cycle === 'YEARLY' ? 'text-gray-900' : 'text-gray-400' }}">
            Anual
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full ml-1">-20%</span>
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($this->plans as $i => $plan)
        @php
            $price     = $cycle === 'YEARLY' ? $plan->price_yearly : $plan->price_monthly;
            $popular   = $i === 1;
            $isCurrent = $this->subscription?->plan_id === $plan->id;
        @endphp
        <div class="tu-card p-5 relative {{ $popular && !$isCurrent ? 'ring-2 ring-indigo-500' : '' }} {{ $isCurrent ? 'ring-2 ring-emerald-400' : '' }}">
            @if($popular && !$isCurrent)
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 bg-indigo-600 text-white text-[10px] font-bold rounded-full tracking-wide uppercase">Mais popular</span>
            @endif
            @if($isCurrent)
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 bg-emerald-500 text-white text-[10px] font-bold rounded-full tracking-wide uppercase">Plano atual</span>
            @endif

            <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
            <p class="text-sm text-gray-400 mt-0.5 min-h-[36px]">{{ $plan->description }}</p>

            <div class="mt-3">
                <span class="text-3xl font-extrabold text-gray-900">R${{ number_format($price, 0, ',', '.') }}</span>
                <span class="text-gray-400 text-sm">/{{ $cycle === 'YEARLY' ? 'ano' : 'mês' }}</span>
            </div>

            <ul class="mt-3 space-y-2">
                <li class="flex items-center gap-2 text-xs text-gray-600">
                    <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Até {{ $plan->max_users > 9999 ? 'ilimitados' : $plan->max_users }} usuários
                </li>
                <li class="flex items-center gap-2 text-xs text-gray-600">
                    <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    {{ $plan->max_courses > 9999 ? 'Cursos ilimitados' : $plan->max_courses . ' cursos' }}
                </li>
            </ul>

            @if($isCurrent)
            <div class="mt-4 text-center text-xs font-semibold text-emerald-600 bg-emerald-50 py-2 rounded-xl">✓ Plano ativo</div>
            @else
            <button wire:click="openCheckout({{ $plan->id }})"
                    class="mt-4 w-full py-2 rounded-xl font-semibold text-sm transition-all
                           {{ $popular ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'border-2 border-gray-200 hover:border-indigo-300 text-gray-700 hover:text-indigo-700' }}">
                {{ $this->subscription ? 'Trocar para ' . $plan->name : 'Assinar ' . $plan->name }}
            </button>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ╔══════════════════════════════════════════════════════════════════
     RAMO 2 — COLABORADOR DE EMPRESA
     Vê que a empresa cobre o acesso; pode comprar cursos avulsos
     ══════════════════════════════════════════════════════════════════ --}}
@elseif($hasCompany)

<div>
    <h1 class="text-2xl font-bold text-gray-900">Minha Assinatura</h1>
</div>

<div class="tu-card p-6 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center shrink-0">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
    </div>
    <div>
        <p class="text-sm font-semibold text-gray-500 mb-0.5">Acesso fornecido pela empresa</p>
        <p class="text-xl font-black text-gray-900">{{ $user->company->name }}</p>
        @if($this->subscription?->plan)
        <p class="text-sm text-indigo-600 font-semibold mt-1">Plano {{ $this->subscription->plan->name }}</p>
        @endif
        <p class="text-xs text-gray-400 mt-2">
            Seu acesso à plataforma é gerenciado pela sua empresa. Para dúvidas sobre o plano, entre em contato com o gestor.
        </p>
    </div>
</div>

{{-- Cursos e pacotes individuais --}}
@if($this->availableProducts->isNotEmpty())
<div>
    <h2 class="text-lg font-bold text-gray-900 mb-1">Cursos e Pacotes Disponíveis</h2>
    <p class="text-sm text-gray-400 mb-4">Adquira conteúdos adicionais individualmente, para uso pessoal.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($this->availableProducts as $product)
        <div class="tu-card p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between gap-2">
                <h3 class="font-bold text-gray-900 text-sm leading-snug">{{ $product->name }}</h3>
                <span class="shrink-0 text-xs font-bold px-2 py-0.5 rounded-full
                             {{ $product->type->value === 'pacote' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                    {{ $product->type->label() }}
                </span>
            </div>
            @if($product->description)
            <p class="text-xs text-gray-400 leading-relaxed flex-1">{{ $product->description }}</p>
            @endif
            <div class="space-y-0.5">
                @if($product->price_once)
                <p class="text-base font-extrabold text-gray-900">
                    R$ {{ number_format($product->price_once, 2, ',', '.') }}
                    <span class="text-xs font-normal text-gray-400">compra única</span>
                </p>
                @endif
                @if($product->price_monthly)
                <p class="text-sm text-gray-500">
                    ou R$ {{ number_format($product->price_monthly, 2, ',', '.') }}/mês
                </p>
                @endif
            </div>
            <button wire:click="openProductCheckout({{ $product->id }})"
                    class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors">
                Adquirir
            </button>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ╔══════════════════════════════════════════════════════════════════
     RAMO 3 — COLABORADOR SEM EMPRESA (B2C)
     Pode adquirir produtos individuais
     ══════════════════════════════════════════════════════════════════ --}}
@else

<div>
    <h1 class="text-2xl font-bold text-gray-900">Minha Assinatura</h1>
    <p class="text-sm text-gray-400 mt-0.5">Adquira cursos e pacotes diretamente.</p>
</div>

<div class="tu-card p-5 flex items-center gap-4 bg-blue-50 border border-blue-100">
    <svg class="w-8 h-8 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-blue-800">
        Você ainda não tem um plano ativo. Escolha um curso ou pacote abaixo para começar.
    </p>
</div>

@if($this->availableProducts->isNotEmpty())
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($this->availableProducts as $product)
    <div class="tu-card p-5 flex flex-col gap-3">
        <div class="flex items-center justify-between gap-2">
            <h3 class="font-bold text-gray-900 text-sm leading-snug">{{ $product->name }}</h3>
            <span class="shrink-0 text-xs font-bold px-2 py-0.5 rounded-full
                         {{ $product->type->value === 'pacote' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                {{ $product->type->label() }}
            </span>
        </div>
        @if($product->description)
        <p class="text-xs text-gray-400 leading-relaxed flex-1">{{ $product->description }}</p>
        @endif
        <div class="space-y-0.5">
            @if($product->price_once)
            <p class="text-base font-extrabold text-gray-900">
                R$ {{ number_format($product->price_once, 2, ',', '.') }}
                <span class="text-xs font-normal text-gray-400">compra única</span>
            </p>
            @endif
            @if($product->price_monthly)
            <p class="text-sm text-gray-500">ou R$ {{ number_format($product->price_monthly, 2, ',', '.') }}/mês</p>
            @endif
        </div>
        <button wire:click="openProductCheckout({{ $product->id }})"
                class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors">
            Adquirir
        </button>
    </div>
    @endforeach
</div>
@else
<div class="tu-card p-10 text-center">
    <p class="text-gray-400 text-sm">Nenhum produto disponível no momento.</p>
</div>
@endif

@endif

{{-- ╔══════════════════════════════════════════════════════════════════
     MODAL — Checkout de plano (gestor)
     ══════════════════════════════════════════════════════════════════ --}}
@if($showCheckout)
@php $checkoutPlan = $this->plans->find($checkoutPlanId); @endphp
<div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     x-data wire:click.self="$wire.set('showCheckout', false)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Confirmar assinatura</h2>
            <button wire:click="$set('showCheckout', false)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if($checkoutPlan)
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Plano selecionado</p>
            <p class="font-bold text-gray-900">{{ $checkoutPlan->name }}</p>
            <p class="text-2xl font-black text-indigo-600 mt-1">
                R$ {{ number_format($cycle === 'YEARLY' ? $checkoutPlan->price_yearly : $checkoutPlan->price_monthly, 2, ',', '.') }}
                <span class="text-sm font-normal text-gray-400">/{{ $cycle === 'YEARLY' ? 'ano' : 'mês' }}</span>
            </p>
        </div>

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

        <button wire:click="confirmSubscription" wire:loading.attr="disabled" wire:target="confirmSubscription"
                class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition-all disabled:opacity-60 flex items-center justify-center gap-2">
            <span wire:loading.remove wire:target="confirmSubscription">Confirmar e gerar cobrança</span>
            <span wire:loading wire:target="confirmSubscription" class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Processando...
            </span>
        </button>
        @endif
    </div>
</div>
@endif

{{-- ╔══════════════════════════════════════════════════════════════════
     MODAL — Confirmação de pagamento pós-checkout
     ══════════════════════════════════════════════════════════════════ --}}
@if($showPayment)
<div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center space-y-4">
        <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900">Assinatura criada!</h2>
        <p class="text-sm text-gray-500">Realize o pagamento para ativar o plano.</p>

        @if($pixQrCode)
        <div class="bg-gray-50 rounded-xl p-3">
            <p class="text-xs font-semibold text-gray-500 mb-2">QR Code PIX</p>
            <img src="data:image/png;base64,{{ $pixQrCode }}" alt="QR Code PIX" class="w-48 h-48 mx-auto rounded-lg">
        </div>
        @endif

        @if($paymentUrl)
        <a href="{{ $paymentUrl }}" target="_blank"
           class="block w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition-colors">
            {{ $billingType === 'BOLETO' ? 'Abrir boleto' : 'Abrir link de pagamento' }}
        </a>
        @endif

        <button wire:click="$set('showPayment', false)"
                class="block w-full py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold transition-colors">
            Fechar
        </button>
    </div>
</div>
@endif

{{-- ╔══════════════════════════════════════════════════════════════════
     MODAL — Compra individual de produto (colaborador)
     ══════════════════════════════════════════════════════════════════ --}}
@if($showProductModal)
@php $buyProduct = $this->availableProducts->find($buyingProductId); @endphp
<div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     x-data wire:click.self="$wire.set('showProductModal', false)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Adquirir produto</h2>
            <button wire:click="$set('showProductModal', false)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @if($buyProduct)
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="font-bold text-gray-900">{{ $buyProduct->name }}</p>
            @if($buyProduct->price_once)
            <p class="text-xl font-black text-indigo-600 mt-1">
                R$ {{ number_format($buyProduct->price_once, 2, ',', '.') }}
                <span class="text-sm font-normal text-gray-400">· compra única</span>
            </p>
            @endif
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
            <p class="text-sm text-amber-800 font-semibold mb-1">Disponível em breve</p>
            <p class="text-xs text-amber-700">A compra individual estará disponível em breve. Entre em contato com o suporte para adquirir este produto agora.</p>
        </div>
        @endif
        <button wire:click="$set('showProductModal', false)"
                class="w-full py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold transition-colors">
            Fechar
        </button>
    </div>
</div>
@endif

</div>
