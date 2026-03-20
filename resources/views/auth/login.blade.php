<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O'Maria — Connexion</title>

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ---- Panneau gauche ---- */
        .login-brand {
            width: 45%;
            background: linear-gradient(165deg, rgba(13,28,72,0.97) 0%, rgba(6,13,31,0.99) 60%, rgba(8,20,50,0.98) 100%);
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 3rem; position: relative; overflow: hidden;
        }
        .login-brand::before {
            content: ''; position: absolute; top: -80px; left: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .brand-waves {
            position: absolute; bottom: 0; left: 0; width: 200%; height: 140px;
            background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 140'><path fill='%2306b6d4' fill-opacity='0.10' d='M0,80L60,74.7C120,69,240,59,360,64C480,69,600,91,720,96C840,101,960,91,1080,80C1200,69,1320,59,1380,53.3L1440,48L1440,140L0,140Z'/></svg>") repeat-x;
            background-size: 50% 140px;
            animation: wave 14s linear infinite; pointer-events: none;
        }
        .brand-waves.back { bottom: 10px; opacity: 0.5; animation: wave 20s linear infinite reverse; }
        @keyframes wave { from { transform: translateX(0); } to { transform: translateX(-50%); } }

        .brand-logo { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; }
        .brand-logo-box {
            width: 44px; height: 44px; background: white; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; box-shadow: 0 4px 16px rgba(6,13,31,0.4);
        }
        .brand-logo-box img { width: 100%; height: 100%; object-fit: cover; }
        .brand-name { font-family: 'Sora', sans-serif; font-weight: 800; font-size: 1.2rem; color: white; letter-spacing: 3px; }

        .brand-center { position: relative; z-index: 1; }
        .brand-tagline { font-family: 'Sora', sans-serif; font-size: 2.2rem; font-weight: 800; color: white; line-height: 1.15; margin-bottom: 1.25rem; }
        .brand-tagline span { background: linear-gradient(90deg, #fbbf24, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .brand-desc { font-size: 14px; color: rgba(255,255,255,0.45); line-height: 1.7; max-width: 340px; }

        .brand-stats { display: flex; gap: 2rem; position: relative; z-index: 1; padding-bottom: 1.5rem; }
        .brand-stat-value { font-family: 'Sora', sans-serif; font-size: 1.6rem; font-weight: 800; color: white; line-height: 1; }
        .brand-stat-label { font-size: 11px; color: rgba(255,255,255,0.35); font-weight: 500; letter-spacing: 0.5px; }

        /* ---- Panneau droit ---- */
        .login-form-panel {
            flex: 1; background: #f0f4f8;
            background-image:
                radial-gradient(ellipse 500px 400px at 80% 10%, rgba(59,130,246,0.05) 0%, transparent 60%),
                radial-gradient(ellipse 300px 300px at 20% 90%, rgba(245,158,11,0.04) 0%, transparent 50%);
            display: flex; align-items: center; justify-content: center; padding: 2rem;
        }

        .login-card {
            background: white; border-radius: 24px; padding: 2.5rem;
            width: 100%; max-width: 440px;
            box-shadow: 0 1px 3px rgba(6,13,31,0.06), 0 8px 24px rgba(6,13,31,0.08), 0 24px 60px rgba(6,13,31,0.06);
            border: 1px solid rgba(15,34,82,0.07);
            animation: fadeUp 0.5s cubic-bezier(0.34,1.2,0.64,1);
        }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .login-card-header { text-align: center; margin-bottom: 2rem; }
        .login-logo-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #0f2252, #1d4088);
            border-radius: 14px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem; box-shadow: 0 6px 18px rgba(13,40,88,0.3);
        }
        .login-logo-icon i { color: white; font-size: 22px; }
        .login-title { font-family: 'Sora', sans-serif; font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .login-sub { font-size: 13.5px; color: #64748b; }

        /* Alertes */
        .form-error {
            display: flex; align-items: center; gap: 7px; padding: 10px 14px;
            background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);
            border-radius: 10px; color: #7f1d1d; font-size: 13px; margin-bottom: 1.25rem;
        }
        .form-success {
            display: flex; align-items: center; gap: 7px; padding: 10px 14px;
            background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2);
            border-radius: 10px; color: #065f46; font-size: 13px; margin-bottom: 1.25rem;
        }

        /* Champs */
        .form-group { margin-bottom: 1.25rem; }
        .form-label-p {
            display: block; font-family: 'Sora', sans-serif; font-size: 11px; font-weight: 600;
            letter-spacing: 0.5px; text-transform: uppercase; color: #475569; margin-bottom: 6px;
        }
        .form-input-wrap { position: relative; }
        .form-input-icon {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 16px; pointer-events: none; transition: color 0.2s;
        }
        .form-input-p {
            width: 100%; padding: 11px 14px 11px 40px;
            border: 1.5px solid rgba(15,34,82,0.1); border-radius: 12px;
            font-family: 'DM Sans', sans-serif; font-size: 14px; color: #0f172a;
            background: white; outline: none; transition: all 0.2s;
        }
        .form-input-p:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
        .form-input-wrap:focus-within .form-input-icon { color: #3b82f6; }

        .toggle-password {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 16px; padding: 2px; transition: color 0.2s;
        }
        .toggle-password:hover { color: #3b82f6; }

        /* Ligne remember + forgot */
        .form-extras {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem; font-size: 13px;
        }
        .form-check-label { display: flex; align-items: center; gap: 7px; color: #475569; cursor: pointer; }
        .form-check-input-p { width: 16px; height: 16px; border: 1.5px solid rgba(15,34,82,0.2); border-radius: 4px; accent-color: #3b82f6; cursor: pointer; }
        .forgot-link {
            color: #3b82f6; text-decoration: none; font-weight: 500; font-size: 13px;
            display: flex; align-items: center; gap: 4px; transition: color 0.2s;
        }
        .forgot-link:hover { color: #1d4ed8; text-decoration: underline; }

        /* Bouton connexion */
        .btn-login {
            width: 100%; padding: 12px;
            background: linear-gradient(135deg, #0f2252, #1d4088);
            color: white; border: none; border-radius: 12px;
            font-family: 'Sora', sans-serif; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.34,1.3,0.64,1);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 14px rgba(13,40,88,0.3);
        }
        .btn-login:hover { background: linear-gradient(135deg, #163068, #2554b0); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(13,40,88,0.4); }

        /* Niveaux d'accès — 4 rôles en 2x2 */
        .roles-section { margin-top: 1.75rem; padding-top: 1.5rem; border-top: 1px solid rgba(15,34,82,0.08); }
        .roles-title {
            font-family: 'Sora', sans-serif; font-size: 11px; font-weight: 600;
            letter-spacing: 1px; text-transform: uppercase; color: #94a3b8;
            text-align: center; margin-bottom: 12px;
        }
        .roles-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .role-card { padding: 10px 8px; border-radius: 10px; border: 1.5px solid transparent; text-align: center; cursor: default; transition: all 0.2s; }
        .role-card.admin        { background: rgba(245,158,11,0.06);  border-color: rgba(245,158,11,0.2); }
        .role-card.proprietaire { background: rgba(139,92,246,0.06);  border-color: rgba(139,92,246,0.2); }
        .role-card.gestionnaire { background: rgba(59,130,246,0.06);  border-color: rgba(59,130,246,0.2); }
        .role-card.collecteur   { background: rgba(16,185,129,0.06);  border-color: rgba(16,185,129,0.2); }
        .role-card-icon { font-size: 18px; margin-bottom: 4px; display: block; }
        .role-card.admin        .role-card-icon { color: #f59e0b; }
        .role-card.proprietaire .role-card-icon { color: #8b5cf6; }
        .role-card.gestionnaire .role-card-icon { color: #3b82f6; }
        .role-card.collecteur   .role-card-icon { color: #10b981; }
        .role-card-name { font-family: 'Sora', sans-serif; font-size: 11px; font-weight: 600; display: block; margin-bottom: 2px; }
        .role-card.admin        .role-card-name { color: #92400e; }
        .role-card.proprietaire .role-card-name { color: #4c1d95; }
        .role-card.gestionnaire .role-card-name { color: #1e40af; }
        .role-card.collecteur   .role-card-name { color: #065f46; }
        .role-card-desc { font-size: 10px; color: #94a3b8; line-height: 1.3; }

        @media (max-width: 768px) {
            .login-brand { display: none; }
            .login-form-panel { padding: 1rem; }
        }
    </style>
</head>

<body>

    {{-- Panneau gauche branding --}}
    <div class="login-brand">
        <div class="brand-waves"></div>
        <div class="brand-waves back"></div>

        <div class="brand-logo">
            <div class="brand-logo-box">
                <img src="{{ asset('img/logo.jpeg') }}" alt="O'Maria">
            </div>
            <span class="brand-name">O'MARIA</span>
        </div>

        <div class="brand-center">
            <h1 class="brand-tagline">
                Gestion financière<br>
                <span>intelligente</span><br>
                de vos fontaines
            </h1>
            <p class="brand-desc">
                Suivez vos collectes, maîtrisez vos dépenses, analysez
                la rentabilité de chaque point de distribution en temps réel.
            </p>
        </div>

        <div class="brand-stats">
            <div class="brand-stat">
                <div class="brand-stat-value">{{ \App\Models\Point::count() }}</div>
                <div class="brand-stat-label">Points actifs</div>
            </div>
            <div class="brand-stat">
                <div class="brand-stat-value">{{ \App\Models\Collecte::count() }}</div>
                <div class="brand-stat-label">Collectes</div>
            </div>
            <div class="brand-stat">
                <div class="brand-stat-value">{{ \App\Models\Point::where('status', 'Actif')->count() }}</div>
                <div class="brand-stat-label">En service</div>
            </div>
        </div>
    </div>

    {{-- Panneau droit formulaire --}}
    <div class="login-form-panel">
        <div class="login-card">

            <div class="login-card-header">
                <div class="login-logo-icon">
                    <i class="bi bi-droplet-fill"></i>
                </div>
                <h2 class="login-title">Bienvenue</h2>
                <p class="login-sub">Connectez-vous à votre espace de gestion</p>
            </div>

            {{-- Message succès (ex: email réinitialisation envoyé) --}}
            @if(session('status'))
                <div class="form-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Erreurs --}}
            @if($errors->any())
                <div class="form-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="form-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label-p" for="email">Adresse email</label>
                    <div class="form-input-wrap">
                        <i class="bi bi-envelope form-input-icon"></i>
                        <input type="email" id="email" name="email" class="form-input-p"
                               value="{{ old('email') }}" placeholder="admin@omaria.sn"
                               autocomplete="email" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label-p" for="password">Mot de passe</label>
                    <div class="form-input-wrap">
                        <i class="bi bi-lock form-input-icon"></i>
                        <input type="password" id="password" name="password" class="form-input-p"
                               placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" class="toggle-password" onclick="togglePwd()">
                            <i class="bi bi-eye" id="pwd-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember me + Mot de passe oublié --}}
                <div class="form-extras">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input-p" name="remember">
                        Se souvenir de moi
                    </label>

                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            <i class="bi bi-key"></i>
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Se connecter
                </button>
            </form>

            {{-- Niveaux d'accès — 4 rôles --}}
            <div class="roles-section">
                <p class="roles-title">Niveaux d'accès</p>
                <div class="roles-grid">

                    

                    <div class="role-card proprietaire">
                        <i class="bi bi-house-fill role-card-icon"></i>
                        <span class="role-card-name">Propriétaire</span>
                        <span class="role-card-desc">Gestion complète</span>
                    </div>

                    <div class="role-card gestionnaire">
                        <i class="bi bi-person-badge-fill role-card-icon"></i>
                        <span class="role-card-name">Gestionnaire</span>
                        <span class="role-card-desc">Collectes + dépenses</span>
                    </div>

                    <div class="role-card collecteur">
                        <i class="bi bi-basket-fill role-card-icon"></i>
                        <span class="role-card-name">Collecteur</span>
                        <span class="role-card-desc">Saisie collectes</span>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        function togglePwd() {
            const pwd = document.getElementById('password');
            const eye = document.getElementById('pwd-eye');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                eye.className = 'bi bi-eye-slash';
            } else {
                pwd.type = 'password';
                eye.className = 'bi bi-eye';
            }
        }
    </script>

</body>
</html>