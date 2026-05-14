#!/bin/sh
set -e

cd /var/www

echo "🚀 Starting Laravel..."

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