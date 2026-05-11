#!/bin/sh
set -e

cd /var/www

composer install
# Fix storage and cache permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Generate app key if not set
php artisan key:generate --force

# Run migrations and seed
php artisan migrate --force --seed

# Start PHP-FPM
exec php-fpm
