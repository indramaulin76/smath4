#!/bin/bash

# SMA Tunas Harapan - Backup and Rollback Management
# Usage: ./backup-rollback.sh [backup|restore|list|cleanup] [backup_name]

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
BACKUP_DIR="storage/app/backup"
ENV_FILE=".env"
LOG_FILE="storage/logs/backup.log"

# Functions
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

show_help() {
    echo "SMA Tunas Harapan - Backup and Rollback Management"
    echo ""
    echo "Usage: $0 [command] [options]"
    echo ""
    echo "Commands:"
    echo "  backup [name]     Create a new backup (optional custom name)"
    echo "  restore <name>    Restore from a specific backup"
    echo "  list             List all available backups"
    echo "  cleanup          Remove old backups (keep last 10)"
    echo "  help             Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 backup                    # Create backup with timestamp"
    echo "  $0 backup before_update      # Create backup with custom name"
    echo "  $0 restore 20231215_140530   # Restore specific backup"
    echo "  $0 list                      # List all backups"
    echo "  $0 cleanup                   # Clean old backups"
}

create_backup() {
    local backup_name="$1"
    
    # Generate backup name if not provided
    if [ -z "$backup_name" ]; then
        backup_name="$(date +%Y%m%d_%H%M%S)"
    fi
    
    local backup_path="$BACKUP_DIR/$backup_name"
    
    echo -e "${BLUE}🗃️  Creating backup: $backup_name${NC}"
    
    # Create backup directory
    mkdir -p "$backup_path"
    
    # Backup database
    if [ -f "$ENV_FILE" ]; then
        DB_HOST=$(grep "DB_HOST=" "$ENV_FILE" | cut -d'=' -f2)
        DB_DATABASE=$(grep "DB_DATABASE=" "$ENV_FILE" | cut -d'=' -f2)
        DB_USERNAME=$(grep "DB_USERNAME=" "$ENV_FILE" | cut -d'=' -f2)
        DB_PASSWORD=$(grep "DB_PASSWORD=" "$ENV_FILE" | cut -d'=' -f2)
        
        if [ ! -z "$DB_DATABASE" ] && [ ! -z "$DB_USERNAME" ]; then
            echo -n "📊 Backing up database... "
            if mysqldump -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$backup_path/database.sql" 2>/dev/null; then
                echo -e "${GREEN}✅${NC}"
                log "✅ Database backup created: $backup_path/database.sql"
            else
                echo -e "${RED}❌${NC}"
                log "❌ Database backup failed"
                return 1
            fi
        else
            echo -e "${YELLOW}⚠️  Database credentials not found in $ENV_FILE${NC}"
        fi
    fi
    
    # Backup files
    echo -n "📁 Backing up uploaded files... "
    if [ -d "storage/app/public" ]; then
        cp -r storage/app/public "$backup_path/uploads" 2>/dev/null
        echo -e "${GREEN}✅${NC}"
        log "✅ Files backup created: $backup_path/uploads"
    else
        echo -e "${YELLOW}⚠️  No uploads directory found${NC}"
    fi
    
    # Backup environment file
    echo -n "⚙️  Backing up environment configuration... "
    if [ -f "$ENV_FILE" ]; then
        cp "$ENV_FILE" "$backup_path/env_backup"
        echo -e "${GREEN}✅${NC}"
        log "✅ Environment backup created: $backup_path/env_backup"
    fi
    
    # Backup important logs
    echo -n "📄 Backing up logs... "
    if [ -d "storage/logs" ]; then
        mkdir -p "$backup_path/logs"
        cp storage/logs/*.log "$backup_path/logs/" 2>/dev/null || true
        echo -e "${GREEN}✅${NC}"
    fi
    
    # Create backup metadata
    cat > "$backup_path/backup_info.json" << EOF
{
    "backup_name": "$backup_name",
    "created_at": "$(date '+%Y-%m-%d %H:%M:%S')",
    "app_version": "$(php artisan --version 2>/dev/null || echo 'Unknown')",
    "php_version": "$(php -r 'echo PHP_VERSION;' 2>/dev/null || echo 'Unknown')",
    "environment": "$(grep "APP_ENV=" "$ENV_FILE" 2>/dev/null | cut -d'=' -f2 || echo 'Unknown')",
    "database_size": "$(du -sh "$backup_path/database.sql" 2>/dev/null | cut -f1 || echo 'Unknown')",
    "files_count": "$(find "$backup_path/uploads" -type f 2>/dev/null | wc -l || echo '0')",
    "backup_size": "$(du -sh "$backup_path" 2>/dev/null | cut -f1 || echo 'Unknown')"
}
EOF
    
    # Calculate total backup size
    local backup_size=$(du -sh "$backup_path" | cut -f1)
    
    echo ""
    echo -e "${GREEN}✅ Backup created successfully!${NC}"
    echo "📊 Backup information:"
    echo "  Name: $backup_name"
    echo "  Path: $backup_path"
    echo "  Size: $backup_size"
    echo "  Created: $(date '+%Y-%m-%d %H:%M:%S')"
    
    log "🎉 Backup '$backup_name' created successfully (Size: $backup_size)"
}

restore_backup() {
    local backup_name="$1"
    
    if [ -z "$backup_name" ]; then
        echo -e "${RED}❌ Backup name is required!${NC}"
        echo "Usage: $0 restore <backup_name>"
        echo "Use '$0 list' to see available backups"
        return 1
    fi
    
    local backup_path="$BACKUP_DIR/$backup_name"
    
    if [ ! -d "$backup_path" ]; then
        echo -e "${RED}❌ Backup '$backup_name' not found!${NC}"
        echo "Available backups:"
        list_backups
        return 1
    fi
    
    echo -e "${YELLOW}🔄 Restoring backup: $backup_name${NC}"
    echo -e "${RED}⚠️  WARNING: This will overwrite current data!${NC}"
    echo -n "Continue? [y/N]: "
    read confirm
    
    if [[ ! $confirm =~ ^[Yy]$ ]]; then
        echo "Restore cancelled."
        return 0
    fi
    
    # Create pre-restore backup
    echo -e "${BLUE}📋 Creating pre-restore backup...${NC}"
    create_backup "pre_restore_$(date +%Y%m%d_%H%M%S)"
    
    # Restore database
    if [ -f "$backup_path/database.sql" ]; then
        echo -n "📊 Restoring database... "
        
        if [ -f "$ENV_FILE" ]; then
            DB_HOST=$(grep "DB_HOST=" "$ENV_FILE" | cut -d'=' -f2)
            DB_DATABASE=$(grep "DB_DATABASE=" "$ENV_FILE" | cut -d'=' -f2)
            DB_USERNAME=$(grep "DB_USERNAME=" "$ENV_FILE" | cut -d'=' -f2)
            DB_PASSWORD=$(grep "DB_PASSWORD=" "$ENV_FILE" | cut -d'=' -f2)
            
            if mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < "$backup_path/database.sql" 2>/dev/null; then
                echo -e "${GREEN}✅${NC}"
                log "✅ Database restored from: $backup_path/database.sql"
            else
                echo -e "${RED}❌${NC}"
                log "❌ Database restore failed"
                return 1
            fi
        fi
    fi
    
    # Restore files
    if [ -d "$backup_path/uploads" ]; then
        echo -n "📁 Restoring uploaded files... "
        rm -rf storage/app/public 2>/dev/null || true
        cp -r "$backup_path/uploads" storage/app/public
        echo -e "${GREEN}✅${NC}"
        log "✅ Files restored from: $backup_path/uploads"
    fi
    
    # Restore environment (optional)
    if [ -f "$backup_path/env_backup" ]; then
        echo -n "⚙️  Restore environment configuration? [y/N]: "
        read restore_env
        if [[ $restore_env =~ ^[Yy]$ ]]; then
            cp "$backup_path/env_backup" "$ENV_FILE"
            echo -e "${GREEN}✅ Environment configuration restored${NC}"
            log "✅ Environment restored from: $backup_path/env_backup"
        fi
    fi
    
    # Clear caches after restore
    echo -n "🧹 Clearing caches... "
    php artisan config:clear >/dev/null 2>&1 || true
    php artisan route:clear >/dev/null 2>&1 || true
    php artisan view:clear >/dev/null 2>&1 || true
    php artisan cache:clear >/dev/null 2>&1 || true
    echo -e "${GREEN}✅${NC}"
    
    # Fix permissions
    echo -n "🔐 Fixing permissions... "
    chmod -R 755 storage bootstrap/cache 2>/dev/null || true
    chmod -R 775 storage/app/public 2>/dev/null || true
    echo -e "${GREEN}✅${NC}"
    
    echo ""
    echo -e "${GREEN}✅ Restore completed successfully!${NC}"
    echo "🔍 Please test your application to ensure everything works correctly."
    
    log "🎉 Backup '$backup_name' restored successfully"
}

list_backups() {
    echo -e "${BLUE}📋 Available Backups${NC}"
    echo "==================="
    
    if [ ! -d "$BACKUP_DIR" ] || [ -z "$(ls -A "$BACKUP_DIR" 2>/dev/null)" ]; then
        echo "No backups found."
        return 0
    fi
    
    printf "%-20s %-15s %-10s %-20s\n" "NAME" "SIZE" "FILES" "CREATED"
    printf "%-20s %-15s %-10s %-20s\n" "----" "----" "-----" "-------"
    
    for backup in "$BACKUP_DIR"/*; do
        if [ -d "$backup" ]; then
            local backup_name=$(basename "$backup")
            local backup_size=$(du -sh "$backup" 2>/dev/null | cut -f1 || echo "Unknown")
            local files_count="0"
            local created_date="Unknown"
            
            # Get files count
            if [ -d "$backup/uploads" ]; then
                files_count=$(find "$backup/uploads" -type f 2>/dev/null | wc -l)
            fi
            
            # Get creation date from metadata or directory
            if [ -f "$backup/backup_info.json" ]; then
                created_date=$(grep '"created_at"' "$backup/backup_info.json" | cut -d'"' -f4 2>/dev/null || echo "Unknown")
            else
                created_date=$(stat -c %y "$backup" 2>/dev/null | cut -d'.' -f1 || echo "Unknown")
            fi
            
            printf "%-20s %-15s %-10s %-20s\n" "$backup_name" "$backup_size" "$files_count" "$created_date"
        fi
    done
    
    echo ""
    echo "Usage:"
    echo "  $0 restore <backup_name>  # Restore a specific backup"
    echo "  $0 backup                 # Create a new backup"
}

cleanup_backups() {
    echo -e "${BLUE}🧹 Cleaning up old backups${NC}"
    
    if [ ! -d "$BACKUP_DIR" ]; then
        echo "No backup directory found."
        return 0
    fi
    
    # Count current backups
    local backup_count=$(find "$BACKUP_DIR" -maxdepth 1 -type d ! -path "$BACKUP_DIR" | wc -l)
    
    if [ "$backup_count" -le 10 ]; then
        echo "Only $backup_count backups found. Keeping all (limit: 10)."
        return 0
    fi
    
    echo "Found $backup_count backups. Keeping latest 10..."
    
    # Remove old backups (keep last 10)
    cd "$BACKUP_DIR"
    ls -t | tail -n +11 | while read backup; do
        if [ -d "$backup" ]; then
            local backup_size=$(du -sh "$backup" 2>/dev/null | cut -f1 || echo "Unknown")
            echo "Removing old backup: $backup ($backup_size)"
            rm -rf "$backup"
            log "🗑️  Removed old backup: $backup"
        fi
    done
    cd - >/dev/null
    
    echo -e "${GREEN}✅ Cleanup completed${NC}"
}

# Main script
case "${1:-help}" in
    "backup")
        create_backup "$2"
        ;;
    "restore")
        restore_backup "$2"
        ;;
    "list")
        list_backups
        ;;
    "cleanup")
        cleanup_backups
        ;;
    "help"|*)
        show_help
        ;;
esac