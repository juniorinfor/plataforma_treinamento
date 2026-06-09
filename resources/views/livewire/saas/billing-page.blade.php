<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">

    {{-- ── Header ── --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Faturamento</h1>
        <p class="text-sm text-gray-500 mt-1">Gerencie sua assinatura e histórico de pagamentos</p>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if($this->subscription)
    @php
        $sub    = $this->subscription;
        $plan   = $sub->plan;
        $status = $sub->status;
        $statusColor = match($status?->value) {
            'active'   => 'text-emerald-700 bg-emerald-50 border-emerald-200',
            'trial'    => 'text-blue-700 bg-blue-50 border-blue-200',
            'past_due' => 'text-amber-700 bg-amber-50 border-amber-200',
            'cancelled','expired' => 'text-red-700 bg-red-50 border-red-200',
            default    => 'text-gray-600 bg-gray-50 border-gray-200',
        };
    @endphp

    {{-- ── Card de Assinatura ── --}}
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

        <div class="grid grid-cols-2 gap-4">
            @if($plan)
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 font-semibold mb-1">Valor</p>
                <p class="text-xl font-black text-gray-900">
                    R$ {{ number_format($sub->cycle === 'YEARLY' ? $plan->price_yearly : $plan->price_monthly, 2, ',', '.') }}
                    <span class="text-sm font-normal text-gray-400">/{{ $sub->cycle === 'YEARLY' ? 'ano' : 'mês' }}</span>
                </p>
            </div>
            @endif

            @if($sub->next_due_date)
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 font-semibold mb-1">Próximo vencimento</p>
                <p class="text-xl font-bold text-gray-900">
                    {{ $sub->next_due_date->format('d/m/Y') }}
                </p>
            </div>
            @endif
        </div>

        {{-- Ações --}}
        @if($status?->isActive())
        @if(!$confirmCancel)
        <button wire:click="$set('confirmCancel', true)"
                class="text-sm font-semibold text-red-500 hover:text-red-700 hover:underline transition-colors">
            Cancelar assinatura
        </button>
        @else
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 space-y-3">
            <p class="text-sm font-semibold text-red-800">Tem certeza que deseja cancelar?</p>
            <p class="text-xs text-red-600">Você perderá o acesso ao final do período pago. Esta ação não pode ser desfeita.</p>
            <div class="flex gap-3">
                <button wire:click="cancelSubscription"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-colors">
                    Sim, cancelar
                </button>
                <button wire:click="$set('confirmCancel', false)"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                    Manter assinatura
                </button>
            </div>
        </div>
        @endif
        @endif
    </div>

    {{-- ── Histórico de Cobranças ── --}}
    <div class="tu-card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50">
            <h2 class="font-bold text-gray-900">Histórico de Cobranças</h2>
        </div>

        @if($this->invoices->isEmpty())
        <div class="p-8 text-center text-sm text-gray-400">
            Nenhuma cobrança encontrada.
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($this->invoices as $invoice)
            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                        @if($invoice->billing_type === 'PIX')
                        <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.03 2.59a1.5 1.5 0 011.94 0l7.5 6.363a1.5 1.5 0 01.53 1.144V19.5a1.5 1.5 0 01-1.5 1.5H15a1.5 1.5 0 01-1.5-1.5V15H10.5v4.5A1.5 1.5 0 019 21H4.5A1.5 1.5 0 013 19.5v-9.403a1.5 1.5 0 01.53-1.144l7.5-6.363z"/>
                        </svg>
                        @else
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            R$ {{ number_format($invoice->amount, 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $invoice->billing_type }} ·
                            Venc. {{ $invoice->due_at?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $invoice->statusColor() }}">
                        {{ $invoice->statusLabel() }}
                    </span>
                    @if($invoice->isPending() && $invoice->payment_url)
                    <a href="{{ $invoice->payment_url }}" target="_blank"
                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                        Pagar →
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    @else
    {{-- Sem assinatura --}}
    <div class="tu-card p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        <p class="text-gray-500 text-sm mb-4">Você não possui uma assinatura ativa.</p>
        <a href="{{ route('plans') }}" wire:navigate
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white
                  text-sm font-semibold hover:bg-indigo-700 transition-colors">
            Ver planos
        </a>
    </div>
    @endif
</div>
