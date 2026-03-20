<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O'Maria — Réinitialiser mon mot de passe</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap"
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
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .reset-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 8px 32px rgba(6, 13, 31, 0.1);
            border: 1px solid rgba(15, 34, 82, 0.07);
        }

        .reset-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #0f2252, #1d4088);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 6px 18px rgba(13, 40, 88, 0.3);
        }

        .reset-icon i {
            color: #fbbf24;
            font-size: 24px;
        }

        .reset-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            text-align: center;
            margin-bottom: 6px;
        }

        .reset-sub {
            font-size: 13px;
            color: #64748b;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-label-p {
            display: block;
            font-family: 'Sora', sans-serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-input-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .form-input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
        }

        .form-input-p {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid rgba(15, 34, 82, 0.1);
            border-radius: 12px;
            font-size: 14px;
            color: #0f172a;
            background: white;
            outline: none;
            transition: all 0.2s;
        }

        .form-input-p:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .btn-reset {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0f2252, #1d4088);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(13, 40, 88, 0.3);
            margin-top: 0.5rem;
        }

        .btn-reset:hover {
            background: linear-gradient(135deg, #163068, #2554b0);
            transform: translateY(-2px);
        }

        .form-error {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 10px 14px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            color: #7f1d1d;
            font-size: 13px;
            margin-bottom: 1.25rem;
        }
    </style>
</head>

<body>
    <div class="reset-card">
        <div class="reset-icon">
            <i class="bi bi-key-fill"></i>
        </div>
        <h2 class="reset-title">Nouveau mot de passe</h2>
        <p class="reset-sub">Choisissez un nouveau mot de passe sécurisé pour votre compte</p>

        @if($errors->any())
            <div class="form-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.reset.store', $token) }}">
            @csrf
            <label class="form-label-p">Nouveau mot de passe</label>
            <div class="form-input-wrap">
                <i class="bi bi-lock form-input-icon"></i>
                <input type="password" name="password" class="form-input-p" placeholder="••••••••" required>
            </div>

            <label class="form-label-p">Confirmer le mot de passe</label>
            <div class="form-input-wrap">
                <i class="bi bi-lock-fill form-input-icon"></i>
                <input type="password" name="password_confirmation" class="form-input-p" placeholder="••••••••"
                    required>
            </div>

            <button type="submit" class="btn-reset">
                <i class="bi bi-check-circle"></i>
                Réinitialiser mon mot de passe
            </button>
        </form>
    </div>
</body>

</html>