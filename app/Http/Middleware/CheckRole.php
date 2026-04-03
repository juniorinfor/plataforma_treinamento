<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Platform admin has access to everything
        if ($user->isPlatformAdmin()) {
            return $next($request);
        }

        // Check if user's role is in the allowed roles
        $allowedRoles = array_map(fn($role) => UserRole::tryFrom($role), $roles);

        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
}
