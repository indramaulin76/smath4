#!/bin/bash

# Alternative Storage Fix - Manual Symlink Creation
echo "🔧 Creating manual storage symlink with absolute path..."

# Colors
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m'

# Get current directory
CURRENT_DIR=$(pwd)
echo -e "${YELLOW}Current directory: $CURRENT_DIR${NC}"

# Remove existing symlink
echo -e "${YELLOW}Removing existing symlink...${NC}"
rm -rf public/storage

# Create directories
echo -e "${YELLOW}Creating storage directories...${NC}"
mkdir -p storage/app/public/teachers
mkdir -p storage/app/public/articles  
mkdir -p storage/app/public/services

# Create absolute symlink
echo -e "${YELLOW}Creating absolute symlink...${NC}"
ln -s "$CURRENT_DIR/storage/app/public" "$CURRENT_DIR/public/storage"

# Set permissions
echo -e "${YELLOW}Setting permissions...${NC}"
chmod -R 755 storage/app/public/
chown -R www-data:www-data storage/app/public/ 2>/dev/null || chown -R apache:apache storage/app/public/ 2>/dev/null || echo "Could not set ownership"

# Test
echo -e "${YELLOW}Testing symlink...${NC}"
echo "test" > storage/app/public/test.txt
if [ -f "public/storage/test.txt" ]; then
    echo -e "${GREEN}✅ Symlink working!${NC}"
    rm storage/app/public/test.txt
else
    echo "❌ Symlink still not working"
fi

echo -e "${GREEN}Manual symlink creation completed!${NC}"
