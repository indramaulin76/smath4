#!/bin/sh

echo "🚀 Starting SMA Tunas Harapan Application..."

# Wait for database to be ready
echo "⏳ Waiting for database..."
while ! mysqladmin ping -h"$DB_HOST" --silent; do
    echo "Database is unavailable - sleeping"
    sleep 2
done
echo "✅ Database is ready!"

# Copy environment file if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "📄 Creating .env file..."
    cp /var/www/html/.env.production.example /var/www/html/.env
fi

# Generate app key if not set
if ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Run database migrations
echo "🗃️  Running database migrations..."
php artisan migrate --force

# Create storage symlink
echo "🔗 Creating storage symlink..."
php artisan storage:link --force

# Seed database (only if empty)
echo "🌱 Seeding database..."
php artisan db:seed --force

# Cache configurations for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

echo "✅ Application ready!"

# Start supervisor (which manages nginx and php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf