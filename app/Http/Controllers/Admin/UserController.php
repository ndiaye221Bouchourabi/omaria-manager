<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use App\Services\ActivityLogger;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    /** Crée le compte et envoie l'invitation par email */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:admin,proprietaire,gestionnaire,collecteur'],
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'role.required' => 'Le rôle est obligatoire.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make(Str::random(32)),
            'is_active' => false,
        ]);

        $this->sendInvitation($user);

        // ✅ Log ici — DANS la fonction store()
        ActivityLogger::log(
            action: 'Invitation envoyée',
            module: 'users',
            detail: "Nouvel utilisateur : {$user->name} ({$user->email}) — Rôle : {$user->getRoleLabel()}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Invitation envoyée à ' . $user->email . '.');
    }

    /** Renvoyer une invitation */
    public function resendInvitation(User $user)
    {
        Invitation::where('user_id', $user->id)
            ->whereNull('accepted_at')
            ->delete();

        $this->sendInvitation($user);

        // ✅ Log renvoi invitation
        ActivityLogger::log(
            action: 'Invitation renvoyée',
            module: 'users',
            detail: "Renvoi invitation à : {$user->name} ({$user->email})"
        );

        return back()->with('success', 'Invitation renvoyée à ' . $user->email . '.');
    }

    /** Logique commune : créer token + envoyer mail */
    private function sendInvitation(User $user): void
    {
        $token = Str::random(64);

        Invitation::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        try {
            Mail::to($user->email)->send(new InvitationMail($user, $token));
        } catch (\Exception $e) {
            \Log::error('Mail failed: ' . $e->getMessage());
        }
    }

    /** Page "créer mon mot de passe" (publique) */
    public function acceptForm(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            return redirect()->route('login')
                ->with('error', 'Ce lien a expiré. Contactez votre administrateur.');
        }

        return view('admin.users.accept-invitation', compact('invitation', 'token'));
    }

    /** Traitement du formulaire "créer mon mot de passe" */
    public function acceptStore(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($invitation->isExpired()) {
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

        $invitation->user->update([
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $invitation->update(['accepted_at' => now()]);

        // ✅ Log activation compte
        ActivityLogger::log(
            action: 'Activation compte',
            module: 'users',
            detail: "Compte activé : {$invitation->user->name} ({$invitation->user->email})"
        );

        return redirect()->route('login')
            ->with('success', 'Mot de passe créé ! Vous pouvez maintenant vous connecter.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,proprietaire,gestionnaire,collecteur'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::min(8)];
        }

        $validated = $request->validate($rules);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // ✅ Log ici — DANS la fonction update()
        ActivityLogger::log(
            action: 'Modification utilisateur',
            module: 'users',
            detail: "Utilisateur : {$user->name} ({$user->email}) — Rôle : {$user->getRoleLabel()}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }

    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $action = $user->is_active ? 'activé' : 'désactivé';

        // ✅ Log activation/désactivation
        ActivityLogger::log(
            action: 'Compte ' . $action,
            module: 'users',
            detail: "Compte de {$user->name} ({$user->email}) {$action}"
        );

        return back()->with('success', 'Compte de "' . $user->name . '" ' . $action . '.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $name = $user->name;
        $email = $user->email;
        $role = $user->getRoleLabel();

        $user->delete();

        // ✅ Log suppression utilisateur
        ActivityLogger::log(
            action: 'Suppression utilisateur',
            module: 'users',
            detail: "Utilisateur supprimé : {$name} ({$email}) — Rôle : {$role}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur "' . $name . '" supprimé.');
    }
}