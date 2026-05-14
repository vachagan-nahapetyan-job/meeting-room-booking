#!/bin/sh
set -e

cd /var/www

# Create necessary directories
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/api-docs

# Copy .env if it doesn't exist (but you already have it)
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Install composer dependencies
if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist
fi

# Install Swagger if not present
if [ ! -d "vendor/darkaonline/l5-swagger" ]; then
    echo "📚 Installing Swagger..."
    composer require "darkaonline/l5-swagger" --no-interaction
    php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider" --force
fi

# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Generate app key if not set
if ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY= .env | cut -d '=' -f2)" ]; then
    php artisan key:generate --force
fi

# Clear caches (don't exit on error)
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Wait for database to be ready
echo "Waiting for database to be ready..."
max_retries=30
counter=0
until php artisan db:show > /dev/null 2>&1; do
    counter=$((counter + 1))
    if [ $counter -gt $max_retries ]; then
        echo "❌ Database connection failed after $max_retries attempts"
        echo "Check your .env file: DB_HOST=db, DB_DATABASE=booking, DB_USERNAME=booking, DB_PASSWORD=secret"
        exit 1
    fi
    echo "⏳ Waiting for database... ($counter/$max_retries)"
    sleep 2
done

echo "✅ Database is ready!"

# Run migrations and seeders
echo "Running migrations..."
php artisan migrate --force --seed

# Generate Swagger documentation
if [ -d "vendor/darkaonline/l5-swagger" ]; then
    echo "📝 Generating Swagger documentation..."
    php artisan l5-swagger:generate
fi

# Final permission fix
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "✅ Setup complete! Starting PHP-FPM..."
exec php-fpm
echo "Setup complete! API is running at http://localhost:8000"
echo "Check Swagger at http://localhost:8000/api/documentation"