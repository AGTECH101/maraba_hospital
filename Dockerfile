FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zlib1g-dev \
    libjpeg-dev \
    libfreetype6-dev \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif gd intl bcmath xml

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev || true \
    && npm install \
    && npm run build \
    && mkdir -p /var/data \
    && touch /var/data/database.sqlite \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/data || true

ENV PORT=8000
EXPOSE 8000

CMD sh -c "mkdir -p /var/www/html/storage/framework/views /var/www/html/storage/framework/cache /var/www/html/bootstrap/cache /var/www/html/resources/views && php artisan config:clear || true && php artisan route:clear || true && php artisan view:clear || true && php artisan migrate --force && php artisan storage:link || true && php artisan serve --host 0.0.0.0 --port ${PORT}"
