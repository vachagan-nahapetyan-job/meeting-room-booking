#!/bin/sh
set -e

cd /var/www

# Create required Laravel directories
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Install dependencies if vendor missing
if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist
fi

# Permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Generate app key
php artisan key:generate --force

# Run migrations + seed
php artisan migrate --force --seed

# Start PHP-FPM
exec php-fpm