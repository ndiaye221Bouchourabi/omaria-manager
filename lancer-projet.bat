@"
@echo off
title O'Maria Manager
color 0A

echo ========================================
echo    DEMARRAGE DU PROJET LARAVEL
echo    O'Maria Manager
echo ========================================
echo.

cd /d C:\xampp\htdocs\omaria-manager

echo [1/3] Verification de l'environnement...
php artisan --version
echo.

echo [2/3] Demarrage du serveur...
echo.
echo Le serveur sera accessible a: http://127.0.0.1:8000
echo.
echo Pour arreter le serveur: Ctrl + C
echo.
echo ========================================
echo.

php artisan serve

pause
"@ | Out-File -FilePath "$env:USERPROFILE\Desktop\lancer-projet.bat" -Encoding ASCII