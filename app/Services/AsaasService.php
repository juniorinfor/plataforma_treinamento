<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AsaasService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $env           = config('services.asaas.env', 'sandbox');
        $this->baseUrl = $env === 'production'
            ? 'https://api.asaas.com/api/v3'
            : 'https://sandbox.asaas.com/api/v3';

        $this->apiKey = config('services.asaas.key', '');
    }

    // ── Customers ─────────────────────────────────────────────────────

    /**
     * Retorna o asaas_customer_id existente ou cria um novo.
     */
    public function getOrCreateCustomer(Company $company): string
    {
        if ($company->asaas_customer_id) {
            return $company->asaas_customer_id;
        }

        $response = $this->post('/customers', [
            'name'             => $company->name,
            'email'            => $company->email ?? '',
            'cpfCnpj'          => preg_replace('/\D/', '', $company->document ?? ''),
            'groupName'        => 'Executive Map',
            'externalReference' => (string) $company->id,
        ]);

        $customerId = $response['id'];

        $company->update(['asaas_customer_id' => $customerId]);

        return $customerId;
    }

    // ── Subscriptions ─────────────────────────────────────────────────

    /**
     * Cria assinatura no Asaas e persiste localmente.
     * Retorna a subscription local já atualizada.
     */
    public function createSubscription(
        Company $company,
        Plan    $plan,
        string  $cycle       = 'MONTHLY',
        string  $billingType = 'PIX'
    ): Subscription {
        $customerId = $this->getOrCreateCustomer($company);

        $price = $cycle === 'YEARLY'
            ? (float) $plan->price_yearly
            : (float) $plan->price_monthly;

        $nextDue = now()->addDay()->format('Y-m-d');

        $response = $this->post('/subscriptions', [
            'customer'          => $customerId,
            'billingType'       => $billingType,
            'value'             => $price,
            'nextDueDate'       => $nextDue,
            'cycle'             => $cycle,
            'description'       => "Executive Map - {$plan->name}",
            'externalReference' => (string) $company->id,
        ]);

        // Persiste / atualiza subscription local
        $subscription = Subscription::updateOrCreate(
            ['company_id' => $company->id, 'payment_gateway' => 'asaas'],
            [
                'plan_id'                  => $plan->id,
                'status'                   => SubscriptionStatus::Active,
                'payment_gateway'          => 'asaas',
                'gateway_subscription_id'  => $response['id'],
                'cycle'                    => $cycle,
                'next_due_date'            => $nextDue,
                'starts_at'                => now(),
            ]
        );

        // Atualiza company
        $company->update([
            'plan_id'             => $plan->id,
            'subscription_status' => SubscriptionStatus::Active,
        ]);

        // Busca o primeiro pagamento gerado e cria invoice local
        $this->syncFirstPayment($subscription, $response['id'], $billingType);

        return $subscription->fresh();
    }

    /**
     * Cancela assinatura no Asaas e localmente.
     */
    public function cancelSubscription(Subscription $subscription): void
    {
        if ($subscription->gateway_subscription_id) {
            try {
                $this->delete("/subscriptions/{$subscription->gateway_subscription_id}");
            } catch (Throwable $e) {
                Log::warning("Asaas cancel failed: {$e->getMessage()}");
            }
        }

        $subscription->update([
            'status'       => SubscriptionStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        $subscription->company->update([
            'subscription_status' => SubscriptionStatus::Cancelled,
        ]);
    }

    // ── Payments / Invoices ───────────────────────────────────────────

    /**
     * Busca os pagamentos de uma assinatura e retorna os mais recentes.
     */
    public function getSubscriptionPayments(string $asaasSubId, int $limit = 5): array
    {
        try {
            $response = $this->get("/subscriptions/{$asaasSubId}/payments", [
                'limit' => $limit,
                'sort'  => 'dueDate',
                'order' => 'desc',
            ]);
            return $response['data'] ?? [];
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * Retorna o link de pagamento de um invoice pelo gateway_invoice_id.
     */
    public function getPaymentUrl(string $asaasPaymentId): ?string
    {
        try {
            $response = $this->get("/payments/{$asaasPaymentId}");
            return $response['invoiceUrl'] ?? $response['bankSlipUrl'] ?? null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Retorna o QR code PIX de um pagamento.
     */
    public function getPixQrCode(string $asaasPaymentId): ?string
    {
        try {
            $response = $this->get("/payments/{$asaasPaymentId}/pixQrCode");
            return $response['encodedImage'] ?? null; // base64 PNG
        } catch (Throwable) {
            return null;
        }
    }

    // ── Webhook processing ────────────────────────────────────────────

    /**
     * Processa um evento de webhook do Asaas.
     */
    public function processWebhook(array $payload): void
    {
        $event   = $payload['event']   ?? null;
        $payment = $payload['payment'] ?? [];

        if (!$event || !$payment) {
            return;
        }

        $asaasSubId = $payment['subscription'] ?? null;
        if (!$asaasSubId) {
            return;
        }

        $subscription = Subscription::where('gateway_subscription_id', $asaasSubId)->first();
        if (!$subscription) {
            return;
        }

        match ($event) {
            'PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED' => $this->handlePaymentReceived($subscription, $payment),
            'PAYMENT_OVERDUE'                        => $this->handlePaymentOverdue($subscription, $payment),
            'PAYMENT_DELETED', 'SUBSCRIPTION_DELETED' => $this->handleCancelled($subscription),
            'PAYMENT_CREATED'                        => $this->upsertInvoice($subscription, $payment, 'pending'),
            default                                  => null,
        };
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function handlePaymentReceived(Subscription $subscription, array $payment): void
    {
        $subscription->update([
            'status'        => SubscriptionStatus::Active,
            'next_due_date' => $payment['dueDate'] ?? $subscription->next_due_date,
        ]);

        $subscription->company->update([
            'subscription_status' => SubscriptionStatus::Active,
        ]);

        $this->upsertInvoice($subscription, $payment, 'paid', now());
    }

    private function handlePaymentOverdue(Subscription $subscription, array $payment): void
    {
        $subscription->update(['status' => SubscriptionStatus::PastDue]);
        $subscription->company->update(['subscription_status' => SubscriptionStatus::PastDue]);
        $this->upsertInvoice($subscription, $payment, 'overdue');
    }

    private function handleCancelled(Subscription $subscription): void
    {
        $subscription->update([
            'status'       => SubscriptionStatus::Cancelled,
            'cancelled_at' => now(),
        ]);
        $subscription->company->update(['subscription_status' => SubscriptionStatus::Cancelled]);
    }

    private function upsertInvoice(
        Subscription $subscription,
        array        $payment,
        string       $status,
        ?string      $paidAt = null
    ): void {
        Invoice::updateOrCreate(
            ['gateway_invoice_id' => $payment['id']],
            [
                'company_id'      => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'amount'          => $payment['value'] ?? 0,
                'currency'        => 'BRL',
                'billing_type'    => $payment['billingType'] ?? null,
                'status'          => $status,
                'due_at'          => $payment['dueDate']  ?? null,
                'paid_at'         => $paidAt,
                'payment_url'     => $payment['invoiceUrl'] ?? $payment['bankSlipUrl'] ?? null,
            ]
        );
    }

    private function syncFirstPayment(Subscription $subscription, string $asaasSubId, string $billingType): void
    {
        try {
            $payments = $this->getSubscriptionPayments($asaasSubId, 1);
            if (!empty($payments)) {
                $this->upsertInvoice($subscription, $payments[0], 'pending');

                // Salva PIX QR se aplicável
                if ($billingType === 'PIX' && isset($payments[0]['id'])) {
                    $qr = $this->getPixQrCode($payments[0]['id']);
                    if ($qr) {
                        Invoice::where('gateway_invoice_id', $payments[0]['id'])
                            ->update(['pix_qr_code' => $qr]);
                    }
                }
            }
        } catch (Throwable $e) {
            Log::warning("AsaasService::syncFirstPayment — {$e->getMessage()}");
        }
    }

    // ── HTTP helpers ──────────────────────────────────────────────────

    private function get(string $path, array $params = []): array
    {
        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->get($this->baseUrl . $path, $params);

        $response->throw();

        return $response->json();
    }

    private function post(string $path, array $data): array
    {
        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post($this->baseUrl . $path, $data);

        $response->throw();

        return $response->json();
    }

    private function delete(string $path): void
    {
        Http::withHeaders(['access_token' => $this->apiKey])
            ->delete($this->baseUrl . $path)
            ->throw();
    }
}
