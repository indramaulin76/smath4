#!/bin/bash

# Fix Upload & Autoload Issues
echo "🔧 Fixing upload and autoload issues..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=== FIXING PRODUCTION ISSUES ===${NC}"

# 1. Fix autoload issue first
echo -e "${YELLOW}📦 Fixing Composer autoload...${NC}"
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader
composer dump-autoload --optimize

# 2. Clear all caches to ensure clean state
echo -e "${YELLOW}🧹 Clearing all caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
rm -rf bootstrap/cache/*.php

# 3. Recreate storage structure
echo -e "${YELLOW}📁 Setting up storage structure...${NC}"
mkdir -p storage/app/public/teachers
mkdir -p storage/app/public/articles
mkdir -p storage/app/public/services
mkdir -p storage/app/public/profile
mkdir -p storage/app/public/hero-sections

# 4. Fix permissions again
echo -e "${YELLOW}🔐 Setting proper permissions...${NC}"
chown -R www-data:www-data storage/
chmod -R 775 storage/app/public/

# 5. Fix symlink with copy method (more reliable)
echo -e "${YELLOW}🔗 Using copy method instead of symlink...${NC}"
rm -rf public/storage
cp -r storage/app/public public/storage
chown -R www-data:www-data public/storage/
chmod -R 755 public/storage/

# 6. Test file upload manually
echo -e "${YELLOW}🧪 Creating test upload structure...${NC}"
echo "test teacher photo" > storage/app/public/teachers/test-teacher.txt
echo "test teacher photo" > public/storage/teachers/test-teacher.txt
chown www-data:www-data storage/app/public/teachers/test-teacher.txt
chown www-data:www-data public/storage/teachers/test-teacher.txt
chmod 644 storage/app/public/teachers/test-teacher.txt
chmod 644 public/storage/teachers/test-teacher.txt

# 7. Optimize for production
echo -e "${YELLOW}🚀 Production optimization...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Check teacher data
echo -e "${YELLOW}📊 Checking teacher data...${NC}"
php artisan tinker --execute="
echo 'Teacher count: ' . App\Models\Teacher::count() . PHP_EOL;
\$teachers = App\Models\Teacher::all();
foreach(\$teachers as \$teacher) {
    echo 'Teacher: ' . \$teacher->name . ' - Photo: ' . (\$teacher->photo ?? 'NO PHOTO') . PHP_EOL;
}
"

# 9. Show storage status
echo -e "${BLUE}=== STORAGE STATUS ===${NC}"
echo "Storage app/public/teachers:"
ls -la storage/app/public/teachers/
echo ""
echo "Public storage/teachers:"
ls -la public/storage/teachers/
echo ""
echo "Storage directory permissions:"
ls -la storage/app/ | grep public

# 10. Create sync script for future uploads
echo -e "${YELLOW}📝 Creating sync script...${NC}"
cat > sync-storage.sh << 'EOF'
#!/bin/bash
# Sync storage files (run after upload issues)
echo "Syncing storage files..."
rsync -av storage/app/public/ public/storage/
chown -R www-data:www-data public/storage/
chmod -R 755 public/storage/
echo "Storage sync completed!"
EOF
chmod +x sync-storage.sh

echo -e "${GREEN}✅ All fixes completed!${NC}"
echo -e "${YELLOW}📋 Next steps:${NC}"
echo "1. Try uploading a new teacher photo"
echo "2. If upload doesn't work, run: ./sync-storage.sh"
echo "3. Test access: https://yourdomain.com/storage/teachers/test-teacher.txt"
echo "4. Check error logs if issues persist"
