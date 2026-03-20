FROM php:8.4-cli

# Extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev \
    libxml2-dev libonig-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring zip xml gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Installer les dépendances
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install && npm run build

EXPOSE 8000

CMD php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
