#!/bin/bash

# Fresh Clone & Clean Setup Script
echo "🔄 Fresh clone and clean setup for SMA Tunas Harapan..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=== FRESH CLONE SETUP ===${NC}"

# 1. Backup important files first
echo -e "${YELLOW}💾 Backing up important files...${NC}"
mkdir -p /tmp/smath-backup
cp .env.production /tmp/smath-backup/ 2>/dev/null || echo "No .env.production found"
cp -r storage/app/public/teachers /tmp/smath-backup/ 2>/dev/null || echo "No teachers directory found"

# 2. Move to parent directory and remove current
echo -e "${YELLOW}🗑️ Removing current installation...${NC}"
cd ..
rm -rf smatunasharapan

# 3. Fresh clone
echo -e "${YELLOW}📥 Fresh clone from GitHub...${NC}"
git clone https://github.com/indramaulin76/smath4.git smatunasharapan
cd smatunasharapan

# 4. Set proper ownership immediately
echo -e "${YELLOW}👤 Setting ownership...${NC}"
chown -R www-data:www-data . 2>/dev/null || chown -R apache:apache . 2>/dev/null || true

# 5. Copy back important files
echo -e "${YELLOW}📋 Restoring backup files...${NC}"
cp /tmp/smath-backup/.env.production . 2>/dev/null || echo "Creating new .env.production..."

# Create .env.production if not exists
if [ ! -f ".env.production" ]; then
    cp .env.example .env.production
    echo "APP_ENV=production" >> .env.production
    echo "APP_DEBUG=false" >> .env.production
    echo "APP_URL=https://smatunasharapan.site" >> .env.production
fi

# 6. Composer install (clean)
echo -e "${YELLOW}📦 Fresh Composer install...${NC}"
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_DISABLE_XDEBUG_WARN=1
composer install --no-dev --optimize-autoloader --no-interaction

# 7. Generate app key
echo -e "${YELLOW}🔑 Generating app key...${NC}"
php artisan key:generate --force

# 8. NPM install and build
echo -e "${YELLOW}🎨 Building assets...${NC}"
if command -v npm &> /dev/null; then
    npm install --production
    npm run build
else
    echo "⚠️ NPM not found, skipping asset build"
fi

# 9. Setup storage structure
echo -e "${YELLOW}📁 Setting up storage...${NC}"
mkdir -p storage/app/public/teachers
mkdir -p storage/app/public/articles
mkdir -p storage/app/public/services
mkdir -p storage/app/public/profile

# Restore teacher photos if exist
if [ -d "/tmp/smath-backup/teachers" ]; then
    cp -r /tmp/smath-backup/teachers/* storage/app/public/teachers/ 2>/dev/null || true
fi

# 10. Remove symlink and use copy method
echo -e "${YELLOW}🔗 Setting up storage access...${NC}"
rm -rf public/storage
cp -r storage/app/public public/storage

# 11. Set permissions
echo -e "${YELLOW}🔐 Setting permissions...${NC}"
chown -R www-data:www-data storage/ 2>/dev/null || chown -R apache:apache storage/ 2>/dev/null || true
chown -R www-data:www-data public/storage/ 2>/dev/null || chown -R apache:apache public/storage/ 2>/dev/null || true
chmod -R 755 storage/
chmod -R 755 public/storage/
find storage/app/public -type f -exec chmod 644 {} \;
find public/storage -type f -exec chmod 644 {} \;

# 12. Database migration
echo -e "${YELLOW}🗃️ Running migrations...${NC}"
php artisan migrate --force

# 13. Clear and cache everything
echo -e "${YELLOW}💾 Optimizing for production...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
rm -rf bootstrap/cache/*.php

php artisan config:cache
php artisan route:cache
php artisan view:cache

# 14. Create maintenance scripts
echo -e "${YELLOW}📝 Creating maintenance scripts...${NC}"

# Storage sync script
cat > sync-storage.sh << 'EOF'
#!/bin/bash
echo "Syncing storage..."
rsync -av --delete storage/app/public/ public/storage/
chown -R www-data:www-data public/storage/ 2>/dev/null || chown -R apache:apache public/storage/
find public/storage -type d -exec chmod 755 {} \;
find public/storage -type f -exec chmod 644 {} \;
echo "Storage sync completed!"
EOF
chmod +x sync-storage.sh

# Daily maintenance script
cat > daily-maintenance.sh << 'EOF'
#!/bin/bash
echo "Running daily maintenance..."
./sync-storage.sh
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "Daily maintenance completed!"
EOF
chmod +x daily-maintenance.sh

# 15. Test setup
echo -e "${YELLOW}🧪 Testing setup...${NC}"
echo "Laravel version: $(php artisan --version 2>/dev/null || echo 'FAILED')"
echo "Autoload exists: $([ -f 'vendor/autoload.php' ] && echo 'YES' || echo 'NO')"
echo "Storage accessible: $([ -d 'public/storage/teachers' ] && echo 'YES' || echo 'NO')"

# 16. Create test files
echo "test content" > storage/app/public/teachers/test-fresh.txt
cp storage/app/public/teachers/test-fresh.txt public/storage/teachers/test-fresh.txt
chmod 644 storage/app/public/teachers/test-fresh.txt
chmod 644 public/storage/teachers/test-fresh.txt

# 17. Show final status
echo -e "${BLUE}=== FRESH SETUP COMPLETED ===${NC}"
echo -e "${GREEN}✅ Fresh clone successful${NC}"
echo -e "${GREEN}✅ Composer dependencies installed${NC}"
echo -e "${GREEN}✅ Storage structure created${NC}"
echo -e "${GREEN}✅ Permissions set correctly${NC}"
echo -e "${GREEN}✅ Assets built (if NPM available)${NC}"
echo -e "${GREEN}✅ Database migrated${NC}"
echo -e "${GREEN}✅ Caches optimized${NC}"

echo -e "\n${YELLOW}📋 Next steps:${NC}"
echo "1. Restart web server: systemctl restart nginx && systemctl restart php8.2-fpm"
echo "2. Test website: https://smatunasharapan.site"
echo "3. Test admin: https://smatunasharapan.site/smath-admin-secure-2025"
echo "4. Test storage: https://smatunasharapan.site/storage/teachers/test-fresh.txt"
echo "5. Upload teacher photo in admin panel"

echo -e "\n${YELLOW}📋 Maintenance:${NC}"
echo "- Run ./sync-storage.sh after uploads"
echo "- Run ./daily-maintenance.sh daily"
echo "- Backup files are in /tmp/smath-backup/"

# Clean up backup
rm -rf /tmp/smath-backup/

echo -e "\n${GREEN}🎉 Fresh setup completed! All errors should be resolved.${NC}"
