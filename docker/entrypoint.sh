#!/bin/bash

# Exit on any error
set -e

echo "Starting Maraba Hospital Application..."

mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache

# Clear all caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan cache:clear || true

# Run database migrations
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

# Run database seeders if needed
if [ "$RUN_SEEDERS" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force
fi

echo "Application ready. Starting PHP-FPM..."

# Start PHP-FPM in the foreground
exec php-fpm
