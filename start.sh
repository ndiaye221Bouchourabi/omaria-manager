#!/bin/bash
sleep 5
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan route:clear
php artisan queue:work --tries=3 &
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}