<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Vérifie le rôle de l'utilisateur connecté.
     *
     * Usage dans les routes :
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,manager')
     *
     * @param string ...$roles  Rôles autorisés séparés par virgule
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Non connecté → login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Compte désactivé → déconnexion
        if (!$user->is_active) {
            auth()->guard('web')->logout(); // ✅
            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé. Contactez un administrateur.');
        }

        // Rôle non autorisé → 403
        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }

        return $next($request);
    }
}
