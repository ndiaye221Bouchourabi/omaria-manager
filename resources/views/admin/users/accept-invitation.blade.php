<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O'Maria — Créer mon mot de passe</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            background: #0b1423;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'DM Sans', sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Vagues animées */
        .waves-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .wave {
            position: absolute;
            width: 200%;
            height: 200%;
            border-radius: 45%;
            opacity: 0.04;
        }

        .wave-1 {
            background: #3b82f6;
            top: -60%;
            left: -50%;
            animation: wave-rotate 18s linear infinite;
        }

        .wave-2 {
            background: #10b981;
            top: -65%;
            left: -45%;
            animation: wave-rotate 24s linear infinite reverse;
        }

        .wave-3 {
            background: #6366f1;
            top: -70%;
            left: -55%;
            animation: wave-rotate 30s linear infinite;
        }

        @keyframes wave-rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Card principale */
        .card-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 460px;
            margin: 20px;
        }

        /* ════════════════════════════════════
           ✅ LOGO O'MARIA — même style login
           ════════════════════════════════════ */
        .brand {
            text-align: center;
            margin-bottom: 24px;
        }

        .brand-logo-wrap {
            margin: 0 auto 14px;
            width: 180px;
            height: 80px;
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15), 0 8px 28px rgba(13, 40, 88, 0.35);
        }

        /* Bordure animée */
        .brand-logo-wrap::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 22px;
            background: conic-gradient(from 0deg,
                    #3b82f6 0%,
                    #06b6d4 30%,
                    #fbbf24 60%,
                    #3b82f6 100%);
            animation: spin-ring 4s linear infinite;
            z-index: 0;
        }

        .brand-logo-wrap::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 18px;
            background: white;
            z-index: 1;
        }

        @keyframes spin-ring {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .brand-logo-img {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            padding: 8px 12px;
        }

        .brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }

        .brand-sub {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.45);
            margin-top: 4px;
        }

        /* Card formulaire */
        .card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
        }

        /* Bienvenue */
        .welcome-block {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(99, 102, 241, 0.1));
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .welcome-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #1d4088, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .welcome-icon i {
            color: white;
            font-size: 18px;
        }

        .welcome-name {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: white;
        }

        .welcome-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 2px;
        }

        .role-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* Titre formulaire */
        .form-title {
            font-family: 'Sora', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: white;
            margin-bottom: 6px;
        }

        .form-sub {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.45);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        /* Champs */
        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            font-family: 'Sora', sans-serif;
            font-size: 11.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i.icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            font-size: 16px;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            padding: 13px 44px 13px 42px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
            outline: none;
        }

        .input-wrap input::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .input-wrap input:focus {
            border-color: rgba(59, 130, 246, 0.6);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .input-wrap input.is-invalid {
            border-color: rgba(239, 68, 68, 0.6);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .toggle-pwd {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.3);
            font-size: 16px;
            padding: 4px;
            transition: color 0.15s;
        }

        .toggle-pwd:hover {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Barre de force */
        .strength-bar {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }

        .strength-bar span {
            flex: 1;
            height: 3px;
            border-radius: 99px;
            background: rgba(255, 255, 255, 0.1);
            transition: background 0.3s;
        }

        .strength-label {
            font-size: 11px;
            margin-top: 5px;
            color: rgba(255, 255, 255, 0.35);
            transition: color 0.3s;
        }

        /* Erreurs */
        .field-error {
            font-size: 11.5px;
            color: #fca5a5;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Règles mot de passe */
        .pwd-rules {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 22px;
        }

        .pwd-rule {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.4);
            padding: 3px 0;
            transition: color 0.2s;
        }

        .pwd-rule i {
            font-size: 11px;
            transition: color 0.2s;
        }

        .pwd-rule.ok {
            color: #6ee7b7;
        }

        .pwd-rule.ok i {
            color: #10b981;
        }

        /* Bouton submit */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0f2252, #1d4088, #3b82f6);
            background-size: 200% 200%;
            color: white;
            border: none;
            border-radius: 14px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background-position: right center;
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(59, 130, 246, 0.45);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Lien retour login */
        .back-link {
            text-align: center;
            margin-top: 18px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.3);
        }

        .back-link a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.15s;
        }

        .back-link a:hover {
            color: white;
        }
    </style>
</head>

<body>

    <!-- Fond animé -->
    <div class="waves-bg">
        <div class="wave wave-1"></div>
        <div class="wave wave-2"></div>
        <div class="wave wave-3"></div>
    </div>

    <div class="card-wrap">

        <!-- ✅ Branding avec logo O'Maria (même style que login) -->
        <div class="brand">
            <div class="brand-logo-wrap">
                <img src="{{ asset('img/logo.jpeg') }}" alt="O'Maria" class="brand-logo-img">
            </div>
            <div class="brand-name">O'Maria</div>
            <div class="brand-sub">Gestion des fontaines d'eau</div>
        </div>

        <!-- Card -->
        <div class="card">

            <!-- Bloc bienvenue -->
            <div class="welcome-block">
                <div class="welcome-icon">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <div>
                    <div class="welcome-name">Bienvenue, {{ $invitation->user->name }} !</div>
                    <div class="welcome-role">
                        Votre rôle :
                        <span class="role-pill">{{ $invitation->user->getRoleLabel() }}</span>
                    </div>
                </div>
            </div>

            <div class="form-title">Créez votre mot de passe</div>
            <div class="form-sub">
                Choisissez un mot de passe sécurisé pour accéder à la plateforme.
                Ce lien expire dans 24h.
            </div>

            <!-- Erreurs globales -->
            @if($errors->any())
                <div
                    style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); border-radius:10px; padding:12px 14px; margin-bottom:18px; font-size:12.5px; color:#fca5a5;">
                    <i class="bi bi-exclamation-circle-fill" style="margin-right:6px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Formulaire -->
            <form method="POST" action="{{ route('invitation.accept.store', $token) }}">
                @csrf

                <!-- Mot de passe -->
                <div class="field">
                    <label>Mot de passe</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill icon-left"></i>
                        <input type="password" name="password" id="password" placeholder="Minimum 8 caractères"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                            oninput="checkStrength(this.value)" required>
                        <button type="button" class="toggle-pwd" onclick="toggleVisibility('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="strength-bar">
                        <span id="bar-1"></span>
                        <span id="bar-2"></span>
                        <span id="bar-3"></span>
                        <span id="bar-4"></span>
                    </div>
                    <div class="strength-label" id="strength-label">Saisissez votre mot de passe</div>

                    @error('password')
                        <div class="field-error">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Règles -->
                <div class="pwd-rules">
                    <div class="pwd-rule" id="rule-length">
                        <i class="bi bi-circle"></i> Au moins 8 caractères
                    </div>
                    <div class="pwd-rule" id="rule-upper">
                        <i class="bi bi-circle"></i> Une lettre majuscule
                    </div>
                    <div class="pwd-rule" id="rule-number">
                        <i class="bi bi-circle"></i> Un chiffre
                    </div>
                </div>

                <!-- Confirmation -->
                <div class="field">
                    <label>Confirmer le mot de passe</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill icon-left"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Répétez le mot de passe"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                        <button type="button" class="toggle-pwd"
                            onclick="toggleVisibility('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-circle-fill"></i>
                    Créer mon mot de passe et accéder
                </button>
            </form>
        </div>

        <div class="back-link">
            Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
        </div>
    </div>

    <script>
        function toggleVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        function checkStrength(val) {
            const rules = {
                'rule-length': val.length >= 8,
                'rule-upper': /[A-Z]/.test(val),
                'rule-number': /[0-9]/.test(val),
            };

            let score = 0;
            Object.entries(rules).forEach(([id, ok]) => {
                const el = document.getElementById(id);
                const icon = el.querySelector('i');
                if (ok) {
                    el.classList.add('ok');
                    icon.className = 'bi bi-check-circle-fill';
                    score++;
                } else {
                    el.classList.remove('ok');
                    icon.className = 'bi bi-circle';
                }
            });

            const colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
            const labels = ['', 'Faible', 'Moyen', 'Fort', 'Très fort'];
            const barFill = val.length === 0 ? 0 : Math.min(score + (val.length >= 12 ? 1 : 0), 4);

            for (let i = 1; i <= 4; i++) {
                document.getElementById('bar-' + i).style.background =
                    i <= barFill ? colors[barFill] : 'rgba(255,255,255,0.1)';
            }

            const label = document.getElementById('strength-label');
            label.textContent = val.length === 0 ? 'Saisissez votre mot de passe' : labels[barFill] || 'Faible';
            label.style.color = val.length === 0 ? 'rgba(255,255,255,0.35)' : colors[barFill];
        }
    </script>
</body>

</html>