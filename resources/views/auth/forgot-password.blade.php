<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O'Maria — Mot de passe oublié</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            background: #0b1423;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

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

        .card-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            margin: 20px;
        }

        .brand {
            text-align: center;
            margin-bottom: 24px;
        }

        .brand-logo {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #0f2252, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.35);
        }

        .brand-logo i {
            color: white;
            font-size: 24px;
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

        .card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.4);
        }

        .card-title {
            font-family: 'Sora', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
            text-align: center;
        }

        .card-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            text-align: center;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .alert-success {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: #6ee7b7;
            margin-bottom: 20px;
        }

        .alert-error {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: #fca5a5;
            margin-bottom: 20px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-family: 'Sora', sans-serif;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i.icon {
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
            padding: 13px 14px 13px 42px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: all 0.2s;
        }

        .input-wrap input::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .input-wrap input:focus {
            border-color: rgba(59, 130, 246, 0.6);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0f2252, #1d4088, #3b82f6);
            color: white;
            border: none;
            border-radius: 14px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(59, 130, 246, 0.45);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.35);
        }

        .back-link a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.15s;
        }

        .back-link a:hover {
            color: white;
        }
    </style>
</head>

<body>
    <div class="waves-bg">
        <div class="wave wave-1"></div>
        <div class="wave wave-2"></div>
        <div class="wave wave-3"></div>
    </div>

    <div class="card-wrap">
        <div class="brand">
            <div class="brand-logo"><i class="bi bi-droplet-fill"></i></div>
            <div class="brand-name">O'Maria</div>
            <div class="brand-sub">Gestion des fontaines d'eau</div>
        </div>

        <div class="card">
            <div class="card-title">Mot de passe oublié ?</div>
            <div class="card-desc">
                Pas de souci. Indiquez votre adresse email et nous vous enverrons
                un lien de réinitialisation.
            </div>

            @if(session('status'))
                <div class="alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="field">
                    <label>Adresse email</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="votre@email.com"
                            required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bi bi-send-fill"></i>
                    Envoyer le lien de réinitialisation
                </button>
            </form>
        </div>

        <div class="back-link">
            <a href="{{ route('login') }}">
                <i class="bi bi-arrow-left"></i> Retour à la connexion
            </a>
        </div>
    </div>
</body>

</html>