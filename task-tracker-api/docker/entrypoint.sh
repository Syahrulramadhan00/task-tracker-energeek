#!/bin/bash
set -e

cd /var/www/html

# Generate app key if not already set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Wait for PostgreSQL to be ready
echo "Waiting for database..."
until php artisan db:monitor --databases=pgsql 2>/dev/null; do
    sleep 2
done
echo "Database is ready."

# Run migrations and seeders
php artisan migrate --force
php artisan db:seed --force

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in the foreground
nginx -g "daemon off;"
