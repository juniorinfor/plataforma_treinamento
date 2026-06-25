<?php

namespace App\Livewire\Platform;

use App\Models\AiProvider;
use App\Models\Setting;
use App\Services\AsaasService;
use App\Services\DiagnosticReportService;
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

    // ── Inteligência Artificial ───────────────────────────────────────
    public string $aiDriver = 'claude';        // claude | openai
    public string $aiModel = '';
    public string $aiKey = '';                  // nova chave (em branco = mantém)
    public int $aiMaxTokens = 2048;
    public string $aiTemperature = '0.70';
    public bool $aiActive = false;
    public bool $aiHasKey = false;
    public ?string $aiTestResult = null;        // null | 'ok' | 'fail'

    public function mount(): void
    {
        $this->asaasEnv          = Setting::get('asaas_env') ?: config('services.asaas.env', 'sandbox');
        $this->asaasWebhookToken = Setting::get('asaas_webhook_token') ?: '';
        $this->hasKey            = Setting::has('asaas_key') || (bool) config('services.asaas.key');

        $provider = AiProvider::first();
        if ($provider) {
            $this->aiDriver      = $provider->driver ?? 'claude';
            $this->aiModel       = $provider->model ?? '';
            $this->aiMaxTokens   = (int) ($provider->max_tokens ?? 2048);
            $this->aiTemperature = (string) ($provider->temperature ?? '0.70');
            $this->aiActive      = (bool) $provider->is_active;
            $this->aiHasKey      = filled($provider->api_key);
        }
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

    // ── IA ────────────────────────────────────────────────────────────

    public function saveAi(): void
    {
        $this->validate([
            'aiDriver'      => ['required', 'in:claude,openai'],
            'aiModel'       => ['nullable', 'string', 'max:120'],
            'aiKey'         => ['nullable', 'string', 'max:500'],
            'aiMaxTokens'   => ['required', 'integer', 'min:256', 'max:32000'],
            'aiTemperature' => ['required', 'numeric', 'min:0', 'max:2'],
            'aiActive'      => ['boolean'],
        ]);

        $provider = AiProvider::first() ?? new AiProvider(['name' => 'IA dos Diagnósticos']);

        $provider->driver      = $this->aiDriver;
        $provider->model       = $this->aiModel ?: null;
        $provider->max_tokens  = $this->aiMaxTokens;
        $provider->temperature = $this->aiTemperature;
        $provider->is_active   = $this->aiActive;

        // Só substitui a chave se o admin digitou uma nova (cast 'encrypted')
        if (trim($this->aiKey) !== '') {
            $provider->api_key = trim($this->aiKey);
            $this->aiHasKey = true;
            $this->aiKey = '';
        }

        $provider->save();

        $this->aiTestResult = null;
        session()->flash('status', 'Configuração de IA salva.');
    }

    public function testAi(): void
    {
        $provider = AiProvider::first();
        $this->aiTestResult = ($provider && app(DiagnosticReportService::class)->testProvider($provider))
            ? 'ok'
            : 'fail';
    }

    public function render()
    {
        return view('livewire.platform.integration-settings');
    }
}
