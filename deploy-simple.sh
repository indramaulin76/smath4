#!/bin/bash

# SMA Tunas Harapan - One-Command Deployment
# Usage: ./deploy-simple.sh [domain]

set -e

echo "🚀 SMA Tunas Harapan - Simple Deployment"
echo "======================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Get domain from argument or prompt
DOMAIN="$1"
if [ -z "$DOMAIN" ]; then
    echo -n "🌐 Enter your domain (e.g., smatunasharapan.com): "
    read DOMAIN
fi

if [ -z "$DOMAIN" ]; then
    echo -e "${RED}❌ Domain is required!${NC}"
    exit 1
fi

echo -e "${BLUE}📋 Deployment Configuration${NC}"
echo "Domain: $DOMAIN"
echo "Installation path: $(pwd)"
echo ""

# Confirm deployment
echo -n "Continue with deployment? [y/N]: "
read CONFIRM
if [[ ! $CONFIRM =~ ^[Yy]$ ]]; then
    echo "Deployment cancelled."
    exit 0
fi

echo ""
echo -e "${YELLOW}🔍 Step 1: Checking requirements...${NC}"
if [ -f "check-requirements.sh" ]; then
    chmod +x check-requirements.sh
    if ! ./check-requirements.sh --quiet; then
        echo -e "${RED}❌ Requirements check failed!${NC}"
        echo "Please install missing requirements and try again."
        exit 1
    fi
else
    echo -e "${YELLOW}⚠️  Requirements checker not found, continuing...${NC}"
fi

echo ""
echo -e "${YELLOW}📄 Step 2: Setting up environment...${NC}"

# Create .env file
if [ ! -f ".env" ]; then
    if [ -f ".env.production.example" ]; then
        cp .env.production.example .env
        echo "✅ Created .env from production example"
    elif [ -f ".env.example" ]; then
        cp .env.example .env
        echo "✅ Created .env from example"
    else
        echo -e "${RED}❌ No .env template found!${NC}"
        exit 1
    fi
fi

# Update domain in .env
sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|g" .env
sed -i "s|SESSION_DOMAIN=.*|SESSION_DOMAIN=$DOMAIN|g" .env
echo "✅ Updated domain configuration"

# Set production environment
sed -i "s|APP_ENV=.*|APP_ENV=production|g" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|g" .env
echo "✅ Set production environment"

# Database configuration
echo ""
echo -e "${BLUE}🗃️  Database Configuration${NC}"
echo -n "Database host [localhost]: "
read DB_HOST
DB_HOST=${DB_HOST:-localhost}

echo -n "Database name: "
read DB_DATABASE

echo -n "Database username: "
read DB_USERNAME

echo -n "Database password: "
read -s DB_PASSWORD
echo ""

# Update database config
sed -i "s|DB_HOST=.*|DB_HOST=$DB_HOST|g" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|g" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|g" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|g" .env
echo "✅ Updated database configuration"

echo ""
echo -e "${YELLOW}📦 Step 3: Installing dependencies...${NC}"

# Install Composer dependencies
if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --optimize-autoloader --no-interaction
    echo "✅ Composer dependencies installed"
else
    echo -e "${RED}❌ Composer not found!${NC}"
    exit 1
fi

# Install NPM dependencies
if command -v npm >/dev/null 2>&1; then
    npm ci --silent
    echo "✅ NPM dependencies installed"
else
    echo -e "${RED}❌ NPM not found!${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}🎨 Step 4: Building assets...${NC}"
npm run build
echo "✅ Assets built successfully"

echo ""
echo -e "${YELLOW}⚙️  Step 5: Laravel setup...${NC}"

# Generate app key
php artisan key:generate --force
echo "✅ Application key generated"

# Test database connection
echo -n "🔗 Testing database connection... "
if php artisan migrate:status >/dev/null 2>&1; then
    echo -e "${GREEN}✅ Connected${NC}"
else
    echo -e "${RED}❌ Failed${NC}"
    echo "Please check your database configuration and try again."
    exit 1
fi

# Run migrations
php artisan migrate --force
echo "✅ Database migrations completed"

# Seed database
echo -n "🌱 Seed database with sample data? [y/N]: "
read SEED_DB
if [[ $SEED_DB =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
    echo "✅ Database seeded"
fi

# Create storage link
php artisan storage:link --force
echo "✅ Storage symlink created"

echo ""
echo -e "${YELLOW}⚡ Step 6: Optimizing for production...${NC}"

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Create production caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "✅ Laravel optimizations completed"

echo ""
echo -e "${YELLOW}🔐 Step 7: Setting permissions...${NC}"

# Set file permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public
chmod -R 775 storage/framework
chmod -R 775 storage/logs
chmod 600 .env

# Set ownership (if running as root/sudo)
if [ "$EUID" -eq 0 ]; then
    chown -R www-data:www-data storage bootstrap/cache
    echo "✅ Ownership set to www-data"
fi

echo "✅ Permissions configured"

echo ""
echo -e "${YELLOW}🏥 Step 8: Running health checks...${NC}"

if [ -f "health-check.sh" ]; then
    chmod +x health-check.sh
    if ./health-check.sh --summary; then
        echo -e "${GREEN}✅ Health checks passed${NC}"
    else
        echo -e "${YELLOW}⚠️  Some health checks failed, but deployment continued${NC}"
    fi
fi

echo ""
echo "================================================="
echo -e "${GREEN}🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!${NC}"
echo "================================================="
echo ""
echo -e "${BLUE}📱 Website Information:${NC}"
echo "🌐 Website URL: https://$DOMAIN"
echo "👨‍💼 Admin Panel: https://$DOMAIN/admin"
echo "📧 Default Admin: admin@smath.com"
echo "🔑 Default Password: password"
echo ""
echo -e "${BLUE}📝 Next Steps:${NC}"
echo "1. 🌐 Configure your web server (Nginx/Apache) to point to public/ directory"
echo "2. 🔒 Setup SSL certificate (recommended: Let's Encrypt)"
echo "3. 👨‍💼 Login to admin panel and change default password"
echo "4. 📋 Upload your content and images"
echo "5. 🔍 Test all functionality"
echo ""
echo -e "${BLUE}🛠️  Useful Commands:${NC}"
echo "• Health check: ./health-check.sh"
echo "• View logs: tail -f storage/logs/laravel.log"
echo "• Clear cache: php artisan optimize:clear"
echo "• Update app: git pull && composer install --no-dev && npm run build"
echo ""
echo -e "${BLUE}📚 Documentation:${NC}"
echo "• Full guide: PANDUAN_DEPLOYMENT.md"
echo "• Admin guide: USER_GUIDE_ADMIN.md"
echo "• Security: SECURITY_FINAL.md"
echo ""
echo -e "${GREEN}🎊 Happy deploying!${NC}"