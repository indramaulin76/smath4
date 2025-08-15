#!/bin/bash

# SMA Tunas Harapan - Complete Asset & Upload Fix
echo "🔧 Fixing JavaScript errors and upload issues..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=== COMPREHENSIVE FIX ===${NC}"

# 1. Clear all Laravel caches
echo -e "${YELLOW}🧹 Clearing Laravel caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
rm -rf bootstrap/cache/*.php

# 2. Clear Filament caches
echo -e "${YELLOW}🎨 Clearing Filament caches...${NC}"
php artisan filament:clear-cached-components
php artisan optimize:clear

# 3. Fix Composer issues
echo -e "${YELLOW}📦 Fixing Composer...${NC}"
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader
composer dump-autoload --optimize

# 4. Rebuild assets
echo -e "${YELLOW}🏗️ Rebuilding assets...${NC}"
if [ -f "package.json" ]; then
    npm install --production
    npm run build
fi

# 5. Fix storage structure
echo -e "${YELLOW}📁 Fixing storage structure...${NC}"
mkdir -p storage/app/public/teachers
mkdir -p storage/app/public/articles
mkdir -p storage/app/public/services
mkdir -p storage/app/public/profile

# 6. Remove problematic symlink and use direct copy
echo -e "${YELLOW}🔗 Using direct copy instead of symlink...${NC}"
rm -rf public/storage
mkdir -p public/storage
cp -r storage/app/public/* public/storage/ 2>/dev/null || true

# 7. Set proper permissions
echo -e "${YELLOW}🔐 Setting permissions...${NC}"
chown -R www-data:www-data storage/ 2>/dev/null || chown -R apache:apache storage/ 2>/dev/null || true
chown -R www-data:www-data public/storage/ 2>/dev/null || chown -R apache:apache public/storage/ 2>/dev/null || true
chmod -R 775 storage/app/public/
chmod -R 755 public/storage/

# 8. Test upload directory
echo -e "${YELLOW}🧪 Testing upload directories...${NC}"
echo "test upload" > storage/app/public/teachers/test-upload.txt
cp storage/app/public/teachers/test-upload.txt public/storage/teachers/test-upload.txt 2>/dev/null || true
chmod 644 storage/app/public/teachers/test-upload.txt
chmod 644 public/storage/teachers/test-upload.txt 2>/dev/null || true

# 9. Cache optimizations
echo -e "${YELLOW}🚀 Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Create sync script for uploads
echo -e "${YELLOW}📝 Creating upload sync script...${NC}"
cat > sync-uploads.sh << 'EOF'
#!/bin/bash
echo "Syncing uploads..."
rsync -av --delete storage/app/public/ public/storage/
chown -R www-data:www-data public/storage/ 2>/dev/null || chown -R apache:apache public/storage/ 2>/dev/null || true
chmod -R 755 public/storage/
echo "Upload sync completed!"
EOF
chmod +x sync-uploads.sh

# 11. Create cron job for automatic sync
echo -e "${YELLOW}⏰ Setting up auto-sync...${NC}"
cat > auto-sync-uploads.sh << 'EOF'
#!/bin/bash
# Run every 5 minutes to sync uploads
cd /var/www/smatunasharapan
rsync -av --delete storage/app/public/ public/storage/ > /dev/null 2>&1
chown -R www-data:www-data public/storage/ > /dev/null 2>&1
chmod -R 755 public/storage/ > /dev/null 2>&1
EOF
chmod +x auto-sync-uploads.sh

# 12. Show status
echo -e "${BLUE}=== STATUS CHECK ===${NC}"
echo "Teachers directory:"
ls -la storage/app/public/teachers/ 2>/dev/null || echo "Directory not found"
echo ""
echo "Public storage:"
ls -la public/storage/teachers/ 2>/dev/null || echo "Directory not found"
echo ""
echo "Test file access:"
if [ -f "public/storage/teachers/test-upload.txt" ]; then
    echo "✅ Test file accessible"
else
    echo "❌ Test file not accessible"
fi

# 13. Check for JavaScript assets
echo -e "${YELLOW}📄 Checking assets...${NC}"
if [ -d "public/build" ]; then
    echo "✅ Build directory exists"
    ls -la public/build/ | head -3
else
    echo "⚠️ Build directory missing - assets may not be compiled"
fi

echo -e "${GREEN}✅ Complete fix finished!${NC}"
echo -e "${YELLOW}📋 Next steps:${NC}"
echo "1. Clear browser cache and refresh admin page"
echo "2. Try uploading a teacher photo"
echo "3. If upload works but image doesn't show, run: ./sync-uploads.sh"
echo "4. For automatic sync, add to crontab: */5 * * * * /var/www/smatunasharapan/auto-sync-uploads.sh"
echo "5. Test access: https://yourdomain.com/storage/teachers/test-upload.txt"
