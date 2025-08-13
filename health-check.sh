#!/bin/bash

# SMA Tunas Harapan - Health Check Script
# Usage: ./health-check.sh

set -e

echo "🏥 SMA Tunas Harapan - Health Check"
echo "=================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
LOG_FILE="storage/logs/health-check.log"
TIMESTAMP=$(date '+%Y-%m-%d %H:%M:%S')

# Function to log results
log_result() {
    echo "[$TIMESTAMP] $1" >> $LOG_FILE
}

# Function to check and display status
check_status() {
    local check_name="$1"
    local command="$2"
    
    echo -n "🔍 $check_name... "
    
    if eval $command > /dev/null 2>&1; then
        echo -e "${GREEN}✅ OK${NC}"
        log_result "✅ $check_name: OK"
        return 0
    else
        echo -e "${RED}❌ FAILED${NC}"
        log_result "❌ $check_name: FAILED"
        return 1
    fi
}

# Initialize log
echo "[$TIMESTAMP] Starting health check..." >> $LOG_FILE

FAILED_CHECKS=0

# 1. Check if Laravel app is accessible
echo -e "${YELLOW}🌐 Application Status${NC}"
if ! check_status "Laravel Application" "php artisan --version"; then
    ((FAILED_CHECKS++))
fi

# 2. Database connectivity
echo -e "${YELLOW}🗃️  Database Status${NC}"
if ! check_status "Database Connection" "php artisan migrate:status"; then
    ((FAILED_CHECKS++))
fi

# 3. Storage permissions
echo -e "${YELLOW}📁 Storage Status${NC}"
if ! check_status "Storage Writable" "touch storage/logs/health-test.tmp && rm storage/logs/health-test.tmp"; then
    ((FAILED_CHECKS++))
fi

# 4. Storage symlink
if ! check_status "Storage Symlink" "test -L public/storage"; then
    ((FAILED_CHECKS++))
fi

# 5. Cache status
echo -e "${YELLOW}⚡ Cache Status${NC}"
if ! check_status "Config Cache" "test -f bootstrap/cache/config.php"; then
    ((FAILED_CHECKS++))
fi

if ! check_status "Route Cache" "test -f bootstrap/cache/routes-v7.php"; then
    ((FAILED_CHECKS++))
fi

# 6. File permissions
echo -e "${YELLOW}🔐 Permissions${NC}"
if ! check_status "Storage Permissions" "test -w storage/logs"; then
    ((FAILED_CHECKS++))
fi

if ! check_status "Cache Permissions" "test -w bootstrap/cache"; then
    ((FAILED_CHECKS++))
fi

# 7. Disk space
echo -e "${YELLOW}💾 Disk Space${NC}"
DISK_USAGE=$(df -h . | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 90 ]; then
    echo -e "${RED}❌ Disk usage: ${DISK_USAGE}% (WARNING: >90%)${NC}"
    log_result "❌ Disk Space: ${DISK_USAGE}% - Critical"
    ((FAILED_CHECKS++))
elif [ "$DISK_USAGE" -gt 80 ]; then
    echo -e "${YELLOW}⚠️  Disk usage: ${DISK_USAGE}% (WARNING: >80%)${NC}"
    log_result "⚠️  Disk Space: ${DISK_USAGE}% - Warning"
else
    echo -e "${GREEN}✅ Disk usage: ${DISK_USAGE}%${NC}"
    log_result "✅ Disk Space: ${DISK_USAGE}% - OK"
fi

# 8. Memory usage
echo -e "${YELLOW}🧠 Memory Status${NC}"
MEMORY_USAGE=$(free | grep Mem | awk '{printf "%.1f", $3/$2 * 100.0}')
MEMORY_USAGE_INT=$(echo $MEMORY_USAGE | cut -d. -f1)

if [ "$MEMORY_USAGE_INT" -gt 90 ]; then
    echo -e "${RED}❌ Memory usage: ${MEMORY_USAGE}% (CRITICAL: >90%)${NC}"
    log_result "❌ Memory: ${MEMORY_USAGE}% - Critical"
    ((FAILED_CHECKS++))
elif [ "$MEMORY_USAGE_INT" -gt 80 ]; then
    echo -e "${YELLOW}⚠️  Memory usage: ${MEMORY_USAGE}% (WARNING: >80%)${NC}"
    log_result "⚠️  Memory: ${MEMORY_USAGE}% - Warning"
else
    echo -e "${GREEN}✅ Memory usage: ${MEMORY_USAGE}%${NC}"
    log_result "✅ Memory: ${MEMORY_USAGE}% - OK"
fi

# 9. Check critical directories
echo -e "${YELLOW}📂 Directory Structure${NC}"
CRITICAL_DIRS=("storage/app" "storage/logs" "storage/framework" "public/storage" "bootstrap/cache")

for dir in "${CRITICAL_DIRS[@]}"; do
    if ! check_status "Directory: $dir" "test -d $dir"; then
        ((FAILED_CHECKS++))
    fi
done

# 10. Check environment
echo -e "${YELLOW}🔧 Environment${NC}"
if [ -f ".env" ]; then
    ENV_TYPE=$(grep "APP_ENV=" .env | cut -d'=' -f2)
    echo -e "${GREEN}✅ Environment: $ENV_TYPE${NC}"
    log_result "✅ Environment: $ENV_TYPE"
    
    # Check debug mode in production
    if [ "$ENV_TYPE" = "production" ]; then
        DEBUG_MODE=$(grep "APP_DEBUG=" .env | cut -d'=' -f2)
        if [ "$DEBUG_MODE" = "true" ]; then
            echo -e "${RED}❌ DEBUG MODE ENABLED IN PRODUCTION!${NC}"
            log_result "❌ Debug mode enabled in production - SECURITY RISK"
            ((FAILED_CHECKS++))
        fi
    fi
else
    echo -e "${RED}❌ .env file not found${NC}"
    log_result "❌ .env file missing"
    ((FAILED_CHECKS++))
fi

# Summary
echo ""
echo "=================================="
if [ $FAILED_CHECKS -eq 0 ]; then
    echo -e "${GREEN}🎉 All checks passed! System is healthy.${NC}"
    log_result "✅ Health check completed - All systems OK"
    exit 0
else
    echo -e "${RED}❌ $FAILED_CHECKS check(s) failed. Please review the issues above.${NC}"
    log_result "❌ Health check completed - $FAILED_CHECKS issues found"
    exit 1
fi
