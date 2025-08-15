#!/bin/bash

# SMA Tunas Harapan - Server Requirements Checker
# Usage: ./check-requirements.sh

set -e

echo "🔍 SMA Tunas Harapan - Server Requirements Checker"
echo "================================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Initialize counters
PASSED=0
FAILED=0
WARNINGS=0

# Function to check and display status
check_requirement() {
    local requirement="$1"
    local check_command="$2"
    local required_version="$3"
    local current_version=""
    
    echo -n "🔍 Checking $requirement... "
    
    if command -v $check_command >/dev/null 2>&1; then
        case $check_command in
            "php")
                current_version=$(php -r "echo PHP_VERSION;" 2>/dev/null)
                ;;
            "composer")
                current_version=$(composer --version 2>/dev/null | grep -oP 'version \K[0-9.]+' | head -1)
                ;;
            "node")
                current_version=$(node --version 2>/dev/null | sed 's/v//')
                ;;
            "npm")
                current_version=$(npm --version 2>/dev/null)
                ;;
            "mysql")
                current_version=$(mysql --version 2>/dev/null | grep -oP 'Distrib \K[0-9.]+' | head -1)
                ;;
            "nginx")
                current_version=$(nginx -v 2>&1 | grep -oP 'nginx/\K[0-9.]+' | head -1)
                ;;
            "apache2")
                current_version=$(apache2 -v 2>/dev/null | grep -oP 'Apache/\K[0-9.]+' | head -1)
                ;;
            *)
                current_version="installed"
                ;;
        esac
        
        echo -e "${GREEN}✅ FOUND${NC} (v$current_version)"
        ((PASSED++))
        return 0
    else
        echo -e "${RED}❌ NOT FOUND${NC}"
        if [ ! -z "$required_version" ]; then
            echo "   Required: $required_version or higher"
        fi
        ((FAILED++))
        return 1
    fi
}

# Function to check PHP extensions
check_php_extension() {
    local extension="$1"
    echo -n "  📦 PHP Extension: $extension... "
    
    if php -m 2>/dev/null | grep -i "$extension" >/dev/null; then
        echo -e "${GREEN}✅ INSTALLED${NC}"
        return 0
    else
        echo -e "${RED}❌ MISSING${NC}"
        ((FAILED++))
        return 1
    fi
}

# Function to check directory permissions
check_permissions() {
    local directory="$1"
    local required_perm="$2"
    
    echo -n "🔐 Checking permissions for $directory... "
    
    if [ -d "$directory" ]; then
        if [ -w "$directory" ]; then
            echo -e "${GREEN}✅ WRITABLE${NC}"
            return 0
        else
            echo -e "${RED}❌ NOT WRITABLE${NC}"
            echo "   Run: chmod -R 755 $directory"
            ((FAILED++))
            return 1
        fi
    else
        echo -e "${YELLOW}⚠️  DIRECTORY NOT EXISTS${NC}"
        echo "   Will be created during installation"
        ((WARNINGS++))
        return 1
    fi
}

# Function to check disk space
check_disk_space() {
    echo -n "💾 Checking disk space... "
    
    # Get available space in GB
    available_space=$(df -BG . | awk 'NR==2 {print $4}' | sed 's/G//')
    
    if [ "$available_space" -ge 2 ]; then
        echo -e "${GREEN}✅ SUFFICIENT${NC} (${available_space}GB available)"
        return 0
    elif [ "$available_space" -ge 1 ]; then
        echo -e "${YELLOW}⚠️  LIMITED${NC} (${available_space}GB available)"
        echo "   Recommended: 2GB+ free space"
        ((WARNINGS++))
        return 1
    else
        echo -e "${RED}❌ INSUFFICIENT${NC} (${available_space}GB available)"
        echo "   Required: Minimum 1GB free space"
        ((FAILED++))
        return 1
    fi
}

# Function to check memory
check_memory() {
    echo -n "🧠 Checking memory... "
    
    # Get total memory in MB
    total_memory=$(free -m | awk 'NR==2{print $2}')
    
    if [ "$total_memory" -ge 2048 ]; then
        echo -e "${GREEN}✅ SUFFICIENT${NC} (${total_memory}MB total)"
        return 0
    elif [ "$total_memory" -ge 1024 ]; then
        echo -e "${YELLOW}⚠️  LIMITED${NC} (${total_memory}MB total)"
        echo "   Recommended: 2GB+ RAM"
        ((WARNINGS++))
        return 1
    else
        echo -e "${RED}❌ INSUFFICIENT${NC} (${total_memory}MB total)"
        echo "   Required: Minimum 1GB RAM"
        ((FAILED++))
        return 1
    fi
}

# System Information
echo -e "${BLUE}📋 System Information${NC}"
echo "OS: $(uname -s) $(uname -r)"
echo "Architecture: $(uname -m)"
if [ -f /etc/os-release ]; then
    echo "Distribution: $(grep PRETTY_NAME /etc/os-release | cut -d'"' -f2)"
fi
echo ""

# Check basic requirements
echo -e "${BLUE}🔧 Basic Requirements${NC}"
check_requirement "PHP" "php" "8.2+"
check_requirement "Composer" "composer" "2.0+"
check_requirement "Node.js" "node" "18+"
check_requirement "NPM" "npm" "8+"
echo ""

# Check web server
echo -e "${BLUE}🌐 Web Server${NC}"
if check_requirement "Nginx" "nginx" "1.18+"; then
    WEB_SERVER="nginx"
elif check_requirement "Apache" "apache2" "2.4+"; then
    WEB_SERVER="apache"
else
    echo -e "${YELLOW}⚠️  No web server detected. You'll need to install Nginx or Apache.${NC}"
    WEB_SERVER="none"
fi
echo ""

# Check database
echo -e "${BLUE}🗃️  Database${NC}"
if check_requirement "MySQL" "mysql" "8.0+"; then
    DATABASE="mysql"
elif check_requirement "MariaDB" "mariadb" "10.4+"; then
    DATABASE="mariadb"
else
    echo -e "${YELLOW}⚠️  No database server detected. You'll need to install MySQL or MariaDB.${NC}"
    DATABASE="none"
fi
echo ""

# Check PHP extensions if PHP is available
if command -v php >/dev/null 2>&1; then
    echo -e "${BLUE}📦 Required PHP Extensions${NC}"
    
    # Required extensions
    REQUIRED_EXTENSIONS=("bcmath" "ctype" "curl" "dom" "fileinfo" "filter" "hash" "mbstring" "openssl" "pcre" "pdo" "session" "tokenizer" "xml" "zip" "gd" "intl" "mysql")
    
    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        check_php_extension "$ext"
    done
    echo ""
fi

# Check system resources
echo -e "${BLUE}💻 System Resources${NC}"
check_disk_space
check_memory
echo ""

# Check file permissions (if in Laravel directory)
if [ -f "artisan" ]; then
    echo -e "${BLUE}🔐 File Permissions${NC}"
    check_permissions "storage" "755"
    check_permissions "bootstrap/cache" "755"
    check_permissions "public" "755"
    echo ""
fi

# Check environment file
if [ -f "artisan" ]; then
    echo -e "${BLUE}⚙️  Environment Configuration${NC}"
    
    echo -n "📄 Checking .env file... "
    if [ -f ".env" ]; then
        echo -e "${GREEN}✅ EXISTS${NC}"
        
        # Check critical environment variables
        echo -n "  🔑 APP_KEY... "
        if grep -q "APP_KEY=base64:" .env; then
            echo -e "${GREEN}✅ SET${NC}"
        else
            echo -e "${RED}❌ NOT SET${NC}"
            echo "     Run: php artisan key:generate"
            ((FAILED++))
        fi
        
        echo -n "  🏠 APP_URL... "
        if grep -q "APP_URL=" .env && ! grep -q "APP_URL=http://localhost" .env; then
            echo -e "${GREEN}✅ CONFIGURED${NC}"
        else
            echo -e "${YELLOW}⚠️  NEEDS CONFIGURATION${NC}"
            echo "     Set your domain in .env file"
            ((WARNINGS++))
        fi
        
        echo -n "  🗃️  Database config... "
        if grep -q "DB_DATABASE=" .env && grep -q "DB_USERNAME=" .env; then
            echo -e "${GREEN}✅ CONFIGURED${NC}"
        else
            echo -e "${YELLOW}⚠️  NEEDS CONFIGURATION${NC}"
            echo "     Configure database settings in .env"
            ((WARNINGS++))
        fi
        
    else
        echo -e "${RED}❌ NOT FOUND${NC}"
        if [ -f ".env.example" ]; then
            echo "   Copy from: cp .env.example .env"
        elif [ -f ".env.production.example" ]; then
            echo "   Copy from: cp .env.production.example .env"
        fi
        ((FAILED++))
    fi
    echo ""
fi

# Security checks
echo -e "${BLUE}🛡️  Security Checks${NC}"

echo -n "🔒 Checking if debug mode is disabled... "
if [ -f ".env" ] && grep -q "APP_DEBUG=false" .env; then
    echo -e "${GREEN}✅ DISABLED${NC}"
elif [ -f ".env" ] && grep -q "APP_DEBUG=true" .env; then
    echo -e "${RED}❌ ENABLED${NC}"
    echo "   Set APP_DEBUG=false in production!"
    ((FAILED++))
else
    echo -e "${YELLOW}⚠️  NOT SET${NC}"
    ((WARNINGS++))
fi

echo -n "🌐 Checking HTTPS configuration... "
if [ -f ".env" ] && grep -q "APP_URL=https://" .env; then
    echo -e "${GREEN}✅ CONFIGURED${NC}"
elif [ -f ".env" ] && grep -q "APP_URL=http://" .env; then
    echo -e "${YELLOW}⚠️  HTTP ONLY${NC}"
    echo "   Consider using HTTPS for production"
    ((WARNINGS++))
else
    echo -e "${YELLOW}⚠️  NOT CONFIGURED${NC}"
    ((WARNINGS++))
fi

echo ""

# Installation commands based on OS
echo -e "${BLUE}📝 Installation Commands${NC}"

if [ -f /etc/debian_version ]; then
    echo -e "${YELLOW}For Ubuntu/Debian:${NC}"
    echo "  sudo apt update && sudo apt upgrade -y"
    echo "  sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath"
    echo "  sudo apt install -y nginx mysql-server"
    echo "  curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer"
    echo "  curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash - && sudo apt-get install -y nodejs"
elif [ -f /etc/redhat-release ]; then
    echo -e "${YELLOW}For CentOS/RHEL:${NC}"
    echo "  sudo dnf update -y"
    echo "  sudo dnf install -y php php-fpm php-mysql php-xml php-mbstring php-curl php-zip php-gd php-intl php-bcmath"
    echo "  sudo dnf install -y nginx mysql-server"
    echo "  curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer"
    echo "  curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash - && sudo dnf install -y nodejs npm"
fi

echo ""

# Final summary
echo "================================================="
echo -e "${BLUE}📊 SUMMARY${NC}"
echo "================================================="

if [ $FAILED -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}🎉 ALL REQUIREMENTS MET!${NC}"
    echo -e "${GREEN}✅ Your server is ready for deployment!${NC}"
elif [ $FAILED -eq 0 ]; then
    echo -e "${YELLOW}⚠️  READY WITH WARNINGS${NC}"
    echo -e "${GREEN}✅ Passed: $PASSED${NC}"
    echo -e "${YELLOW}⚠️  Warnings: $WARNINGS${NC}"
    echo ""
    echo "Your server meets the basic requirements but has some warnings."
    echo "You can proceed with deployment, but consider addressing the warnings."
else
    echo -e "${RED}❌ REQUIREMENTS NOT MET${NC}"
    echo -e "${GREEN}✅ Passed: $PASSED${NC}"
    echo -e "${YELLOW}⚠️  Warnings: $WARNINGS${NC}"
    echo -e "${RED}❌ Failed: $FAILED${NC}"
    echo ""
    echo "Please install the missing requirements before proceeding with deployment."
fi

echo ""
echo -e "${BLUE}📖 Next Steps:${NC}"
echo "1. Install missing requirements (see commands above)"
echo "2. Copy .env.production.example to .env"
echo "3. Configure database and other settings in .env"
echo "4. Run: ./deploy.sh"
echo "5. Run: ./health-check.sh"

echo ""
echo -e "${BLUE}📚 Documentation:${NC}"
echo "• Full deployment guide: PANDUAN_DEPLOYMENT.md"
echo "• Security guide: SECURITY_FINAL.md"
echo "• Admin guide: USER_GUIDE_ADMIN.md"

exit $FAILED