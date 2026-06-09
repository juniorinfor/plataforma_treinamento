<?php

namespace App\Livewire\Saas;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Services\AsaasService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Faturamento')]
class BillingPage extends Component
{
    public bool $confirmCancel = false;

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
    public function invoices()
    {
        $sub = $this->subscription;
        return $sub
            ? Invoice::where('subscription_id', $sub->id)
                ->latest()
                ->limit(10)
                ->get()
            : collect();
    }

    // ── Actions ───────────────────────────────────────────────────────

    public function cancelSubscription(): void
    {
        $sub = $this->subscription;

        if (!$sub) {
            return;
        }

        app(AsaasService::class)->cancelSubscription($sub);

        unset($this->subscription);
        $this->confirmCancel = false;
        session()->flash('success', 'Assinatura cancelada. O acesso permanece até o fim do período pago.');
    }

    public function render()
    {
        return view('livewire.saas.billing-page');
    }
}
