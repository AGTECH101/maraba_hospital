# syntax=docker/dockerfile:1

# ---- Build stage for Vite assets ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY . .
RUN npm ci && npm run build

# ---- Final PHP-FPM image ----
FROM php:8.2-fpm-alpine

# Install system dependencies (SQLite only)
RUN apk add --no-cache sqlite sqlite-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install -j$(nproc) pdo_sqlite mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application code
COPY . .

# ---- Set environment variables for build ----
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY=base64:abcdefghijklmnopqrstuvwxyz1234567890= \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/data/database.sqlite \
    CACHE_DRIVER=file \
    SESSION_DRIVER=file \
    QUEUE_CONNECTION=sync

# ---- Create required directories and set permissions ----
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/framework/testing \
    && mkdir -p bootstrap/cache \
    && mkdir -p /var/data \
    && touch /var/data/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache /var/data \
    && chmod -R 775 storage bootstrap/cache /var/data

# ---- Install PHP dependencies (skip scripts to avoid early failure) ----
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# ---- Run package discover manually (now environment is ready) ----
RUN php artisan package:discover --ansi

# ---- Copy built Vite assets from the assets stage ----
COPY --from=assets /app/public/build /var/www/html/public/build

# ---- Laravel optimizations ----
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 9000
CMD ["php-fpm"]