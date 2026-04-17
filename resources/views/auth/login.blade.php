<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O'Maria — Connexion</title>

    {{-- ═══════════════════════════════════════════════════════
    PWA — manifest + icône Apple
    ═══════════════════════════════════════════════════════ --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b1423">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="O'Maria">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    {{-- ═══════════════════════════════════════════════════════ --}}

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
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

        .brand-logo { display: flex; align-items: center; gap: 16px; position: relative; z-index: 1; }

        /* Badge ovale — même style que le logo du formulaire */
        .brand-logo-box {
            width: 110px;
            height: 50px;
            position: relative;
            border-radius: 30px;
            flex-shrink: 0;
        }
        /* Anneau animé bleu clair */
        .brand-logo-box::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 32px;
           
        }
        /* Fond blanc intérieur */
        .brand-logo-box::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 28px;
            background: white;
            z-index: 1;
        }
        .brand-logo-box img {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 6px 10px;
        }
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
            flex: 1;
            background: #eef6fb;
            background-image:
                radial-gradient(ellipse 500px 400px at 80% 10%, rgba(56,189,248,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 300px 300px at 20% 90%, rgba(14,165,233,0.07) 0%, transparent 50%);
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

        /* ════════════════════════════════════════════
           ✅ LOGO O'MARIA — style ovale bleu clair
           ressemblant au badge Ma.G / O'Maria image 2
           ════════════════════════════════════════════ */
        .login-card-header { text-align: center; margin-bottom: 2rem; }

        /* Conteneur ovale extérieur — imite le badge blanc ovale */
        .login-logo-wrap {
            margin: 0 auto 1.4rem;
            width: 200px;
            height: 90px;
            position: relative;
            border-radius: 50px;         /* bord très arrondi = ovale */
            background: #ffffff;
            /* Ombre portée douce bleue comme sur le badge */
            box-shadow:
                0 0 0 3px rgba(56,189,248,0.30),
                0 0 0 6px rgba(56,189,248,0.12),
                0 10px 32px rgba(14,100,160,0.18);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Anneau tournant bleu-ciel + cyan autour de l'ovale */
        .login-logo-wrap::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 54px;
            background: conic-gradient(
                from 0deg,
                #0b0386fb  0%,
                #0b0386fb 25%,
                #bae6fd 50%,
               #0b0386fb  75%,
               #0b0386fb  100%
            );
            animation: spin-ring 3.5s linear infinite;
            z-index: 0;
        }

        /* Fond blanc intérieur qui masque l'anneau derrière l'image */
        .login-logo-wrap::after {
            content: '';
            position: absolute;
            inset: 3px;
            border-radius: 48px;
            background: white;
            z-index: 1;
        }

        @keyframes spin-ring {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* Image du logo — reste au-dessus du pseudo-element blanc */
        .login-logo-img {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            padding: 10px 16px;
        }

        /* Badge "Ma.G" au-dessus à gauche — détail du badge image 2 */
        .logo-badge-label {
            position: absolute;
            top: 8px;
            left: 16px;
            z-index: 3;
            font-family: 'DM Sans', sans-serif;
            font-size: 9px;
            font-weight: 600;
            color: #0369a1;
            letter-spacing: 0.5px;
            opacity: 0.8;
        }

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
        .form-input-p:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56,189,248,0.15); }
        .form-input-wrap:focus-within .form-input-icon { color: #38bdf8; }

        .toggle-password {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 16px; padding: 2px; transition: color 0.2s;
        }
        .toggle-password:hover { color: #38bdf8; }

        /* Ligne remember + forgot */
        .form-extras {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem; font-size: 13px;
        }
        .form-check-label { display: flex; align-items: center; gap: 7px; color: #475569; cursor: pointer; }
        .form-check-input-p { width: 16px; height: 16px; border: 1.5px solid rgba(15,34,82,0.2); border-radius: 4px; accent-color: #38bdf8; cursor: pointer; }
        .forgot-link {
            color: #38bdf8; text-decoration: none; font-weight: 500; font-size: 13px;
            display: flex; align-items: center; gap: 4px; transition: color 0.2s;
        }
        .forgot-link:hover { color: #0ea5e9; text-decoration: underline; }

        /* Bouton connexion — bleu clair */
        .btn-login {
            width: 100%; padding: 12px;
            background: linear-gradient(135deg, #090170, #0572a1);
            color: white; border: none; border-radius: 12px;
            font-family: 'Sora', sans-serif; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.34,1.3,0.64,1);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 14px rgba(14,165,233,0.35);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14,165,233,0.45);
        }

        /* Niveaux d'accès */
        .roles-section { margin-top: 1.75rem; padding-top: 1.5rem; border-top: 1px solid rgba(15,34,82,0.08); }
        .roles-title {
            font-family: 'Sora', sans-serif; font-size: 11px; font-weight: 600;
            letter-spacing: 1px; text-transform: uppercase; color: #94a3b8;
            text-align: center; margin-bottom: 12px;
        }
        .roles-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .role-card {
            padding: 12px 8px; border-radius: 12px; border: 1.5px solid transparent;
            text-align: center; cursor: default; transition: all 0.2s;
        }
        .role-card:hover { transform: translateY(-2px); }

        /* Propriétaire — violet */
        .role-card.proprietaire { background: rgba(139,92,246,0.06); border-color: rgba(139,92,246,0.2); }
        .role-card.proprietaire .role-card-icon { color: #8b5cf6; }
        .role-card.proprietaire .role-card-name { color: #4c1d95; }

        /* Gestionnaire — bleu clair sky */
        .role-card.gestionnaire { background: rgba(56,189,248,0.07); border-color: rgba(56,189,248,0.25); }
        .role-card.gestionnaire .role-card-icon { color: #0ea5e9; }
        .role-card.gestionnaire .role-card-name { color: #0369a1; }

        /* Collecteur — vert */
        .role-card.collecteur { background: rgba(16,185,129,0.06); border-color: rgba(16,185,129,0.2); }
        .role-card.collecteur .role-card-icon { color: #10b981; }
        .role-card.collecteur .role-card-name { color: #065f46; }

        /* Icônes rôles — plus jolies avec cercle de fond */
        .role-card-icon-wrap {
            width: 38px; height: 38px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 6px;
        }
        .role-card.proprietaire .role-card-icon-wrap { background: rgba(139,92,246,0.12); }
        .role-card.gestionnaire .role-card-icon-wrap { background: rgba(56,189,248,0.12); }
        .role-card.collecteur   .role-card-icon-wrap { background: rgba(16,185,129,0.12); }

        .role-card-icon { font-size: 18px; display: block; }
        .role-card-name { font-family: 'Sora', sans-serif; font-size: 11px; font-weight: 700; display: block; margin-bottom: 2px; }
        .role-card-desc { font-size: 10px; color: #94a3b8; line-height: 1.3; }

        /* ---- Responsive ---- */

        /* Grand desktop */
        @media (max-width: 1200px) {
            .login-brand { width: 42%; padding: 2.5rem 2rem; }
            .brand-tagline { font-size: 2rem; }
        }

        /* Tablette paysage — côte à côte, panneau gauche compact */
        @media (max-width: 992px) {
            .login-brand {
                width: 40%;
                padding: 2rem 1.75rem;
                min-height: 100vh;
            }
            .brand-tagline { font-size: 1.65rem; }
            .brand-desc { font-size: 12.5px; }
            .brand-stat-value { font-size: 1.3rem; }
            .brand-stats { gap: 1.25rem; }
            .login-form-panel { padding: 1.5rem 1rem; }
            .login-card { padding: 2rem 1.5rem; }
        }

        /* Tablette portrait — côte à côte encore plus compact */
        @media (max-width: 850px) {
            .login-brand {
                width: 36%;
                padding: 1.75rem 1.25rem;
            }
            .brand-tagline { font-size: 1.35rem; }
            .brand-desc { font-size: 12px; max-width: 220px; }
            .brand-stat-value { font-size: 1.1rem; }
            .brand-stat-label { font-size: 10px; }
            .brand-stats { gap: 1rem; }
            .brand-name { font-size: 1rem; letter-spacing: 2px; }
            .login-card { padding: 1.75rem 1.25rem; }
        }

        /* Mobile — panneau gauche totalement masqué */
        @media (max-width: 767px) {
            body {
                flex-direction: column;
                overflow-y: auto;
                overflow-x: hidden;
            }

            /* CACHE sur mobile */
            .login-brand {
                display: none !important;
            }

            /* Formulaire centré plein écran */
            .login-form-panel {
                width: 100%;
                min-height: 100vh;
                padding: 2rem 1.25rem;
                background: #eef6fb;
                align-items: center;
            }

            .login-card {
                padding: 2rem 1.5rem;
                border-radius: 20px;
                margin: 0 auto;
                box-shadow: 0 4px 24px rgba(6,13,31,0.10);
            }

            .login-logo-wrap { width: 180px; height: 80px; }
            .roles-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 400px) {
            .login-card { padding: 1.5rem 1rem; }
            .roles-grid { grid-template-columns: 1fr; }
            .login-logo-wrap { width: 160px; height: 72px; }
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
        <div class="brand-stat-value">
            @php
                try { echo \App\Models\Point::count(); } catch(\Exception $e) { echo '--'; }
            @endphp
        </div>
        <div class="brand-stat-label">Points actifs</div>
    </div>
    <div class="brand-stat">
        <div class="brand-stat-value">
            @php
                try { echo \App\Models\Collecte::count(); } catch(\Exception $e) { echo '--'; }
            @endphp
        </div>
        <div class="brand-stat-label">Collectes</div>
    </div>
    <div class="brand-stat">
        <div class="brand-stat-value">
            @php
                try { echo \App\Models\Point::where('status','Actif')->count(); } catch(\Exception $e) { echo '--'; }
            @endphp
        </div>
        <div class="brand-stat-label">En service</div>
    </div>
</div>
    </div>

    {{-- Panneau droit formulaire --}}
    <div class="login-form-panel">
        <div class="login-card">

            {{-- ✅ Logo O'Maria — ovale bleu clair avec anneau animé --}}
            <div class="login-card-header">
                <div class="login-logo-wrap">
                    <span class="logo-badge-label">Ma.G</span>
                    <img
                        src="{{ asset('img/logo.jpeg') }}"
                        alt="O'Maria"
                        class="login-logo-img"
                    >
                </div>
                <h2 class="login-title">Bienvenue</h2>
                <p class="login-sub">Connectez-vous à votre espace de gestion</p>
            </div>

            {{-- Message succès --}}
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
                        <i class="bi bi-envelope-fill form-input-icon"></i>
                        <input type="email" id="email" name="email" class="form-input-p"
                               value="{{ old('email') }}" placeholder="admin@gmail.com"
                               autocomplete="email" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label-p" for="password">Mot de passe</label>
                    <div class="form-input-wrap">
                        <i class="bi bi-lock-fill form-input-icon"></i>
                        <input type="password" id="password" name="password" class="form-input-p"
                               placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" class="toggle-password" onclick="togglePwd()">
                            <i class="bi bi-eye-fill" id="pwd-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-extras">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input-p" name="remember">
                        Se souvenir de moi
                    </label>
                    <span class="forgot-link" style="color:#94a3b8; cursor:default;">
                        <i class="bi bi-key-fill"></i>
                        Contactez votre admin
                    </span>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Se connecter
                </button>
            </form>

            {{-- Niveaux d'accès --}}
            <div class="roles-section">
                <p class="roles-title">Niveaux d'accès</p>
                <div class="roles-grid">
                    <div class="role-card proprietaire">
                        <div class="role-card-icon-wrap">
                            <i class="bi bi-shield-fill-check role-card-icon"></i>
                        </div>
                        <span class="role-card-name">Propriétaire</span>
                        <span class="role-card-desc">Gestion complète</span>
                    </div>
                    <div class="role-card gestionnaire">
                        <div class="role-card-icon-wrap">
                            <i class="bi bi-person-badge-fill role-card-icon"></i>
                        </div>
                        <span class="role-card-name">Gestionnaire</span>
                        <span class="role-card-desc">Collectes + dépenses</span>
                    </div>
                    <div class="role-card collecteur">
                        <div class="role-card-icon-wrap">
                            <i class="bi bi-droplet-fill role-card-icon"></i>
                        </div>
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
                eye.className = 'bi bi-eye-slash-fill';
            } else {
                pwd.type = 'password';
                eye.className = 'bi bi-eye-fill';
            }
        }

        /* ================================================================
           PWA — Enregistrement du Service Worker
        ================================================================ */
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(() => console.log('✅ PWA Service Worker enregistré'))
                    .catch(err => console.warn('SW erreur :', err));
            });
        }
    </script>

</body>
</html>