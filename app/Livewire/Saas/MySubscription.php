<?php

namespace App\Livewire\Saas;

use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Subscription;
use App\Services\AsaasService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

#[Layout('components.layouts.app')]
#[Title('Minha Assinatura')]
class MySubscription extends Component
{
    public string $cycle       = 'MONTHLY';
    public string $billingType = 'PIX';

    public bool    $showCheckout   = false;
    public ?int    $checkoutPlanId = null;
    public ?string $checkoutError  = null;

    public bool    $showPayment  = false;
    public ?string $paymentUrl   = null;
    public ?string $pixQrCode    = null;

    public bool $confirmCancel = false;

    public bool $showProductModal = false;
    public ?int $buyingProductId  = null;

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function subscription(): ?Subscription
    {
        $company = auth()->user()?->company;
        return $company
            ? Subscription::where('company_id', $company->id)
                ->with('plan')
                ->latest()
                ->first()
            : null;
    }

    #[Computed]
    public function pendingInvoice(): ?Invoice
    {
        $sub = $this->subscription;
        return $sub
            ? Invoice::where('subscription_id', $sub->id)
                ->where('status', 'pending')
                ->latest()
                ->first()
            : null;
    }

    #[Computed]
    public function invoices()
    {
        $sub = $this->subscription;
        return $sub
            ? Invoice::where('subscription_id', $sub->id)->latest()->limit(12)->get()
            : collect();
    }

    #[Computed]
    public function plans()
    {
        return Plan::where('is_active', true)->orderBy('sort_order')->get();
    }

    #[Computed]
    public function availableProducts()
    {
        return Product::where('is_active', true)->orderBy('sort_order')->get();
    }

    // ── Plan checkout (gestor / company_admin) ────────────────────────

    public function openCheckout(int $planId): void
    {
        abort_unless(auth()->user()->canManageBilling(), 403);
        $this->checkoutPlanId = $planId;
        $this->checkoutError  = null;
        $this->showCheckout   = true;
    }

    public function confirmSubscription(): void
    {
        abort_unless(auth()->user()->canManageBilling(), 403);

        $company = auth()->user()->company;
        $plan    = Plan::find($this->checkoutPlanId);

        if (!$company || !$plan) {
            $this->checkoutError = 'Empresa ou plano não encontrado.';
            return;
        }

        try {
            $subscription = app(AsaasService::class)->createSubscription(
                $company, $plan, $this->cycle, $this->billingType
            );

            unset($this->subscription, $this->pendingInvoice, $this->invoices);
            $this->showCheckout = false;

            $invoice = Invoice::where('subscription_id', $subscription->id)
                ->where('status', 'pending')->latest()->first();

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

    public function cancelSubscription(): void
    {
        abort_unless(auth()->user()->canManageBilling(), 403);

        $sub = $this->subscription;
        if (!$sub) return;

        app(AsaasService::class)->cancelSubscription($sub);
        unset($this->subscription, $this->invoices);
        $this->confirmCancel = false;
        session()->flash('success', 'Assinatura cancelada. O acesso permanece até o fim do período pago.');
    }

    // ── Individual product purchase (colaborador) ─────────────────────

    public function openProductCheckout(int $productId): void
    {
        $this->buyingProductId  = $productId;
        $this->showProductModal = true;
    }

    public function render()
    {
        return view('livewire.saas.my-subscription');
    }
}
