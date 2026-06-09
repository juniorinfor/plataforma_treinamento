<?php

namespace App\Http\Middleware;

use App\Enums\SubscriptionStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    /**
     * Rotas que podem ser acessadas mesmo com assinatura inativa.
     * Usa nomes de rota (route()->getName()).
     */
    private const ALWAYS_ALLOWED = [
        'plans',
        'billing',
        'logout',
        'login',
        'register',
        'password.request',
        'password.reset',
        'webhooks.asaas',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Sem usuário ou platform_admin → passa sempre
        if (!$user || $user->isPlatformAdmin()) {
            return $next($request);
        }

        // Rota sempre permitida → passa
        $routeName = $request->route()?->getName() ?? '';
        foreach (self::ALWAYS_ALLOWED as $allowed) {
            if ($routeName === $allowed || str_starts_with($routeName, $allowed . '.')) {
                return $next($request);
            }
        }

        $company = $user->company;

        // Sem empresa → passa (EnsureCompanyContext já trata isso)
        if (!$company) {
            return $next($request);
        }

        $status = $company->subscription_status instanceof SubscriptionStatus
            ? $company->subscription_status
            : SubscriptionStatus::tryFrom($company->subscription_status ?? '');

        // Trial válido
        if ($status === SubscriptionStatus::Trial) {
            if (!$company->trial_ends_at || $company->trial_ends_at->isFuture()) {
                return $next($request);
            }

            // Trial expirado
            return redirect()->route('plans')
                ->with('subscription_alert', 'Seu período de teste expirou. Escolha um plano para continuar.');
        }

        // Assinatura ativa
        if ($status === SubscriptionStatus::Active) {
            return $next($request);
        }

        // Past due — acesso com banner de aviso (Grace period)
        if ($status === SubscriptionStatus::PastDue) {
            return $next($request); // banner é mostrado no layout
        }

        // Cancelado ou expirado → bloqueia
        return redirect()->route('plans')
            ->with('subscription_alert', 'Sua assinatura está ' . ($status?->label() ?? 'inativa') . '. Renove para continuar usando a plataforma.');
    }
}
