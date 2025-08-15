#!/bin/bash

# Fix Storage for Production Script
# This script fixes common storage issues after deployment

echo "🔧 Fixing storage issues for production..."

# Set environment to production
export APP_ENV=production

# 1. Remove existing storage link (if any)
echo "📂 Removing existing storage link..."
rm -f public/storage

# 2. Create fresh storage link
echo "🔗 Creating fresh storage link..."
php artisan storage:link --env=production

# 3. Set proper permissions
echo "🔐 Setting proper permissions..."
chmod -R 755 storage/
chmod -R 755 public/storage
chmod -R 775 storage/app/public/
chmod -R 775 storage/logs/
chmod -R 775 bootstrap/cache/

# 4. Create necessary directories
echo "📁 Creating necessary directories..."
mkdir -p storage/app/public/teachers
mkdir -p storage/app/public/articles
mkdir -p storage/app/public/services
mkdir -p storage/app/public/profile
mkdir -p storage/app/public/hero-sections
mkdir -p storage/app/public/hero-backgrounds
mkdir -p storage/app/public/livewire-tmp

# 5. Set permissions for created directories
chmod -R 775 storage/app/public/teachers
chmod -R 775 storage/app/public/articles
chmod -R 775 storage/app/public/services
chmod -R 775 storage/app/public/profile
chmod -R 775 storage/app/public/hero-sections
chmod -R 775 storage/app/public/hero-backgrounds
chmod -R 775 storage/app/public/livewire-tmp

# 6. Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear --env=production
php artisan cache:clear --env=production
php artisan view:clear --env=production
php artisan route:clear --env=production

# 7. Generate fresh config cache for production
echo "⚡ Generating production config cache..."
php artisan config:cache --env=production

# 8. Test storage link
echo "🧪 Testing storage link..."
if [ -L "public/storage" ] && [ -e "public/storage" ]; then
    echo "✅ Storage link created successfully!"
else
    echo "❌ Storage link creation failed!"
    exit 1
fi

# 9. Test file write permissions
echo "📝 Testing write permissions..."
TEST_FILE="storage/app/public/test_write.txt"
echo "test" > $TEST_FILE
if [ -f "$TEST_FILE" ]; then
    echo "✅ Write permissions working!"
    rm $TEST_FILE
else
    echo "❌ Write permissions failed!"
    exit 1
fi

echo "✅ Storage fix completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Test file upload in admin panel"
echo "2. Check if images display correctly"
echo "3. Monitor logs: tail -f storage/logs/laravel.log"
