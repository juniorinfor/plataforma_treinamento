<?php

namespace App\Services;

use App\Enums\DiagnosticReportStatus;
use App\Models\AiProvider;
use App\Models\DiagnosticAssessment;
use App\Models\DiagnosticReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class DiagnosticReportService
{
    /**
     * Cria (ou reutiliza) um DiagnosticReport para o assessment.
     * Se houver um AiProvider ativo, dispara a geração do rascunho.
     */
    public function initReport(DiagnosticAssessment $assessment): DiagnosticReport
    {
        $report = DiagnosticReport::firstOrCreate(
            ['diagnostic_assessment_id' => $assessment->id],
            ['status' => DiagnosticReportStatus::Pending]
        );

        // Se ainda não tem rascunho de IA, tenta gerar agora
        if ($report->status === DiagnosticReportStatus::Pending) {
            $provider = AiProvider::where('is_active', true)->first();
            if ($provider) {
                $this->generateAiDraft($report, $provider);
            }
        }

        return $report->fresh();
    }

    /**
     * (Re)gera o rascunho de IA para um relatório existente.
     * Pode ser chamado pelo admin a qualquer momento.
     */
    public function generateAiDraft(DiagnosticReport $report, ?AiProvider $provider = null): DiagnosticReport
    {
        $provider ??= AiProvider::where('is_active', true)->first();

        if (!$provider) {
            return $report;
        }

        $assessment = $report->assessment()->with([
            'tool',
            'user',
            'results.componentTool',
            'results.dimension',
        ])->first();

        $prompt = $this->buildPrompt($assessment);

        try {
            $rawText = $this->callAi($provider, $prompt);
            $parsed  = $this->parseAiResponse($rawText);

            $report->update([
                'ai_provider_id' => $provider->id,
                'status'         => DiagnosticReportStatus::AiGenerated,
                'ai_draft'       => $rawText,
                'content'        => $parsed['content'] ?? $rawText,
                'archetype'      => $parsed['archetype'] ?? null,
                'highlights'     => $parsed['highlights'] ?? null,
                'swot'           => $parsed['swot'] ?? null,
            ]);
        } catch (Throwable $e) {
            Log::warning("DiagnosticReportService: AI call failed — {$e->getMessage()}");
        }

        return $report->fresh();
    }

    // ── Prompt ────────────────────────────────────────────────────────

    public function buildPrompt(DiagnosticAssessment $assessment): string
    {
        $tool      = $assessment->tool;
        $user      = $assessment->user;
        $results   = $assessment->results;
        $global    = round((float) $assessment->global_score, 1);
        $label     = $assessment->global_label;

        $indexLines = $results->map(function ($r) {
            $name  = $r->componentTool?->name ?? $r->dimension?->name ?? '—';
            $code  = $r->componentTool?->code  ?? $r->dimension?->code  ?? '';
            $score = round((float) $r->normalized_score, 1);
            $lbl   = $r->label;
            return "- {$name}" . ($code ? " ({$code})" : '') . ": {$score}/100 — {$lbl}";
        })->implode("\n");

        return <<<PROMPT
Você é um consultor especialista em desenvolvimento organizacional e liderança.
Analise os resultados do diagnóstico abaixo e produza um relatório qualitativo estruturado.

## Dados do Diagnóstico

Ferramenta: {$tool->name}
{$tool->description ? 'Descrição: ' . $tool->description : ''}
AS SCORE® Global: {$global}/100 — {$label}

### Pontuação por índice:
{$indexLines}

## Tarefa

Responda EXCLUSIVAMENTE em JSON válido com este schema:

```json
{
  "archetype": "Uma frase curta que define o arquétipo organizacional (ex.: 'Organização em Transição')",
  "content": "Análise qualitativa em 3-4 parágrafos em português. Comece com o contexto geral, depois analise os pontos fortes, os pontos de atenção e finalize com uma perspectiva de evolução. Use linguagem executiva, clara e construtiva.",
  "highlights": [
    "Ponto forte ou insight 1 (uma frase)",
    "Ponto forte ou insight 2 (uma frase)",
    "Ponto forte ou insight 3 (uma frase)"
  ],
  "swot": {
    "strengths": ["força 1", "força 2"],
    "weaknesses": ["fraqueza 1", "fraqueza 2"],
    "opportunities": ["oportunidade 1", "oportunidade 2"],
    "threats": ["ameaça 1", "ameaça 2"]
  }
}
```

Não inclua nada fora do JSON. Não use markdown fora do JSON. O campo `content` deve estar em um único string com quebras de linha `\n\n` entre os parágrafos.
PROMPT;
    }

    // ── Chamada de IA ─────────────────────────────────────────────────

    private function callAi(AiProvider $provider, string $prompt): string
    {
        return match ($provider->driver) {
            'openai' => $this->callOpenAi($provider, $prompt),
            default  => $this->callClaude($provider, $prompt),
        };
    }

    private function callClaude(AiProvider $provider, string $prompt): string
    {
        $endpoint = $provider->endpoint ?? 'https://api.anthropic.com/v1/messages';
        $model    = $provider->model    ?? 'claude-3-5-sonnet-20241022';

        $response = Http::timeout(60)
            ->withHeaders([
                'x-api-key'         => $provider->api_key,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])
            ->post($endpoint, [
                'model'      => $model,
                'max_tokens' => $provider->max_tokens ?? 2048,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

        $response->throw();

        return $response->json('content.0.text') ?? '';
    }

    private function callOpenAi(AiProvider $provider, string $prompt): string
    {
        $endpoint = $provider->endpoint ?? 'https://api.openai.com/v1/chat/completions';
        $model    = $provider->model    ?? 'gpt-4o';

        $response = Http::timeout(60)
            ->withToken($provider->api_key)
            ->post($endpoint, [
                'model'       => $model,
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'max_tokens'  => $provider->max_tokens ?? 2048,
                'temperature' => (float) ($provider->temperature ?? 0.7),
            ]);

        $response->throw();

        return $response->json('choices.0.message.content') ?? '';
    }

    // ── Parsing ───────────────────────────────────────────────────────

    private function parseAiResponse(string $raw): array
    {
        // Remove blocos markdown ```json ... ``` se existirem
        $clean = preg_replace('/^```(?:json)?\s*/m', '', $raw);
        $clean = preg_replace('/```\s*$/m', '', $clean);
        $clean = trim($clean);

        $data = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Fallback: usa o texto bruto como content
            return ['content' => $raw];
        }

        return $data;
    }
}
