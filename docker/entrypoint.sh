#!/bin/bash
set -e

echo "==> Starting GeoCam Deployment Setup..."

# Configure Apache to listen on Render's dynamic PORT (defaults to 80 if not set)
PORT=${PORT:-80}
echo "==> Configuring Apache to listen on port: $PORT"
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# If SQLite is used, ensure the file exists
if [ "$DB_CONNECTION" = "sqlite" ] && [ ! -f "$DB_DATABASE" ]; then
    echo "==> Initializing SQLite database file..."
    touch "${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    chown www-data:www-data "${DB_DATABASE:-/var/www/html/database/database.sqlite}"
fi

# Ensure storage link exists
echo "==> Linking storage directory..."
php artisan storage:link --force || true

# Production caches
echo "==> Caching configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "==> Running database migrations..."
php artisan migrate --force

# Optional: Run database seeders if explicitly requested via environment variable
if [ "$RUN_SEEDS" = "true" ]; then
    echo "==> Running database seeders..."
    php artisan db:seed --force
fi

echo "==> Starting Apache Server..."
exec apache2-foreground
