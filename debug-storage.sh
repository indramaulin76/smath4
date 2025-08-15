#!/bin/bash

# SMA Tunas Harapan - Storage Debug & Fix Script
echo "🔍 Debugging and fixing storage issues..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== STORAGE DEBUGGING INFORMATION ===${NC}"

# 1. Check current directory
echo -e "${YELLOW}📍 Current directory:${NC}"
pwd

# 2. Check if storage directories exist
echo -e "${YELLOW}📁 Checking storage directories:${NC}"
echo "storage/app/public exists: $([ -d "storage/app/public" ] && echo "✅ YES" || echo "❌ NO")"
echo "storage/app/public/teachers exists: $([ -d "storage/app/public/teachers" ] && echo "✅ YES" || echo "❌ NO")"
echo "public/storage exists: $([ -L "public/storage" ] && echo "✅ YES (symlink)" || echo "❌ NO")"

# 3. Check symlink target
echo -e "${YELLOW}🔗 Checking storage symlink:${NC}"
if [ -L "public/storage" ]; then
    echo "Symlink target: $(readlink public/storage)"
    echo "Symlink target exists: $([ -e "public/storage" ] && echo "✅ YES" || echo "❌ NO")"
else
    echo "❌ public/storage symlink does not exist"
fi

# 4. Check file permissions
echo -e "${YELLOW}🔐 Checking permissions:${NC}"
ls -la storage/ | head -5
echo ""
ls -la storage/app/ | head -5
echo ""
if [ -d "storage/app/public" ]; then
    echo "storage/app/public permissions:"
    ls -la storage/app/public/ | head -5
fi
echo ""
if [ -d "storage/app/public/teachers" ]; then
    echo "Teachers directory content:"
    ls -la storage/app/public/teachers/ | head -10
fi

# 5. Check web server user
echo -e "${YELLOW}👤 Web server information:${NC}"
echo "Current user: $(whoami)"
echo "Web server process: $(ps aux | grep -E "(apache|nginx|httpd)" | grep -v grep | head -2 || echo 'Not found')"

echo -e "${BLUE}=== FIXING STORAGE ISSUES ===${NC}"

# 6. Recreate storage directories
echo -e "${YELLOW}📁 Creating storage directories:${NC}"
mkdir -p storage/app/public/teachers
mkdir -p storage/app/public/articles  
mkdir -p storage/app/public/services
mkdir -p storage/app/public/profile
mkdir -p storage/app/public/hero-sections
mkdir -p storage/app/public/hero-backgrounds
mkdir -p storage/app/public/livewire-tmp

# 7. Fix storage link
echo -e "${YELLOW}🔗 Fixing storage link:${NC}"
rm -f public/storage
php artisan storage:link

# 8. Set proper permissions
echo -e "${YELLOW}🔐 Setting proper permissions:${NC}"
# For the storage directory
chmod -R 775 storage/
# For the public storage symlink
chmod -R 775 storage/app/public/

# Set ownership to web server user (common users: www-data, apache, nginx)
echo -e "${YELLOW}👤 Setting ownership:${NC}"
if command -v apache2 &> /dev/null; then
    chown -R www-data:www-data storage/app/public/ 2>/dev/null || echo "Could not set www-data ownership"
elif command -v nginx &> /dev/null; then
    chown -R nginx:nginx storage/app/public/ 2>/dev/null || echo "Could not set nginx ownership"
else
    echo "Web server user not detected, skipping ownership change"
fi

# 9. Test file creation
echo -e "${YELLOW}🧪 Testing file creation:${NC}"
echo "Test file content" > storage/app/public/test-write.txt
if [ -f "storage/app/public/test-write.txt" ]; then
    echo "✅ File creation successful"
    if [ -f "public/storage/test-write.txt" ]; then
        echo "✅ File accessible via symlink"
    else
        echo "❌ File NOT accessible via symlink"
    fi
    rm storage/app/public/test-write.txt
else
    echo "❌ File creation failed"
fi

# 10. Show final status
echo -e "${BLUE}=== FINAL STATUS ===${NC}"
echo "Storage link: $([ -L "public/storage" ] && echo "✅ EXISTS" || echo "❌ MISSING")"
echo "Teachers directory: $([ -d "storage/app/public/teachers" ] && echo "✅ EXISTS" || echo "❌ MISSING")"
echo "Symlink working: $([ -e "public/storage" ] && echo "✅ YES" || echo "❌ NO")"

# 11. Show uploaded files
echo -e "${YELLOW}📄 Uploaded teacher photos:${NC}"
if [ -d "storage/app/public/teachers" ]; then
    ls -la storage/app/public/teachers/
else
    echo "Teachers directory not found"
fi

echo -e "${GREEN}✅ Storage debugging and fix completed!${NC}"
echo -e "${YELLOW}💡 If images still don't show:${NC}"
echo "   1. Check web server configuration"
echo "   2. Verify .htaccess rules"
echo "   3. Check file permissions on server"
echo "   4. Try accessing direct URL: yoursite.com/storage/teachers/filename.jpg"
