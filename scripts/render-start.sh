#!/usr/bin/env bash
set -euo pipefail

mkdir -p storage/framework/views storage/framework/cache bootstrap/cache resources/views

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan optimize:clear || true
php artisan migrate --force
php artisan storage:link || true
php artisan serve --host 0.0.0.0 --port ${PORT:-8000}
