<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Platform admins can access without company context
        if ($user->isPlatformAdmin()) {
            return $next($request);
        }

        // Ensure user belongs to a company
        if (!$user->company_id) {
            abort(403, 'Você não está vinculado a nenhuma empresa.');
        }

        // Ensure company is active
        if (!$user->company || !$user->company->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Sua empresa está desativada. Entre em contato com o suporte.');
        }

        // Share company data with all views
        view()->share('currentCompany', $user->company);

        return $next($request);
    }
}
