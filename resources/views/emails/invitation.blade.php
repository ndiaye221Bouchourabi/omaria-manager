<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation O'Maria</title>
</head>

<body style="margin:0; padding:0; background-color:#f0f4f8; font-family:'Segoe UI', Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="560" cellpadding="0" cellspacing="0" style="max-width:560px; width:100%;">

                    <!-- LOGO + NOM -->
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td
                                        style="background: linear-gradient(135deg, #0f2252, #1d4088); border-radius: 14px; width: 48px; height: 48px; text-align: center; vertical-align: middle; padding: 12px;">
                                        <span style="color: white; font-size: 22px;">💧</span>
                                    </td>
                                    <td style="padding-left: 12px; vertical-align: middle;">
                                        <div
                                            style="font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: 1px;">
                                            O'MARIA</div>
                                        <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Gestion des
                                            fontaines d'eau</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CARTE PRINCIPALE -->
                    <tr>
                        <td
                            style="background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                            <table width="100%" cellpadding="0" cellspacing="0">

                                <!-- Barre colorée top -->
                                <tr>
                                    <td style="background: linear-gradient(90deg, #0f2252, #3b82f6); height: 4px;"></td>
                                </tr>

                                <!-- Contenu -->
                                <tr>
                                    <td style="padding: 40px 40px 32px;">

                                        <!-- Icône + titre invitation -->
                                        <table cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                                            <tr>
                                                <td
                                                    style="background: #eff6ff; border-radius: 12px; width: 52px; height: 52px; text-align: center; vertical-align: middle;">
                                                    <span style="font-size: 24px;">✉️</span>
                                                </td>
                                                <td style="padding-left: 14px; vertical-align: middle;">
                                                    <div style="font-size: 18px; font-weight: 700; color: #0f172a;">Vous
                                                        êtes invité(e) !</div>
                                                    <div style="font-size: 13px; color: #64748b; margin-top: 3px;">
                                                        Rejoignez O'Maria dès maintenant</div>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Message -->
                                        <p style="font-size: 15px; color: #374151; line-height: 1.7; margin: 0 0 16px;">
                                            Bonjour <strong style="color: #0f172a;">{{ $name }}</strong>,
                                        </p>
                                        <p style="font-size: 15px; color: #374151; line-height: 1.7; margin: 0 0 24px;">
                                            Vous avez été invité(e) à rejoindre la plateforme
                                            <strong style="color: #0f172a;">O'Maria</strong>.
                                            Votre accès a été configuré avec le rôle suivant :
                                        </p>

                                        <!-- Badge rôle -->
                                        <table cellpadding="0" cellspacing="0" style="margin-bottom: 28px;">
                                            <tr>
                                                <td
                                                    style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 99px; padding: 8px 20px;">
                                                    <span style="font-size: 14px; font-weight: 700; color: #1e40af;">🎯
                                                        {{ $role }}</span>
                                                </td>
                                            </tr>
                                        </table>

                                        <p style="font-size: 15px; color: #374151; line-height: 1.7; margin: 0 0 28px;">
                                            Cliquez sur le bouton ci-dessous pour créer votre mot de passe
                                            et accéder à la plateforme :
                                        </p>

                                        <!-- Bouton CTA -->
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                            style="margin-bottom: 28px;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $url }}" style="display: inline-block;
                                                              background: linear-gradient(135deg, #0f2252, #1d4088);
                                                              color: #ffffff; text-decoration: none;
                                                              padding: 16px 40px; border-radius: 12px;
                                                              font-size: 15px; font-weight: 700;
                                                              letter-spacing: 0.3px;
                                                              box-shadow: 0 4px 14px rgba(13,40,88,0.3);">
                                                        🔑 &nbsp; Créer mon mot de passe
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Séparateur -->
                                        <table cellpadding="0" cellspacing="0" width="100%"
                                            style="margin-bottom: 20px;">
                                            <tr>
                                                <td style="border-top: 1px solid #f1f5f9; height: 1px;"></td>
                                            </tr>
                                        </table>

                                        <!-- Alerte expiration -->
                                        <table cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td
                                                    style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 16px;">
                                                    <p
                                                        style="margin: 0; font-size: 13px; color: #78350f; line-height: 1.6;">
                                                        ⚠️ &nbsp; Ce lien est valable <strong>24 heures</strong>.
                                                        Passé ce délai, contactez votre administrateur pour recevoir un
                                                        nouveau lien.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center" style="padding: 24px 0 0;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.6;">
                                O'Maria — Plateforme de gestion des fontaines d'eau<br>
                                <span style="color: #cbd5e1;">Ne pas répondre à cet email</span>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>