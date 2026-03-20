<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ✅ Log connexion
        ActivityLogger::log(
            action: 'Connexion',
            module: 'auth',
            detail: auth()->user()->name . ' (' . auth()->user()->email . ') s\'est connecté'
        );

        // ✅ Redirection selon le rôle
        $role = auth()->user()->role;

        return match ($role) {
            'admin', 'proprietaire' => redirect()->route('dashboard'),
            'gestionnaire' => redirect()->route('collectes.index'),
            'collecteur' => redirect()->route('collectes.index'),
            default => redirect()->route('collectes.index'),
        };
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ✅ Log AVANT logout() — après, auth()->user() retourne NULL
        ActivityLogger::log(
            action: 'Déconnexion',
            module: 'auth',
            detail: auth()->user()->name . ' (' . auth()->user()->email . ') s\'est déconnecté'
        );

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}