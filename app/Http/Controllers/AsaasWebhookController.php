<?php

namespace App\Http\Controllers;

use App\Services\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class AsaasWebhookController extends Controller
{
    public function handle(Request $request, AsaasService $asaas): Response
    {
        // Valida token de segurança (configurado na tela de Integrações / painel Asaas)
        $token = \App\Models\Setting::get('asaas_webhook_token') ?: config('services.asaas.webhook_token');
        if ($token && $request->header('asaas-access-token') !== $token) {
            Log::warning('Asaas webhook: token inválido');
            return response('Unauthorized', 401);
        }

        $payload = $request->all();

        Log::info('Asaas webhook recebido', ['event' => $payload['event'] ?? 'unknown']);

        try {
            $asaas->processWebhook($payload);
        } catch (Throwable $e) {
            Log::error("Asaas webhook error: {$e->getMessage()}");
            // Retorna 200 mesmo em erro para evitar reenvios em cascata do Asaas
        }

        return response('OK', 200);
    }
}
