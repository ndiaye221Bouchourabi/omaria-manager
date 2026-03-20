@extends('layouts.app')
@section('page-title', 'Nouvel utilisateur')

@section('content')
<style>
.form-card {
    background: rgba(255,255,255,0.88);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255,255,255,0.55);
    border-radius: 22px;
    padding: 2rem;
    box-shadow: 0 2px 16px rgba(11,20,35,0.07);
    max-width: 620px;
    position: relative; overflow: hidden;
}
.form-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #0f2252, #3b82f6, #10b981);
}
.form-title {
    font-family: 'Sora', sans-serif;
    font-size: 1rem; font-weight: 800; color: #0f172a;
    margin-bottom: 1.5rem;
    display: flex; align-items: center; gap: 8px;
}
.form-title i { color: #3b82f6; }
.form-group { margin-bottom: 1.2rem; }
.form-label-premium {
    display: block;
    font-family: 'Sora', sans-serif;
    font-size: 10.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.5px;
    color: #64748b; margin-bottom: 6px;
}
.form-input-premium {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid #e2e8f0; border-radius: 12px;
    font-size: 13.5px; color: #0f172a;
    background: white; outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: 'DM Sans', sans-serif;
}
.form-input-premium:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}
.form-input-premium.is-invalid { border-color: #ef4444; }
.invalid-feedback { font-size: 12px; color: #ef4444; margin-top: 4px; }

/* 4 cartes rôles en 2x2 */
.role-cards {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;
}
.role-card-select {
    border: 1.5px solid #e2e8f0; border-radius: 12px;
    padding: 14px 12px; cursor: pointer;
    transition: all 0.2s; text-align: center;
    background: white; display: block;
}
.role-card-select:hover { border-color: #94a3b8; background: #f8fafc; }
.role-card-select input[type="radio"] { display: none; }

.selected-admin        { border-color: #f59e0b !important; background: rgba(245,158,11,0.06) !important; }
.selected-proprietaire { border-color: #8b5cf6 !important; background: rgba(139,92,246,0.06) !important; }
.selected-gestionnaire { border-color: #3b82f6 !important; background: rgba(59,130,246,0.06) !important; }
.selected-collecteur   { border-color: #10b981 !important; background: rgba(16,185,129,0.06) !important; }

.role-card-icon {
    font-size: 1.5rem; margin-bottom: 8px; display: block;
}
.role-card-icon.admin        { color: #f59e0b; }
.role-card-icon.proprietaire { color: #8b5cf6; }
.role-card-icon.gestionnaire { color: #3b82f6; }
.role-card-icon.collecteur   { color: #10b981; }

.role-card-name {
    font-family: 'Sora', sans-serif;
    font-size: 12px; font-weight: 700; color: #0f172a;
    display: block; margin-bottom: 3px;
}
.role-card-desc { font-size: 10.5px; color: #94a3b8; line-height: 1.4; }

/* Bloc info invitation */
.invite-info {
    display: flex; align-items: flex-start; gap: 10px;
    background: rgba(59,130,246,0.06);
    border: 1px solid rgba(59,130,246,0.18);
    border-radius: 12px; padding: 14px 16px;
    margin-bottom: 1.5rem;
}
.invite-info i { color: #3b82f6; font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.invite-info-text { font-size: 12.5px; color: #1e40af; line-height: 1.6; }
.invite-info-text strong { font-weight: 700; }

.form-actions { display: flex; gap: 10px; margin-top: 1.5rem; }
.btn-submit {
    flex: 1; padding: 11px;
    background: linear-gradient(135deg, #0f2252, #1d4088);
    color: white; border: none; border-radius: 12px;
    font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 700;
    cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-submit:hover {
    background: linear-gradient(135deg, #163068, #2554b0);
    transform: translateY(-1px);
}
.btn-cancel {
    padding: 11px 20px;
    background: white; color: #64748b;
    border: 1.5px solid #e2e8f0; border-radius: 12px;
    font-size: 13px; text-decoration: none;
    display: flex; align-items: center; gap: 6px;
    transition: all 0.2s;
}
.btn-cancel:hover { border-color: #94a3b8; color: #0f172a; }
</style>

<div class="form-card">
    <div class="form-title">
        <i class="bi bi-person-plus-fill"></i> Créer un utilisateur
    </div>

    {{-- Info invitation --}}
    <div class="invite-info">
        <i class="bi bi-envelope-fill"></i>
        <div class="invite-info-text">
            <strong>Invitation par email</strong><br>
            Un email sera envoyé automatiquement à l'utilisateur avec un lien
            pour créer son mot de passe. Le lien expire après <strong>24h</strong>.
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        {{-- Nom --}}
        <div class="form-group">
            <label class="form-label-premium">Nom complet</label>
            <input type="text" name="name"
                   class="form-input-premium {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   value="{{ old('name') }}"
                   placeholder="Ex : Mamadou Diallo"
                   required autofocus>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label-premium">Adresse email</label>
            <input type="email" name="email"
                   class="form-input-premium {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   value="{{ old('email') }}"
                   placeholder="m.diallo@omaria.sn"
                   required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Rôles --}}
        <div class="form-group">
            <label class="form-label-premium">Rôle</label>
            <div class="role-cards" id="roleCards">

                @php
                    $roles = [
                        'admin' => [
                            'icon' => 'bi-shield-fill-check',
                            'name' => 'Admin',
                            'desc' => 'Accès complet à tout',
                        ],
                        'proprietaire' => [
                            'icon' => 'bi-house-fill',
                            'name' => 'Propriétaire',
                            'desc' => 'Gestion complète + utilisateurs',
                        ],
                        'gestionnaire' => [
                            'icon' => 'bi-person-badge-fill',
                            'name' => 'Gestionnaire',
                            'desc' => 'Collectes, dépenses, points',
                        ],
                        'collecteur' => [
                            'icon' => 'bi-basket-fill',
                            'name' => 'Collecteur',
                            'desc' => 'Saisie des collectes uniquement',
                        ],
                    ];
                @endphp

                @foreach($roles as $val => $info)
                    <label class="role-card-select {{ old('role') === $val ? 'selected-'.$val : '' }}"
                           id="card-{{ $val }}">
                        <input type="radio" name="role" value="{{ $val }}"
                               {{ old('role', 'collecteur') === $val ? 'checked' : '' }}
                               onchange="selectRole('{{ $val }}')">
                        <i class="bi {{ $info['icon'] }} role-card-icon {{ $val }}"></i>
                        <span class="role-card-name">{{ $info['name'] }}</span>
                        <span class="role-card-desc">{{ $info['desc'] }}</span>
                    </label>
                @endforeach

            </div>
            @error('role') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
        </div>

        {{-- Actions --}}
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="bi bi-envelope-fill"></i> Créer et envoyer l'invitation
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Annuler
            </a>
        </div>
    </form>
</div>

<script>
function selectRole(role) {
    document.querySelectorAll('.role-card-select').forEach(card => {
        card.className = 'role-card-select';
    });
    const card = document.getElementById('card-' + role);
    if (card) card.classList.add('selected-' + role);
}
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="role"]:checked');
    if (checked) selectRole(checked.value);
});
</script>
@endsection