# syntax=docker/dockerfile:1

# ---- Build stage for Vite assets ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY . .
RUN npm ci && npm run build

# ---- Final PHP-FPM image ----
FROM php:8.2-fpm-alpine

RUN apk add --no-cache sqlite sqlite-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install -j$(nproc) pdo_sqlite mbstring zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY --from=assets /app/public/build /var/www/html/public/build

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN mkdir -p /var/data && touch /var/data/database.sqlite && chown www-data:www-data /var/data/database.sqlite

RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

EXPOSE 9000
CMD ["php-fpm"]