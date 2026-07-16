# =========================================================
# Stage 1: Install PHP (Composer) dependencies
# docker build --build-arg VITE_API_URL=https://marabahospital.onrender.com -t test-build .
# =========================================================
FROM composer:2 AS vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# =========================================================
# Stage 2: Build frontend assets (Vite / Tailwind / etc.)
# =========================================================
FROM node:20-alpine AS frontend
WORKDIR /app

# Vite inlines VITE_* vars at BUILD time, not runtime, and .env is
# deliberately excluded from the build context — so pass them in
# explicitly as build args (set these in Render's build settings
# or via --build-arg when building locally).
ARG VITE_APP_NAME
ARG VITE_API_URL
ENV VITE_APP_NAME=$VITE_APP_NAME
ENV VITE_API_URL=$VITE_API_URL

COPY package*.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# Fail the build loudly here instead of deploying a broken app silently
RUN test -f public/build/manifest.json || test -f public/build/.vite/manifest.json \
    || (echo "ERROR: Vite manifest.json not found — build produced no output" && exit 1)

# =========================================================
# Stage 3: Final runtime image (PHP-FPM + Nginx + Supervisor)
# =========================================================
FROM php:8.2-fpm-alpine

# System deps + PHP extensions Laravel commonly needs
RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        curl \
        gettext \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        sqlite-dev \
        icu-dev \
        zip \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        opcache

WORKDIR /var/www/html

# Bring in vendor/ from stage 1 and built assets from stage 2
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# App source
COPY . .

# Docker configs
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Render injects PORT at runtime; this is just documentation
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
