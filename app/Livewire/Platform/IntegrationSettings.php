<?php

namespace App\Livewire\Platform;

use App\Models\Setting;
use App\Services\AsaasService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Integrações da plataforma (somente Admin do Sistema).
 * Hoje: gateway de pagamento Asaas (chave, ambiente, webhook).
 */
#[Layout('components.layouts.app')]
#[Title('Integrações')]
class IntegrationSettings extends Component
{
    public string $asaasEnv = 'sandbox';
    public string $asaasKey = '';          // nova chave (em branco = mantém a atual)
    public string $asaasWebhookToken = '';

    public bool $hasKey = false;
    public ?string $testResult = null;     // null | 'ok' | 'fail'

    public function mount(): void
    {
        $this->asaasEnv          = Setting::get('asaas_env') ?: config('services.asaas.env', 'sandbox');
        $this->asaasWebhookToken = Setting::get('asaas_webhook_token') ?: '';
        $this->hasKey            = Setting::has('asaas_key') || (bool) config('services.asaas.key');
    }

    public function webhookUrl(): string
    {
        return route('webhooks.asaas');
    }

    public function save(): void
    {
        $this->validate([
            'asaasEnv'          => ['required', 'in:sandbox,production'],
            'asaasKey'          => ['nullable', 'string', 'max:500'],
            'asaasWebhookToken' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::put('asaas_env', $this->asaasEnv);
        Setting::put('asaas_webhook_token', $this->asaasWebhookToken ?: null);

        // Só substitui a chave se o admin digitou uma nova
        if (trim($this->asaasKey) !== '') {
            Setting::put('asaas_key', trim($this->asaasKey), encrypt: true);
            $this->hasKey   = true;
            $this->asaasKey = '';
        }

        $this->testResult = null;
        session()->flash('status', 'Configurações de integração salvas.');
    }

    public function testConnection(): void
    {
        // Usa a chave/ambiente já salvos
        $this->testResult = app(AsaasService::class)->ping() ? 'ok' : 'fail';
    }

    public function render()
    {
        return view('livewire.platform.integration-settings');
    }
}
