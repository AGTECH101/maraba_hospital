#!/bin/bash
set -e

echo "==> Booting Laravel container"

# Render injects PORT dynamically. Default to 8080 for local docker runs.
export PORT="${PORT:-8080}"
echo "==> Configuring nginx to listen on port $PORT"
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

cd /var/www/html

# Ensure .env exists (Render should inject real env vars via dashboard,
# but Laravel still needs a .env file present for some bootstrapping).
if [ ! -f .env ]; then
    echo "==> No .env found, copying .env.example"
    cp .env.example .env || true
fi

# Generate app key only if it's not already set
if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
    echo "==> Generating APP_KEY"
    php artisan key:generate --force || true
fi

# If using SQLite, make sure the database file exists
# (on Render, mount a persistent disk at this path or data will not survive restarts/redeploys)
if [ "$DB_CONNECTION" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$DB_PATH" ]; then
        echo "==> Creating SQLite database file at $DB_PATH"
        mkdir -p "$(dirname "$DB_PATH")"
        touch "$DB_PATH"
        chown www-data:www-data "$DB_PATH"
    fi
fi

echo "==> Linking storage"
php artisan storage:link || true

echo "==> Caching config/routes/views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running migrations"
php artisan migrate --force || echo "==> Migration failed or already up to date, continuing"

echo "==> Starting nginx + php-fpm via supervisord"
exec supervisord -c /etc/supervisor/supervisord.conf
