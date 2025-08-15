#!/bin/bash

# SMA Tunas Harapan - Production Fix Script
# Mengatasi masalah deployment dan migration

echo "🔧 Fixing production deployment issues..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Set composer environment untuk menghindari root warning
echo -e "${YELLOW}📦 Setting up Composer environment...${NC}"
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_DISABLE_XDEBUG_WARN=1

# 2. Clear semua cache Laravel yang mungkin corrupt
echo -e "${YELLOW}🧹 Clearing all Laravel caches...${NC}"
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan event:clear || true

# 3. Clear composer cache
echo -e "${YELLOW}🗑️ Clearing Composer cache...${NC}"
composer clear-cache

# 4. Regenerate autoload tanpa dev dependencies
echo -e "${YELLOW}🔄 Regenerating autoload files...${NC}"
composer dump-autoload --no-dev --optimize

# 5. Manual package discovery tanpa cache
echo -e "${YELLOW}🔍 Running package discovery...${NC}"
php artisan package:discover --ansi || {
    echo -e "${RED}Package discovery failed, trying manual fix...${NC}"
    
    # Hapus cache bootstrap jika ada
    rm -f bootstrap/cache/packages.php
    rm -f bootstrap/cache/services.php
    
    # Coba lagi
    php artisan package:discover --ansi
}

# 6. Jalankan migration dengan force
echo -e "${YELLOW}🗃️ Running migrations...${NC}"
php artisan migrate --force

# 7. Recreate storage link
echo -e "${YELLOW}🔗 Setting up storage link...${NC}"
rm -f public/storage
php artisan storage:link

# 8. Set file permissions
echo -e "${YELLOW}🔐 Setting file permissions...${NC}"
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public

# 9. Cache optimizations untuk production
echo -e "${YELLOW}🚀 Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "${GREEN}✅ Production fix completed!${NC}"
echo -e "${GREEN}✅ Migration should now work properly${NC}"

# 10. Verify migration status
echo -e "${YELLOW}📊 Checking migration status...${NC}"
php artisan migrate:status

echo -e "${GREEN}🎉 All done! Try creating a teacher now.${NC}"
