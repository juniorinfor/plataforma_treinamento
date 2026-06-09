<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Uso nas rotas:
     *   ->middleware('role:gestor')               // Nível 2 — qualquer Gestor
     *   ->middleware('role:company_admin')        // Gestor com billing
     *   ->middleware('role:platform_admin')       // Admin do Sistema
     *   ->middleware('role:company_admin,manager') // explícito (legado)
     *
     * Platform admin sempre passa — tem acesso total.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin do Sistema tem acesso a tudo
        if ($user->isPlatformAdmin()) {
            return $next($request);
        }

        // Expande o atalho 'gestor' para company_admin + manager
        $expanded = [];
        foreach ($roles as $role) {
            if ($role === 'gestor') {
                $expanded[] = UserRole::CompanyAdmin;
                $expanded[] = UserRole::Manager;
            } else {
                $r = UserRole::tryFrom($role);
                if ($r) {
                    $expanded[] = $r;
                }
            }
        }

        if (!in_array($user->role, $expanded, true)) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
}
