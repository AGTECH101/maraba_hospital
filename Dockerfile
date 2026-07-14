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

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# ---- Create required directories and set permissions ----
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/framework/testing \
    && mkdir -p bootstrap/cache \
    && mkdir -p /var/data /var/run/nginx /var/log/nginx \
    && touch /var/data/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache /var/data /var/run/nginx /var/log/nginx \
    && chmod -R 775 storage bootstrap/cache /var/data

# ---- Set environment variables for build ----
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY=base64:abcdefghijklmnopqrstuvwxyz1234567890= \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/data/database.sqlite \
    CACHE_DRIVER=file \
    SESSION_DRIVER=file \
    QUEUE_CONNECTION=sync

# ---- Install PHP dependencies (skip scripts) ----
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# ---- Run package discover ----
RUN php artisan package:discover --ansi

# ---- Copy built Vite assets ----
COPY --from=assets /app/public/build /var/www/html/public/build

# ---- Laravel optimizations ----
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# ---- Nginx configuration ----
# Remove default Nginx config and add our own
RUN rm /etc/nginx/http.d/default.conf
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# ---- Create startup script to run both PHP-FPM and Nginx ----
RUN echo '#!/bin/sh\n\
php-fpm -D\n\
nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

# Expose port 80 for HTTP
EXPOSE 80

# Start both services
CMD ["/start.sh"]