#!/bin/bash

# Exit on any error
set -e

echo "Starting Maraba Hospital Application..."

# Clear all caches
php artisan config:clear
php artisan route:cache
php artisan view:cache
php artisan cache:clear

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
