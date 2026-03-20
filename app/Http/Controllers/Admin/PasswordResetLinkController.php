<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Services\ActivityLogger;

class PasswordResetLinkController extends Controller
{
    /** Générer un lien de réinitialisation */
    public function generate(User $user)
    {
        PasswordResetLink::where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        $token = Str::random(64);

        PasswordResetLink::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        $lien = route('password.reset.form', $token);

        ActivityLogger::log(
            action: 'Réinitialisation mot de passe',
            module: 'users',
            detail: "Lien de réinitialisation généré pour : {$user->name} ({$user->email})"
        );

        return redirect()->route('admin.users.index')
            ->with('reset_link', $lien)
            ->with('success', 'Lien de réinitialisation généré !');
    }

    /** Page de réinitialisation (publique) */
    public function form(string $token)
    {
        $reset = PasswordResetLink::where('token', $token)
            ->whereNull('used_at')
            ->firstOrFail();

        if ($reset->isExpired()) {
            return redirect()->route('login')
                ->with('error', 'Ce lien a expiré. Contactez votre administrateur.');
        }

        return view('auth.reset-password-link', compact('reset', 'token'));
    }

    /** Traitement du formulaire */
    public function store(Request $request, string $token)
    {
        $reset = PasswordResetLink::where('token', $token)
            ->whereNull('used_at')
            ->firstOrFail();

        if ($reset->isExpired()) {
            return redirect()->route('login')
                ->with('error', 'Ce lien a expiré. Contactez votre administrateur.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Minimum 8 caractères.',
        ]);

        $reset->user->update([
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $reset->update(['used_at' => now()]);

        ActivityLogger::log(
            action: 'Mot de passe réinitialisé',
            module: 'users',
            detail: "Mot de passe réinitialisé : {$reset->user->name} ({$reset->user->email})"
        );

        return redirect()->route('login')
            ->with('success', 'Mot de passe réinitialisé ! Vous pouvez maintenant vous connecter.');
    }
}