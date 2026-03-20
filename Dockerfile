FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev \
    libxml2-dev libonig-dev nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring zip xml gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install && npm run build

EXPOSE 8080

RUN chmod +x /app/start.sh
CMD ["/bin/bash", "/app/start.sh"]