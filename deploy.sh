#!/bin/bash

# SMA Tunas Harapan - Production Deployment Script
# Usage: ./deploy.sh

set -e

echo "🚀 Starting deployment for SMA Tunas Harapan..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: artisan file not found. Please run this script from the Laravel project root.${NC}"
    exit 1
fi

# 1. Environment Check
echo -e "${YELLOW}📋 Checking environment...${NC}"
if [ ! -f ".env.production" ]; then
    echo -e "${YELLOW}⚠️  .env.production not found. Creating from example...${NC}"
    cp .env.production.example .env.production
    echo -e "${RED}Please edit .env.production with your production settings before continuing!${NC}"
    exit 1
fi

# 2. Backup current state (if exists)
echo -e "${YELLOW}💾 Creating backup...${NC}"
if [ -d "storage/app/backup" ]; then
    rm -rf storage/app/backup/previous
    mv storage/app/backup/current storage/app/backup/previous 2>/dev/null || true
fi
mkdir -p storage/app/backup/current

# 3. Install dependencies
echo -e "${YELLOW}📦 Installing dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

# 4. NPM build for production
echo -e "${YELLOW}🎨 Building assets...${NC}"
npm ci
npm run build

# 5. Laravel optimizations
echo -e "${YELLOW}⚡ Optimizing Laravel...${NC}"

# Clear all caches first
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Generate application key if not exists
if ! grep -q "APP_KEY=" .env.production || [ -z "$(grep "APP_KEY=" .env.production | cut -d'=' -f2)" ]; then
    php artisan key:generate --force
fi

# Database operations
echo -e "${YELLOW}🗃️  Database operations...${NC}"
php artisan migrate --force

# Storage link
echo -e "${YELLOW}🔗 Creating storage link...${NC}"
# Remove existing link first
if [ -L "public/storage" ]; then
    rm public/storage
fi
php artisan storage:link

# Create necessary storage directories
mkdir -p storage/app/public/teachers
mkdir -p storage/app/public/articles  
mkdir -p storage/app/public/services
mkdir -p storage/app/public/profile
mkdir -p storage/app/public/hero-sections
mkdir -p storage/app/public/hero-backgrounds
mkdir -p storage/app/public/livewire-tmp

# Cache optimizations
echo -e "${YELLOW}🚀 Caching for performance...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. File permissions
echo -e "${YELLOW}🔐 Setting file permissions...${NC}"
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public
chmod -R 775 storage/framework
chmod -R 775 storage/logs
# Set specific permissions for upload directories
chmod -R 775 storage/app/public/teachers
chmod -R 775 storage/app/public/articles
chmod -R 775 storage/app/public/services
chmod -R 775 storage/app/public/profile
chmod -R 775 storage/app/public/hero-sections
chmod -R 775 storage/app/public/hero-backgrounds
chmod -R 775 storage/app/public/livewire-tmp

# 7. Security checks
echo -e "${YELLOW}🛡️  Security checks...${NC}"

# Check .env file permissions
chmod 600 .env.production

# Check if debug is disabled
if grep -q "APP_DEBUG=true" .env.production; then
    echo -e "${RED}⚠️  WARNING: APP_DEBUG is enabled in production!${NC}"
fi

# Check if secure cookies are enabled for HTTPS
if grep -q "SESSION_SECURE_COOKIES=false" .env.production; then
    echo -e "${YELLOW}⚠️  WARNING: Secure cookies disabled. Enable for HTTPS sites.${NC}"
fi

# 8. Health check
echo -e "${YELLOW}🏥 Running health checks...${NC}"

# Test database connection
php artisan migrate:status > /dev/null 2>&1 || {
    echo -e "${RED}❌ Database connection failed!${NC}"
    exit 1
}

# Test storage permissions
touch storage/logs/test.log && rm storage/logs/test.log || {
    echo -e "${RED}❌ Storage permissions error!${NC}"
    exit 1
}

# 9. Restart services (uncomment as needed)
echo -e "${YELLOW}🔄 Restarting services...${NC}"
# sudo systemctl restart nginx
# sudo systemctl restart php8.2-fpm
# sudo supervisorctl restart all

# 10. Final cleanup
echo -e "${YELLOW}🧹 Final cleanup...${NC}"
php artisan optimize

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo -e "${GREEN}🎉 SMA Tunas Harapan is ready for production!${NC}"

# Post-deployment notes
echo -e "${YELLOW}"
echo "📝 POST-DEPLOYMENT CHECKLIST:"
echo "  1. Test the website functionality"
echo "  2. Check error logs: tail -f storage/logs/laravel.log"
echo "  3. Monitor performance"
echo "  4. Verify SSL certificate"
echo "  5. Test contact forms"
echo "  6. Check image uploads"
echo "  7. Verify database backups"
echo -e "${NC}"

# Display important URLs
echo -e "${GREEN}"
echo "🌐 Important URLs:"
echo "  • Website: $(grep APP_URL .env.production | cut -d'=' -f2)"
echo "  • Admin Panel: $(grep APP_URL .env.production | cut -d'=' -f2)/admin"
echo -e "${NC}"
