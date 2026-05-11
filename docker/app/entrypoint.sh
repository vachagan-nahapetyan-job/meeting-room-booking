#!/bin/sh
set -e

cd /var/www

mkdir -p bootstrap/cache
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist
fi

chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan key:generate --force

# Simple sleep - MySQL takes ~5-10 seconds to initialize
echo "Waiting 2 seconds for database to initialize..."
sleep 2

# This works whether migrations table exists or not
php artisan migrate --force --seed

exec php-fpm