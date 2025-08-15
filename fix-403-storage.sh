#!/bin/bash

# SMA Tunas Harapan - Fix 403 Storage Permission Script
echo "🔧 Fixing 403 Forbidden error for storage files..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get web server user
WEB_USER=""
if id "www-data" &>/dev/null; then
    WEB_USER="www-data"
elif id "apache" &>/dev/null; then
    WEB_USER="apache"
elif id "nginx" &>/dev/null; then
    WEB_USER="nginx"
elif id "httpd" &>/dev/null; then
    WEB_USER="httpd"
else
    echo -e "${YELLOW}⚠️ Web server user not found, using current user...${NC}"
    WEB_USER=$(whoami)
fi

echo -e "${BLUE}🌐 Detected web server user: ${WEB_USER}${NC}"

echo -e "${YELLOW}📍 Current directory: $(pwd)${NC}"

# 1. Remove and recreate storage link
echo -e "${YELLOW}🔗 Recreating storage symlink...${NC}"
rm -rf public/storage
php artisan storage:link

# 2. Create all necessary directories
echo -e "${YELLOW}📁 Creating storage directories...${NC}"
mkdir -p storage/app/public/teachers
mkdir -p storage/app/public/articles  
mkdir -p storage/app/public/services
mkdir -p storage/app/public/profile
mkdir -p storage/app/public/hero-sections
mkdir -p storage/app/public/hero-backgrounds
mkdir -p storage/app/public/livewire-tmp

# 3. Set ownership recursively
echo -e "${YELLOW}👤 Setting ownership to ${WEB_USER}...${NC}"
chown -R ${WEB_USER}:${WEB_USER} storage/
chown -R ${WEB_USER}:${WEB_USER} public/storage 2>/dev/null || true

# 4. Set proper permissions
echo -e "${YELLOW}🔐 Setting permissions...${NC}"

# Storage directory permissions
chmod -R 755 storage/
chmod -R 775 storage/app/
chmod -R 775 storage/app/public/
chmod -R 775 storage/framework/
chmod -R 775 storage/logs/

# Specific directory permissions
chmod 775 storage/app/public/teachers/
chmod 775 storage/app/public/articles/
chmod 775 storage/app/public/services/
chmod 775 storage/app/public/profile/
chmod 775 storage/app/public/hero-sections/
chmod 775 storage/app/public/hero-backgrounds/

# Set file permissions for uploaded files
find storage/app/public/ -type f -exec chmod 644 {} \;

# 5. Set SELinux context if exists (for CentOS/RHEL)
if command -v setsebool &> /dev/null; then
    echo -e "${YELLOW}🛡️ Setting SELinux permissions...${NC}"
    setsebool -P httpd_can_network_connect 1 2>/dev/null || true
    chcon -R -t httpd_exec_t storage/app/public/ 2>/dev/null || true
fi

# 6. Check if files exist
echo -e "${YELLOW}📄 Checking uploaded files:${NC}"
if [ -d "storage/app/public/teachers" ]; then
    echo "Files in teachers directory:"
    ls -la storage/app/public/teachers/
    
    # Set individual file permissions
    for file in storage/app/public/teachers/*; do
        if [ -f "$file" ]; then
            chmod 644 "$file"
            chown ${WEB_USER}:${WEB_USER} "$file"
            echo "Fixed: $file"
        fi
    done
else
    echo "❌ Teachers directory not found"
fi

# 7. Test symlink
echo -e "${YELLOW}🧪 Testing symlink...${NC}"
if [ -L "public/storage" ]; then
    target=$(readlink public/storage)
    echo "Symlink points to: $target"
    if [ -d "$target" ]; then
        echo "✅ Symlink target exists"
        ls -la "$target" | head -3
    else
        echo "❌ Symlink target does not exist"
        # Recreate with absolute path
        rm public/storage
        ln -s "$(pwd)/storage/app/public" public/storage
        echo "Created absolute symlink"
    fi
else
    echo "❌ Symlink does not exist"
fi

# 8. Create test file and check access
echo -e "${YELLOW}🧪 Testing file access...${NC}"
echo "Test content" > storage/app/public/test-access.txt
chmod 644 storage/app/public/test-access.txt
chown ${WEB_USER}:${WEB_USER} storage/app/public/test-access.txt

if [ -f "public/storage/test-access.txt" ]; then
    echo "✅ Test file accessible via symlink"
    echo "File permissions: $(ls -la public/storage/test-access.txt)"
else
    echo "❌ Test file NOT accessible via symlink"
fi

# Clean up test file
rm -f storage/app/public/test-access.txt

# 9. Show final permissions
echo -e "${BLUE}=== FINAL PERMISSIONS ===${NC}"
echo "storage/ directory:"
ls -la storage/ | head -5

echo -e "\nstorage/app/public/ directory:"
ls -la storage/app/public/ | head -5

echo -e "\npublic/storage symlink:"
ls -la public/ | grep storage

# 10. Web server configuration check
echo -e "${YELLOW}⚙️ Web server check:${NC}"
if pgrep apache2 > /dev/null; then
    echo "✅ Apache2 is running"
    echo "Apache user: $(ps aux | grep apache2 | grep -v grep | head -1 | awk '{print $1}')"
elif pgrep nginx > /dev/null; then
    echo "✅ Nginx is running"  
    echo "Nginx user: $(ps aux | grep nginx | grep -v grep | head -1 | awk '{print $1}')"
else
    echo "⚠️ Web server status unknown"
fi

echo -e "${GREEN}✅ Permission fix completed!${NC}"
echo -e "${BLUE}📝 Next steps:${NC}"
echo "1. Try accessing: https://yourdomain.com/storage/test-access.txt"
echo "2. Upload a new teacher photo and test"
echo "3. Check web server error logs if still failing"
echo "4. Restart web server: systemctl restart apache2 (or nginx)"
