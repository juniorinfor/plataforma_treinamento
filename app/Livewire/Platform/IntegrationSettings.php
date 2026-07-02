<?php

namespace App\Livewire\Platform;

use App\Models\AiProvider;
use App\Models\Setting;
use App\Services\AsaasService;
use App\Services\DiagnosticReportService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Integrações da plataforma (somente Admin do Sistema).
 * Hoje: gateway de pagamento Asaas (chave, ambiente, webhook) e
 * provedores de IA para geração dos relatórios de diagnóstico
 * (Claude e OpenAI podem ficar configurados simultaneamente;
 * apenas um fica "ativo" por vez para gerar os relatórios).
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

    // ── Inteligência Artificial (múltiplos provedores) ────────────────
    public bool $showAiModal = false;
    public ?int $editingAiId = null;

    public string $aiName = '';
    public string $aiDriver = 'claude';        // claude | openai
    public string $aiModel = '';
    public string $aiKey = '';                  // nova chave (em branco = mantém, se editando)
    public int $aiMaxTokens = 4096;
    public string $aiTemperature = '0.70';
    public bool $aiActive = false;
    public bool $aiHasKey = false;

    /** @var array<int,string|null> id => 'ok'|'fail'|null, resultado do último teste por provedor */
    public array $aiTestResults = [];

    public function mount(): void
    {
        $this->asaasEnv          = Setting::get('asaas_env') ?: config('services.asaas.env', 'sandbox');
        $this->asaasWebhookToken = Setting::get('asaas_webhook_token') ?: '';
        $this->hasKey            = Setting::has('asaas_key') || (bool) config('services.asaas.key');
    }

    #[Computed]
    public function aiProviders()
    {
        return AiProvider::orderByDesc('is_active')->orderBy('name')->get();
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

    // ── IA — múltiplos provedores (Claude, OpenAI, ...) ───────────────

    public function openCreateAi(string $driver = 'claude'): void
    {
        $this->reset(['editingAiId', 'aiName', 'aiModel', 'aiKey', 'aiHasKey']);
        $this->aiDriver      = $driver;
        $this->aiMaxTokens   = 4096;
        $this->aiTemperature = '0.70';
        $this->aiActive      = !$this->aiProviders->count(); // primeiro provedor já nasce ativo
        $this->aiName        = $driver === 'openai' ? 'OpenAI (GPT)' : 'Claude (Anthropic)';
        $this->resetValidation();
        $this->showAiModal = true;
    }

    public function openEditAi(int $id): void
    {
        $provider = AiProvider::findOrFail($id);

        $this->editingAiId   = $provider->id;
        $this->aiName        = $provider->name;
        $this->aiDriver      = $provider->driver ?? 'claude';
        $this->aiModel       = $provider->model ?? '';
        $this->aiKey         = '';
        $this->aiMaxTokens   = (int) ($provider->max_tokens ?? 4096);
        $this->aiTemperature = (string) ($provider->temperature ?? '0.70');
        $this->aiActive      = (bool) $provider->is_active;
        $this->aiHasKey      = filled($provider->api_key);
        $this->resetValidation();
        $this->showAiModal = true;
    }

    public function saveAi(): void
    {
        $this->validate([
            'aiName'        => ['required', 'string', 'max:120'],
            'aiDriver'      => ['required', 'in:claude,openai'],
            'aiModel'       => ['nullable', 'string', 'max:120'],
            'aiKey'         => [$this->editingAiId ? 'nullable' : 'required', 'string', 'max:500'],
            'aiMaxTokens'   => ['required', 'integer', 'min:256', 'max:32000'],
            'aiTemperature' => ['required', 'numeric', 'min:0', 'max:2'],
            'aiActive'      => ['boolean'],
        ]);

        $provider = $this->editingAiId
            ? AiProvider::findOrFail($this->editingAiId)
            : new AiProvider();

        $provider->name        = $this->aiName;
        $provider->driver      = $this->aiDriver;
        $provider->model       = $this->aiModel ?: null;
        $provider->max_tokens  = $this->aiMaxTokens;
        $provider->temperature = $this->aiTemperature;

        // Só substitui a chave se o admin digitou uma nova (cast 'encrypted')
        if (trim($this->aiKey) !== '') {
            $provider->api_key = trim($this->aiKey);
        }

        $provider->save();

        // Apenas um provedor fica ativo por vez — evita ambiguidade na geração dos relatórios.
        if ($this->aiActive) {
            AiProvider::where('id', '!=', $provider->id)->update(['is_active' => false]);
            $provider->update(['is_active' => true]);
        } else {
            $provider->update(['is_active' => false]);
        }

        unset($this->aiProviders);
        $this->showAiModal = false;
        session()->flash('status', 'Provedor de IA salvo.');
    }

    public function setActiveAi(int $id): void
    {
        AiProvider::where('id', '!=', $id)->update(['is_active' => false]);
        AiProvider::whereKey($id)->update(['is_active' => true]);
        unset($this->aiProviders);
    }

    public function deleteAi(int $id): void
    {
        AiProvider::whereKey($id)->delete();
        unset($this->aiProviders, $this->aiTestResults[$id]);
    }

    public function testAi(int $id): void
    {
        $provider = AiProvider::find($id);
        $this->aiTestResults[$id] = ($provider && app(DiagnosticReportService::class)->testProvider($provider))
            ? 'ok'
            : 'fail';
    }

    public function render()
    {
        return view('livewire.platform.integration-settings');
    }
}
