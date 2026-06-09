<?php

namespace App\Livewire\Saas;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\AsaasService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

#[Layout('components.layouts.app')]
#[Title('Planos')]
class PlansPage extends Component
{
    public string $cycle       = 'MONTHLY';   // MONTHLY | YEARLY
    public string $billingType = 'PIX';        // PIX | BOLETO

    // Modal de checkout
    public bool  $showCheckout = false;
    public ?int  $checkoutPlanId = null;
    public ?string $checkoutError = null;

    // Após criar assinatura: mostra PIX ou link boleto
    public bool   $showPayment  = false;
    public ?string $paymentUrl  = null;
    public ?string $pixQrCode   = null;
    public ?string $pixCode     = null;

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function plans()
    {
        return Plan::where('is_active', true)->orderBy('sort_order')->get();
    }

    #[Computed]
    public function currentSubscription(): ?Subscription
    {
        $company = auth()->user()?->company;
        return $company
            ? Subscription::where('company_id', $company->id)
                ->whereIn('status', ['trial', 'active', 'past_due'])
                ->with('plan')
                ->latest()
                ->first()
            : null;
    }

    #[Computed]
    public function pendingInvoice(): ?Invoice
    {
        $sub = $this->currentSubscription;
        return $sub
            ? Invoice::where('subscription_id', $sub->id)
                ->where('status', 'pending')
                ->latest()
                ->first()
            : null;
    }

    // ── Actions ───────────────────────────────────────────────────────

    public function openCheckout(int $planId): void
    {
        $this->checkoutPlanId = $planId;
        $this->checkoutError  = null;
        $this->showCheckout   = true;
    }

    public function confirmSubscription(): void
    {
        $company = auth()->user()?->company;
        $plan    = Plan::find($this->checkoutPlanId);

        if (!$company || !$plan) {
            $this->checkoutError = 'Empresa ou plano não encontrado.';
            return;
        }

        try {
            $subscription = app(AsaasService::class)->createSubscription(
                $company,
                $plan,
                $this->cycle,
                $this->billingType
            );

            unset($this->currentSubscription, $this->pendingInvoice);
            $this->showCheckout = false;

            // Recupera dados do primeiro pagamento para exibir ao usuário
            $invoice = Invoice::where('subscription_id', $subscription->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($invoice) {
                $this->paymentUrl = $invoice->payment_url;
                $this->pixQrCode  = $invoice->pix_qr_code;
                $this->showPayment = true;
            }
        } catch (Throwable $e) {
            $this->checkoutError = 'Erro ao processar. Tente novamente ou contate o suporte.';
            \Illuminate\Support\Facades\Log::error("Asaas checkout: {$e->getMessage()}");
        }
    }

    public function render()
    {
        return view('livewire.saas.plans-page');
    }
}
