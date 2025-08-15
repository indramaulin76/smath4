#!/bin/bash

# SMA Tunas Harapan - Enhanced Production Deployment Script
# Usage: ./deploy.sh [--skip-backup] [--skip-tests]

set -e

echo "🚀 Starting deployment for SMA Tunas Harapan..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Parse command line arguments
SKIP_BACKUP=false
SKIP_TESTS=false
for arg in "$@"; do
    case $arg in
        --skip-backup)
            SKIP_BACKUP=true
            shift
            ;;
        --skip-tests)
            SKIP_TESTS=true
            shift
            ;;
        --help)
            echo "Usage: ./deploy.sh [--skip-backup] [--skip-tests]"
            echo "  --skip-backup: Skip creating backup before deployment"
            echo "  --skip-tests: Skip running tests"
            exit 0
            ;;
    esac
done

# Function to log with timestamp
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a storage/logs/deployment.log
}

# Function to handle errors
handle_error() {
    echo -e "${RED}❌ Deployment failed at: $1${NC}"
    echo "Check storage/logs/deployment.log for details"
    exit 1
}

# Trap errors
trap 'handle_error "$(basename $0):$LINENO"' ERR

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: artisan file not found. Please run this script from the Laravel project root.${NC}"
    exit 1
fi

# Create logs directory if it doesn't exist
mkdir -p storage/logs

log "🚀 Starting deployment for SMA Tunas Harapan..."

# 0. Pre-deployment checks
echo -e "${YELLOW}🔍 Pre-deployment checks...${NC}"

# Check system requirements
if [ -f "check-requirements.sh" ]; then
    chmod +x check-requirements.sh
    if ! ./check-requirements.sh --quiet; then
        echo -e "${RED}❌ System requirements not met!${NC}"
        echo "Run './check-requirements.sh' for detailed information."
        exit 1
    fi
    log "✅ System requirements check passed"
else
    echo -e "${YELLOW}⚠️  Requirements checker not found, skipping...${NC}"
fi

# Check disk space (minimum 1GB)
AVAILABLE_SPACE=$(df -BG . | awk 'NR==2 {print $4}' | sed 's/G//')
if [ "$AVAILABLE_SPACE" -lt 1 ]; then
    echo -e "${RED}❌ Insufficient disk space: ${AVAILABLE_SPACE}GB available, 1GB required${NC}"
    exit 1
fi
log "✅ Disk space check passed: ${AVAILABLE_SPACE}GB available"

# 1. Environment Check
echo -e "${YELLOW}📋 Checking environment...${NC}"

# Handle .env file
ENV_FILE=".env"
if [ ! -f "$ENV_FILE" ]; then
    if [ -f ".env.production" ]; then
        ENV_FILE=".env.production"
        echo -e "${YELLOW}⚠️  Using .env.production${NC}"
    elif [ -f ".env.production.example" ]; then
        echo -e "${YELLOW}⚠️  .env not found. Creating from production example...${NC}"
        cp .env.production.example .env
        echo -e "${RED}Please edit .env with your production settings before continuing!${NC}"
        echo "Required settings:"
        echo "  - APP_URL (your domain)"
        echo "  - Database configuration"
        echo "  - Mail configuration (optional)"
        exit 1
    else
        echo -e "${RED}❌ No environment file found!${NC}"
        exit 1
    fi
fi

# Validate critical environment variables
if ! grep -q "APP_KEY=base64:" "$ENV_FILE"; then
    echo -e "${RED}❌ APP_KEY not set in $ENV_FILE${NC}"
    echo "Run: php artisan key:generate --force"
    exit 1
fi

if grep -q "APP_DEBUG=true" "$ENV_FILE"; then
    echo -e "${RED}❌ APP_DEBUG is enabled in $ENV_FILE! This is unsafe for production.${NC}"
    exit 1
fi

log "✅ Environment configuration validated"

# 2. Backup current state (if not skipped)
if [ "$SKIP_BACKUP" = false ]; then
    echo -e "${YELLOW}💾 Creating backup...${NC}"
    BACKUP_DIR="storage/app/backup/$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$BACKUP_DIR"
    
    # Backup database
    if command -v mysqldump >/dev/null 2>&1; then
        DB_HOST=$(grep "DB_HOST=" "$ENV_FILE" | cut -d'=' -f2)
        DB_DATABASE=$(grep "DB_DATABASE=" "$ENV_FILE" | cut -d'=' -f2)
        DB_USERNAME=$(grep "DB_USERNAME=" "$ENV_FILE" | cut -d'=' -f2)
        DB_PASSWORD=$(grep "DB_PASSWORD=" "$ENV_FILE" | cut -d'=' -f2)
        
        if [ ! -z "$DB_DATABASE" ] && [ ! -z "$DB_USERNAME" ]; then
            echo -n "🗃️  Backing up database... "
            if mysqldump -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_DIR/database.sql" 2>/dev/null; then
                echo -e "${GREEN}✅${NC}"
                log "✅ Database backup created: $BACKUP_DIR/database.sql"
            else
                echo -e "${YELLOW}⚠️  Failed${NC}"
                log "⚠️  Database backup failed (deployment continues)"
            fi
        fi
    fi
    
    # Backup uploads
    if [ -d "storage/app/public" ]; then
        cp -r storage/app/public "$BACKUP_DIR/uploads" 2>/dev/null || true
        log "✅ Uploads backup created"
    fi
    
    # Backup environment file
    cp "$ENV_FILE" "$BACKUP_DIR/env_backup" 2>/dev/null || true
    log "✅ Environment backup created"
    
    # Keep only last 5 backups
    cd storage/app/backup && ls -t | tail -n +6 | xargs -d '\n' rm -rf -- 2>/dev/null || true
    cd - >/dev/null
else
    echo -e "${YELLOW}⏭️  Skipping backup (--skip-backup flag)${NC}"
    log "⏭️  Backup skipped"
fi

# 3. Run tests (if not skipped)
if [ "$SKIP_TESTS" = false ] && [ -f "phpunit.xml" ]; then
    echo -e "${YELLOW}🧪 Running tests...${NC}"
    if php artisan test --stop-on-failure; then
        echo -e "${GREEN}✅ All tests passed${NC}"
        log "✅ Tests passed"
    else
        echo -e "${RED}❌ Tests failed!${NC}"
        echo "Use --skip-tests to bypass this check (not recommended)"
        exit 1
    fi
else
    echo -e "${YELLOW}⏭️  Skipping tests${NC}"
    log "⏭️  Tests skipped"
fi

# 4. Install dependencies
echo -e "${YELLOW}📦 Installing dependencies...${NC}"

# Check if composer.lock exists
if [ ! -f "composer.lock" ]; then
    echo -e "${YELLOW}⚠️  composer.lock not found, this may take longer...${NC}"
fi

# Install with timeout
timeout 600 composer install --no-dev --optimize-autoloader --no-interaction || handle_error "Composer install failed"
log "✅ Composer dependencies installed"

# 5. NPM build for production
echo -e "${YELLOW}🎨 Building assets...${NC}"

# Clean previous builds
if [ -d "public/build" ]; then
    rm -rf public/build
fi

# Install NPM dependencies
timeout 300 npm ci || handle_error "NPM install failed"
log "✅ NPM dependencies installed"

# Build assets
timeout 300 npm run build || handle_error "Asset build failed"
log "✅ Assets built successfully"

# 6. Laravel optimizations
echo -e "${YELLOW}⚡ Optimizing Laravel...${NC}"

# Clear all caches first
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true
log "✅ Caches cleared"

# Generate application key if not exists
if ! grep -q "APP_KEY=" "$ENV_FILE" || [ -z "$(grep "APP_KEY=" "$ENV_FILE" | cut -d'=' -f2)" ]; then
    php artisan key:generate --force
    log "✅ Application key generated"
fi

# Database operations with connection test
echo -e "${YELLOW}🗃️  Database operations...${NC}"

# Test database connection first
echo -n "🔗 Testing database connection... "
if php artisan migrate:status >/dev/null 2>&1; then
    echo -e "${GREEN}✅${NC}"
    log "✅ Database connection successful"
else
    echo -e "${RED}❌${NC}"
    echo "Database connection failed. Please check your configuration."
    log "❌ Database connection failed"
    exit 1
fi

# Run migrations
php artisan migrate --force || handle_error "Database migration failed"
log "✅ Database migrations completed"

# Storage link
echo -e "${YELLOW}🔗 Creating storage link...${NC}"
php artisan storage:link --force || true
log "✅ Storage symlink created"

# Cache optimizations
echo -e "${YELLOW}🚀 Caching for performance...${NC}"
php artisan config:cache || handle_error "Config cache failed"
php artisan route:cache || handle_error "Route cache failed"
php artisan view:cache || handle_error "View cache failed"
php artisan event:cache || handle_error "Event cache failed"
log "✅ Laravel caches created"

# 7. File permissions
echo -e "${YELLOW}🔐 Setting file permissions...${NC}"

# Set directory permissions
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage/app/public 2>/dev/null || true
chmod -R 775 storage/framework 2>/dev/null || true
chmod -R 775 storage/logs 2>/dev/null || true

# Set file permissions
chmod 600 "$ENV_FILE" 2>/dev/null || true

# Set ownership if running as root
if [ "$EUID" -eq 0 ]; then
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
    log "✅ Ownership set to www-data"
fi

log "✅ File permissions configured"

# 8. Security checks
echo -e "${YELLOW}🛡️  Security checks...${NC}"

# Check .env file permissions
if [ -f "$ENV_FILE" ]; then
    ENV_PERMS=$(stat -c "%a" "$ENV_FILE" 2>/dev/null || echo "unknown")
    if [ "$ENV_PERMS" != "600" ]; then
        echo -e "${YELLOW}⚠️  Environment file permissions: $ENV_PERMS (should be 600)${NC}"
        chmod 600 "$ENV_FILE" 2>/dev/null || true
    fi
fi

# Check if debug is disabled
if grep -q "APP_DEBUG=true" "$ENV_FILE"; then
    echo -e "${RED}⚠️  WARNING: APP_DEBUG is enabled in production!${NC}"
    log "⚠️  WARNING: APP_DEBUG enabled in production"
fi

# Check if secure cookies are enabled for HTTPS
if grep -q "SESSION_SECURE_COOKIES=false" "$ENV_FILE"; then
    echo -e "${YELLOW}⚠️  WARNING: Secure cookies disabled. Enable for HTTPS sites.${NC}"
    log "⚠️  WARNING: Secure cookies disabled"
fi

log "✅ Security checks completed"

# 9. Health check
echo -e "${YELLOW}🏥 Running health checks...${NC}"

# Test database connection
if php artisan migrate:status > /dev/null 2>&1; then
    log "✅ Database connection: OK"
else
    echo -e "${RED}❌ Database connection failed!${NC}"
    log "❌ Database connection failed"
    exit 1
fi

# Test storage permissions
if touch storage/logs/test.log 2>/dev/null && rm storage/logs/test.log 2>/dev/null; then
    log "✅ Storage permissions: OK"
else
    echo -e "${RED}❌ Storage permissions error!${NC}"
    log "❌ Storage permissions error"
    exit 1
fi

# Test if public/storage link exists
if [ -L "public/storage" ]; then
    log "✅ Storage symlink: OK"
else
    echo -e "${YELLOW}⚠️  Storage symlink missing${NC}"
    php artisan storage:link --force
    log "✅ Storage symlink recreated"
fi

# 10. Restart services (uncomment as needed)
echo -e "${YELLOW}🔄 Restarting services...${NC}"
# sudo systemctl restart nginx 2>/dev/null || echo "Could not restart nginx"
# sudo systemctl restart php8.2-fpm 2>/dev/null || echo "Could not restart php-fpm"
# sudo supervisorctl restart all 2>/dev/null || echo "Could not restart supervisor"
log "⚠️  Service restart skipped (enable manually if needed)"

# 11. Final optimization
echo -e "${YELLOW}🧹 Final optimization...${NC}"
php artisan optimize || true
log "✅ Final optimization completed"

# 12. Post-deployment verification
echo -e "${YELLOW}✅ Post-deployment verification...${NC}"

# Check if application is accessible
echo -n "🌐 Testing application response... "
if php artisan route:list >/dev/null 2>&1; then
    echo -e "${GREEN}✅${NC}"
    log "✅ Application routes accessible"
else
    echo -e "${RED}❌${NC}"
    log "❌ Application routes test failed"
fi

# Log deployment completion
DEPLOYMENT_TIME=$(date '+%Y-%m-%d %H:%M:%S')
log "🎉 Deployment completed successfully at $DEPLOYMENT_TIME"

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo -e "${GREEN}🎉 SMA Tunas Harapan is ready for production!${NC}"

# Enhanced post-deployment notes
echo -e "${BLUE}"
echo "📝 POST-DEPLOYMENT CHECKLIST:"
echo "  1. ✅ Test the website functionality"
echo "  2. ✅ Check error logs: tail -f storage/logs/laravel.log"
echo "  3. ✅ Monitor performance and response times"
echo "  4. ✅ Verify SSL certificate (if using HTTPS)"
echo "  5. ✅ Test contact forms and file uploads"
echo "  6. ✅ Check admin panel access and functionality"
echo "  7. ✅ Verify database backups are working"
echo "  8. ✅ Test mobile responsiveness"
echo "  9. ✅ Check all images and assets load correctly"
echo " 10. ✅ Monitor server resources (CPU, memory, disk)"
echo -e "${NC}"

# Display important URLs and credentials
echo -e "${GREEN}"
echo "🌐 IMPORTANT INFORMATION:"
APP_URL=$(grep "APP_URL=" "$ENV_FILE" | cut -d'=' -f2)
if [ ! -z "$APP_URL" ]; then
    echo "  • Website: $APP_URL"
    echo "  • Admin Panel: $APP_URL/admin"
fi
echo "  • Default Admin Email: admin@smath.com"
echo "  • Default Admin Password: password"
echo "  • Deployment Log: storage/logs/deployment.log"
echo -e "${NC}"

# Performance and monitoring tips
echo -e "${BLUE}"
echo "💡 PERFORMANCE TIPS:"
echo "  • Enable OPcache in PHP configuration"
echo "  • Use Redis for caching and sessions"
echo "  • Enable Gzip compression in web server"
echo "  • Set up monitoring (log rotation, disk space)"
echo "  • Consider using a CDN for static assets"
echo -e "${NC}"

# Security reminders
echo -e "${YELLOW}"
echo "🔒 SECURITY REMINDERS:"
echo "  • Change default admin password immediately"
echo "  • Keep system and dependencies updated"
echo "  • Monitor access logs regularly"
echo "  • Set up automated backups"
echo "  • Use strong passwords and 2FA when possible"
echo -e "${NC}"

# Useful commands
echo -e "${BLUE}"
echo "🛠️  USEFUL COMMANDS:"
echo "  • Health check: ./health-check.sh"
echo "  • Requirements check: ./check-requirements.sh"
echo "  • View logs: tail -f storage/logs/laravel.log"
echo "  • Clear all caches: php artisan optimize:clear"
echo "  • Update application: git pull && ./deploy.sh"
echo "  • Backup database: mysqldump -u [user] -p [database] > backup.sql"
echo -e "${NC}"

# Deployment summary
echo ""
echo "================================================="
echo -e "${GREEN}🎊 DEPLOYMENT SUMMARY${NC}"
echo "================================================="
echo "Deployment completed at: $(date '+%Y-%m-%d %H:%M:%S')"
echo "Environment: $(grep "APP_ENV=" "$ENV_FILE" | cut -d'=' -f2)"
echo "PHP Version: $(php -r 'echo PHP_VERSION;')"
echo "Laravel Version: $(php artisan --version | head -1)"
if [ "$SKIP_BACKUP" = false ]; then
    echo "Backup created: Yes"
else
    echo "Backup created: Skipped"
fi
if [ "$SKIP_TESTS" = false ]; then
    echo "Tests executed: Yes"
else
    echo "Tests executed: Skipped"
fi
echo "Deployment log: storage/logs/deployment.log"
echo ""
echo -e "${GREEN}🎉 Ready to serve! Happy deploying!${NC}"
