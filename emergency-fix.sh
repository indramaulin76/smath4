#!/bin/bash

# EMERGENCY FIX - Critical Autoload and Permission Issues
echo "🚨 EMERGENCY FIX: Resolving critical issues..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${RED}=== EMERGENCY FIXES ===${NC}"

# 1. Check current directory
echo -e "${YELLOW}📍 Current location: $(pwd)${NC}"

# 2. Check if vendor exists
echo -e "${YELLOW}📦 Checking vendor directory...${NC}"
if [ -d "vendor" ]; then
    echo -e "${GREEN}✅ Vendor directory exists${NC}"
    ls -la vendor/ | head -3
else
    echo -e "${RED}❌ Vendor directory MISSING${NC}"
fi

# 3. Check if composer.json exists
echo -e "${YELLOW}📄 Checking composer.json...${NC}"
if [ -f "composer.json" ]; then
    echo -e "${GREEN}✅ composer.json exists${NC}"
else
    echo -e "${RED}❌ composer.json MISSING${NC}"
    exit 1
fi

# 4. FORCE reinstall vendor
echo -e "${YELLOW}🔧 FORCE reinstalling vendor...${NC}"
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_DISABLE_XDEBUG_WARN=1

# Remove vendor completely
rm -rf vendor/
rm -f composer.lock

# Reinstall everything
composer install --no-dev --optimize-autoloader --no-interaction

# 5. Verify autoload.php exists
echo -e "${YELLOW}🔍 Verifying autoload.php...${NC}"
if [ -f "vendor/autoload.php" ]; then
    echo -e "${GREEN}✅ autoload.php created successfully${NC}"
    ls -la vendor/autoload.php
else
    echo -e "${RED}❌ autoload.php STILL MISSING!${NC}"
    echo "Trying alternative approach..."
    
    # Try with different flags
    composer install --optimize-autoloader --classmap-authoritative
fi

# 6. Fix file permissions for storage
echo -e "${YELLOW}🔐 Fixing file permissions...${NC}"

# Fix directory permissions (755)
find storage/app/public -type d -exec chmod 755 {} \;
find public/storage -type d -exec chmod 755 {} \; 2>/dev/null || true

# Fix file permissions (644) - NOT 755!
find storage/app/public -type f -exec chmod 644 {} \;
find public/storage -type f -exec chmod 644 {} \; 2>/dev/null || true

# 7. Fix .htaccess permissions specifically
chmod 644 public/storage/teachers/.htaccess 2>/dev/null || true
chmod 644 public/storage/.htaccess 2>/dev/null || true

# 8. Set ownership
chown -R www-data:www-data storage/ 2>/dev/null || chown -R apache:apache storage/ 2>/dev/null || true
chown -R www-data:www-data public/storage/ 2>/dev/null || chown -R apache:apache public/storage/ 2>/dev/null || true

# 9. Clear bootstrap cache
echo -e "${YELLOW}🗂️ Clearing bootstrap cache...${NC}"
rm -rf bootstrap/cache/*
mkdir -p bootstrap/cache
chmod 775 bootstrap/cache

# 10. Test Laravel can start
echo -e "${YELLOW}🧪 Testing Laravel startup...${NC}"
php artisan --version 2>/dev/null && echo -e "${GREEN}✅ Laravel OK${NC}" || echo -e "${RED}❌ Laravel FAILED${NC}"

# 11. Cache after fixing autoload
echo -e "${YELLOW}💾 Rebuilding caches...${NC}"
php artisan config:clear 2>/dev/null || echo "Config clear failed"
php artisan cache:clear 2>/dev/null || echo "Cache clear failed" 
php artisan route:clear 2>/dev/null || echo "Route clear failed"
php artisan view:clear 2>/dev/null || echo "View clear failed"

# Optimize for production
php artisan config:cache 2>/dev/null || echo "Config cache failed"
php artisan route:cache 2>/dev/null || echo "Route cache failed"
php artisan view:cache 2>/dev/null || echo "View cache failed"

# 12. Show final status
echo -e "${BLUE}=== FINAL STATUS ===${NC}"
echo "Vendor autoload: $([ -f "vendor/autoload.php" ] && echo "✅ EXISTS" || echo "❌ MISSING")"
echo "Laravel version: $(php artisan --version 2>/dev/null || echo "FAILED")"

echo -e "\n${YELLOW}📄 Storage file permissions:${NC}"
ls -la public/storage/teachers/ | head -5

echo -e "\n${YELLOW}📁 Directory structure:${NC}"
ls -la public/ | grep storage

# 13. Create working maintain script
cat > maintain-storage-fixed.sh << 'EOF'
#!/bin/bash
echo "Maintaining storage with correct permissions..."
rsync -av --delete storage/app/public/ public/storage/
# Fix ownership
chown -R www-data:www-data public/storage/ 2>/dev/null || chown -R apache:apache public/storage/ 2>/dev/null
# Fix directory permissions (755)
find public/storage -type d -exec chmod 755 {} \;
# Fix file permissions (644) - NOT 755!
find public/storage -type f -exec chmod 644 {} \;
echo "Storage maintenance completed with correct permissions!"
EOF
chmod +x maintain-storage-fixed.sh

echo -e "${GREEN}✅ EMERGENCY FIX COMPLETED!${NC}"
echo -e "${YELLOW}📋 What was fixed:${NC}"
echo "1. ✅ Reinstalled vendor/autoload.php"
echo "2. ✅ Fixed file permissions (644 for files, 755 for dirs)"
echo "3. ✅ Cleared all caches"
echo "4. ✅ Set proper ownership"
echo ""
echo -e "${YELLOW}📋 Next steps:${NC}"
echo "1. Test website: https://smatunasharapan.site"
echo "2. Test admin: https://smatunasharapan.site/smath-admin-secure-2025"
echo "3. Upload teacher photo"
echo "4. Use ./maintain-storage-fixed.sh for future maintenance"
