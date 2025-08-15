#!/bin/bash

# Nginx Configuration Fix for Storage Access
echo "🔧 Checking and fixing Nginx configuration..."

# Colors
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}📋 Checking current Nginx configuration...${NC}"

# Find nginx config files
NGINX_SITES="/etc/nginx/sites-available"
NGINX_CONF="/etc/nginx/nginx.conf"

echo "Looking for site configuration..."
if [ -d "$NGINX_SITES" ]; then
    echo "Available sites:"
    ls -la $NGINX_SITES/
    
    # Look for smatunasharapan config
    if [ -f "$NGINX_SITES/smatunasharapan.site" ] || [ -f "$NGINX_SITES/default" ]; then
        SITE_CONFIG="$NGINX_SITES/smatunasharapan.site"
        [ ! -f "$SITE_CONFIG" ] && SITE_CONFIG="$NGINX_SITES/default"
        
        echo -e "${YELLOW}Found config: $SITE_CONFIG${NC}"
        
        # Check if storage location exists
        if grep -q "location /storage" "$SITE_CONFIG"; then
            echo -e "${GREEN}✅ Storage location block exists${NC}"
        else
            echo -e "${RED}❌ Storage location block missing${NC}"
            echo -e "${YELLOW}Creating storage location block...${NC}"
            
            # Create backup
            cp "$SITE_CONFIG" "$SITE_CONFIG.backup.$(date +%Y%m%d_%H%M%S)"
            
            # Add storage location before the main location block
            sed -i '/location \/ {/i\
    # Storage files location\
    location /storage/ {\
        alias /var/www/smatunasharapan/public/storage/;\
        expires 1y;\
        add_header Cache-Control "public, immutable";\
        \
        # Prevent directory listing\
        autoindex off;\
        \
        # Only allow specific file types\
        location ~* \\.(jpg|jpeg|png|gif|ico|svg|webp|pdf|txt)$ {\
            try_files $uri =404;\
        }\
    }\
' "$SITE_CONFIG"
            
            echo -e "${GREEN}✅ Added storage location block${NC}"
        fi
        
        # Test nginx configuration
        echo -e "${YELLOW}Testing Nginx configuration...${NC}"
        if nginx -t; then
            echo -e "${GREEN}✅ Nginx configuration is valid${NC}"
            echo -e "${YELLOW}Reloading Nginx...${NC}"
            systemctl reload nginx
            echo -e "${GREEN}✅ Nginx reloaded${NC}"
        else
            echo -e "${RED}❌ Nginx configuration error!${NC}"
            echo "Restoring backup..."
            mv "$SITE_CONFIG.backup."* "$SITE_CONFIG"
        fi
    else
        echo -e "${RED}❌ Site configuration not found${NC}"
    fi
else
    echo -e "${RED}❌ Nginx sites directory not found${NC}"
fi

# Check if nginx is running
if systemctl is-active --quiet nginx; then
    echo -e "${GREEN}✅ Nginx is running${NC}"
else
    echo -e "${RED}❌ Nginx is not running${NC}"
    echo "Starting Nginx..."
    systemctl start nginx
fi

# Show current nginx status
echo -e "${YELLOW}📊 Nginx status:${NC}"
systemctl status nginx --no-pager -l

echo -e "${GREEN}✅ Nginx configuration check completed!${NC}"
