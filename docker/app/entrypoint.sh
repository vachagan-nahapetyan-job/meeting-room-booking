#!/bin/sh
set -e

cd /var/www

echo "🚀 Starting Laravel..."

# Ensure writable dirs (safe in bind mount)
mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chmod -R 775 storage bootstrap/cache || true

# Fix permissions ONLY inside container runtime (no host chown)
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Ensure .env exists
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Install dependencies
if [ ! -f vendor/autoload.php ]; then
    echo "📦 Installing dependencies..."
    composer install --no-interaction --prefer-dist
fi

# Generate app key
if ! grep -q "APP_KEY=base64" .env; then
    php artisan key:generate --force
fi

echo "⏳ Waiting for database..."
sleep 5

echo "✅ DB ready"

php artisan migrate --force --seed || true
php artisan optimize:clear || true
php artisan l5-swagger:generate || true

echo "✅ App ready"

exec php-fpm