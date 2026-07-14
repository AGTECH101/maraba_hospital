# syntax=docker/dockerfile:1

# ---- Build stage for Vite assets ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY . .
RUN npm ci && npm run build

# ---- Final image with Nginx + PHP-FPM ----
FROM php:8.2-fpm-alpine

# Install Nginx and required system packages
RUN apk add --no-cache \
    nginx \
    sqlite sqlite-dev \
    libzip-dev oniguruma-dev \
    && docker-php-ext-install -j$(nproc) pdo_sqlite mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# ---- Create required directories and set permissions ----
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/framework/testing \
    && mkdir -p bootstrap/cache \
    && mkdir -p /var/data /var/run/nginx /var/log/nginx \
    && touch /var/data/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache /var/data /var/run/nginx /var/log/nginx \
    && chmod -R 775 storage bootstrap/cache /var/data

# ---- Set environment variables for build (only for Composer/Artisan commands) ----
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY=base64:abcdefghijklmnopqrstuvwxyz1234567890= \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/data/database.sqlite \
    CACHE_DRIVER=file \
    SESSION_DRIVER=file \
    QUEUE_CONNECTION=sync

# ---- Install PHP dependencies ----
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN php artisan package:discover --ansi

# ---- Copy built Vite assets ----
COPY --from=assets /app/public/build /var/www/html/public/build

# ---- Nginx configuration ----
RUN rm /etc/nginx/http.d/default.conf
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# ---- Expose port 80 ----
EXPOSE 80

# ---- Start both PHP-FPM and Nginx ----
CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"