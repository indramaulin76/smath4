#!/bin/bash

# SMA Tunas Harapan - Fix Autoload & Storage Issues
echo "🔧 Fixing critical autoload and storage issues..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=== CRITICAL FIXES ===${NC}"

# 1. Check and fix vendor directory
echo -e "${YELLOW}📦 Checking vendor directory...${NC}"
if [ ! -d "vendor" ]; then
    echo -e "${RED}❌ Vendor directory missing! Installing...${NC}"
    export COMPOSER_ALLOW_SUPERUSER=1
    composer install --no-dev --optimize-autoloader
else
    echo -e "${GREEN}✅ Vendor directory exists${NC}"
    echo -e "${YELLOW}Optimizing autoload...${NC}"
    export COMPOSER_ALLOW_SUPERUSER=1
    composer dump-autoload --optimize
fi

# 2. Check autoload file specifically
echo -e "${YELLOW}🔍 Checking autoload.php...${NC}"
if [ -f "vendor/autoload.php" ]; then
    echo -e "${GREEN}✅ autoload.php exists${NC}"
else
    echo -e "${RED}❌ autoload.php missing! Reinstalling composer...${NC}"
    rm -rf vendor/
    export COMPOSER_ALLOW_SUPERUSER=1
    composer install --no-dev --optimize-autoloader
fi

# 3. Fix bootstrap cache
echo -e "${YELLOW}🗂️ Fixing bootstrap cache...${NC}"
mkdir -p bootstrap/cache
chmod 775 bootstrap/cache
rm -f bootstrap/cache/*.php

# 4. Clear all Laravel caches
echo -e "${YELLOW}🧹 Clearing all caches...${NC}"
php artisan config:clear 2>/dev/null || echo "Config clear failed"
php artisan cache:clear 2>/dev/null || echo "Cache clear failed"
php artisan route:clear 2>/dev/null || echo "Route clear failed"
php artisan view:clear 2>/dev/null || echo "View clear failed"

# 5. Fix storage structure completely
echo -e "${YELLOW}📁 Rebuilding storage structure...${NC}"
mkdir -p storage/app/public/teachers
mkdir -p storage/app/public/articles
mkdir -p storage/app/public/services
mkdir -p storage/app/public/profile

# 6. Remove any existing storage symlinks/directories
echo -e "${YELLOW}🔗 Fixing storage access...${NC}"
rm -rf public/storage

# Method 1: Try symlink first
ln -sf "$(pwd)/storage/app/public" "$(pwd)/public/storage"

# Method 2: If symlink fails, use direct copy
if [ ! -e "public/storage" ]; then
    echo -e "${YELLOW}Symlink failed, using copy method...${NC}"
    cp -r storage/app/public public/storage
fi

# 7. Set proper permissions
echo -e "${YELLOW}🔐 Setting correct permissions...${NC}"
chown -R www-data:www-data storage/ 2>/dev/null || chown -R apache:apache storage/ 2>/dev/null || true
chown -R www-data:www-data public/storage/ 2>/dev/null || chown -R apache:apache public/storage/ 2>/dev/null || true

chmod -R 755 storage/
chmod -R 755 public/storage/
chmod -R 775 storage/app/public/

# 8. Create .htaccess for storage directory to prevent directory listing
echo -e "${YELLOW}🛡️ Creating .htaccess for security...${NC}"
cat > public/storage/.htaccess << 'EOF'
Options -Indexes
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^(.*)$ $1 [L]
    RewriteRule ^(.*)$ /404 [R=404,L]
</IfModule>
EOF

# 9. Create .htaccess for each subdirectory
for dir in teachers articles services profile; do
    if [ -d "public/storage/$dir" ]; then
        cat > "public/storage/$dir/.htaccess" << 'EOF'
Options -Indexes
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^(.*)$ $1 [L]
</IfModule>
EOF
    fi
done

# 10. Test file access
echo -e "${YELLOW}🧪 Creating test files...${NC}"
echo "test content" > storage/app/public/teachers/test-access.txt
cp storage/app/public/teachers/test-access.txt public/storage/teachers/test-access.txt 2>/dev/null || true
chmod 644 storage/app/public/teachers/test-access.txt
chmod 644 public/storage/teachers/test-access.txt 2>/dev/null || true

# 11. Check PHP-FPM permissions
echo -e "${YELLOW}👤 Checking PHP-FPM user...${NC}"
PHP_USER=$(ps aux | grep php-fpm | grep -v grep | head -1 | awk '{print $1}')
if [ ! -z "$PHP_USER" ]; then
    echo "PHP-FPM running as: $PHP_USER"
    chown -R $PHP_USER:$PHP_USER storage/app/public/ 2>/dev/null || true
fi

# 12. Create nginx config snippet for storage
echo -e "${YELLOW}⚙️ Creating nginx config snippet...${NC}"
cat > nginx-storage-config.txt << 'EOF'
# Add this to your nginx server block
location /storage/ {
    alias /var/www/smatunasharapan/public/storage/;
    expires 1y;
    add_header Cache-Control "public, immutable";
    
    # Security headers
    add_header X-Content-Type-Options nosniff;
    
    # Prevent access to directories
    location ~ /storage/.*(/|\.htaccess)$ {
        deny all;
        return 404;
    }
    
    # Allow specific file types only
    location ~* \.(jpg|jpeg|png|gif|ico|svg|webp|pdf|txt)$ {
        try_files $uri =404;
    }
}
EOF

# 13. Final status check
echo -e "${BLUE}=== FINAL STATUS ===${NC}"
echo "Vendor directory: $([ -d "vendor" ] && echo "✅ EXISTS" || echo "❌ MISSING")"
echo "Autoload file: $([ -f "vendor/autoload.php" ] && echo "✅ EXISTS" || echo "❌ MISSING")"
echo "Storage symlink: $([ -L "public/storage" ] && echo "✅ SYMLINK" || [ -d "public/storage" ] && echo "✅ DIRECTORY" || echo "❌ MISSING")"
echo "Teachers dir: $([ -d "public/storage/teachers" ] && echo "✅ EXISTS" || echo "❌ MISSING")"
echo "Test file: $([ -f "public/storage/teachers/test-access.txt" ] && echo "✅ EXISTS" || echo "❌ MISSING")"

# 14. Show directory contents
echo -e "\n${YELLOW}📄 Storage contents:${NC}"
ls -la public/storage/ 2>/dev/null || echo "Storage directory not accessible"
echo -e "\n${YELLOW}📄 Teachers directory:${NC}"
ls -la public/storage/teachers/ 2>/dev/null || echo "Teachers directory not accessible"

# 15. Create maintenance script
cat > maintain-storage.sh << 'EOF'
#!/bin/bash
# Run this daily to maintain storage
echo "Maintaining storage..."
rsync -av --delete storage/app/public/ public/storage/
chown -R www-data:www-data public/storage/ 2>/dev/null || chown -R apache:apache public/storage/ 2>/dev/null
chmod -R 755 public/storage/
find public/storage -name "*.jpg" -o -name "*.png" -o -name "*.jpeg" -o -name "*.gif" | xargs chmod 644
echo "Storage maintenance completed!"
EOF
chmod +x maintain-storage.sh

echo -e "${GREEN}✅ Critical fixes completed!${NC}"
echo -e "${YELLOW}📋 Next steps:${NC}"
echo "1. Restart web server: systemctl restart nginx && systemctl restart php8.2-fpm"
echo "2. Test autoload: https://yourdomain.com (should not show autoload error)"
echo "3. Test storage: https://yourdomain.com/storage/teachers/test-access.txt"
echo "4. Upload a teacher photo in admin panel"
echo "5. Add nginx config from nginx-storage-config.txt to your server block"
echo "6. Run ./maintain-storage.sh daily for storage maintenance"
