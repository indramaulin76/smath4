# 🐳 Docker Deployment Guide - SMA Tunas Harapan

## Panduan Deployment menggunakan Docker

Docker memungkinkan deployment yang konsisten dan mudah di berbagai environment. Panduan ini akan membantu Anda men-deploy aplikasi SMA Tunas Harapan menggunakan Docker.

## 📋 Prerequisites

### System Requirements
- **Docker**: 20.10+ 
- **Docker Compose**: 2.0+
- **Memory**: Minimal 2GB RAM
- **Storage**: Minimal 5GB free space

### Install Docker (Ubuntu/Debian)
```bash
# Uninstall old versions
sudo apt-get remove docker docker-engine docker.io containerd runc

# Install dependencies
sudo apt-get update
sudo apt-get install -y apt-transport-https ca-certificates curl gnupg lsb-release

# Add Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Set up stable repository
echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker Engine
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Add user to docker group
sudo usermod -aG docker $USER

# Start Docker service
sudo systemctl start docker
sudo systemctl enable docker
```

### Install Docker Compose
```bash
# Download Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

# Make executable
sudo chmod +x /usr/local/bin/docker-compose

# Verify installation
docker --version
docker-compose --version
```

---

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone https://github.com/indramaulin76/smath4.git
cd smath4
```

### 2. Environment Setup
```bash
# Copy environment file
cp .env.production.example .env

# Edit configuration
nano .env
```

### Konfigurasi Environment Utama:
```bash
# Application
APP_NAME="SMA Tunas Harapan"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (gunakan konfigurasi Docker)
DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=sma_tunas_harapan
DB_USERNAME=root
DB_PASSWORD=strong_password_here

# Cache & Session (gunakan Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379

# Email (opsional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

### 3. Build dan Start Container
```bash
# Build application image
docker-compose build

# Start all services
docker-compose up -d

# Check status
docker-compose ps
```

### 4. Initialize Application
```bash
# Generate application key
docker-compose exec app php artisan key:generate --force

# Run database migrations
docker-compose exec app php artisan migrate --force

# Seed database with sample data
docker-compose exec app php artisan db:seed --force

# Create storage symlink
docker-compose exec app php artisan storage:link
```

### 5. Verify Deployment
```bash
# Check application logs
docker-compose logs app

# Test website
curl -I http://localhost

# Access admin panel
# http://localhost/admin
# Email: admin@smath.com
# Password: password
```

---

## 🔧 Advanced Configuration

### Custom Domain Setup

#### Update docker-compose.yml:
```yaml
services:
  app:
    environment:
      - APP_URL=https://yourdomain.com
    ports:
      - "80:80"
      - "443:443"  # for HTTPS
```

#### SSL Certificate dengan Let's Encrypt:
```bash
# Install Certbot
sudo apt install certbot

# Stop containers temporarily
docker-compose down

# Generate certificate
sudo certbot certonly --standalone -d yourdomain.com

# Copy certificates to project
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem ./docker/ssl/
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem ./docker/ssl/

# Update nginx configuration for HTTPS
# Edit docker/nginx.conf to include SSL settings

# Restart containers
docker-compose up -d
```

### Database Backup
```bash
# Manual backup
docker-compose exec database mysqldump -u root -p sma_tunas_harapan > backup_$(date +%Y%m%d_%H%M%S).sql

# Automated backup script
cat > backup-db.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="./database/backups"
mkdir -p $BACKUP_DIR
docker-compose exec -T database mysqldump -u root -ppassword sma_tunas_harapan > $BACKUP_DIR/backup_$(date +%Y%m%d_%H%M%S).sql
# Keep only last 7 backups
ls -t $BACKUP_DIR/backup_*.sql | tail -n +8 | xargs rm -f
EOF

chmod +x backup-db.sh

# Add to crontab for daily backup
echo "0 2 * * * /path/to/your/project/backup-db.sh" | crontab -
```

### Performance Optimization

#### Update docker-compose.yml untuk production:
```yaml
services:
  app:
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    deploy:
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M

  database:
    environment:
      - MYSQL_INNODB_BUFFER_POOL_SIZE=512M
    deploy:
      resources:
        limits:
          memory: 1G
        reservations:
          memory: 512M

  redis:
    command: redis-server --maxmemory 128mb --maxmemory-policy allkeys-lru
```

---

## 📊 Monitoring dan Maintenance

### Check Container Health
```bash
# Container status
docker-compose ps

# Resource usage
docker stats

# Container logs
docker-compose logs app
docker-compose logs database
docker-compose logs redis

# Follow logs in real-time
docker-compose logs -f app
```

### Application Maintenance
```bash
# Clear Laravel caches
docker-compose exec app php artisan optimize:clear

# Update application
git pull
docker-compose build
docker-compose up -d

# Database maintenance
docker-compose exec app php artisan migrate --force

# Rebuild assets
docker-compose exec app npm run build
```

### Database Management
```bash
# Access MySQL shell
docker-compose exec database mysql -u root -p sma_tunas_harapan

# Access Redis CLI
docker-compose exec redis redis-cli

# PHPMyAdmin (jika enabled)
# http://localhost:8080
```

---

## 🔒 Security Best Practices

### Environment Security
```bash
# Set secure file permissions
chmod 600 .env
chmod 600 docker-compose.yml

# Use Docker secrets for sensitive data
echo "strong_db_password" | docker secret create db_password -
```

### Container Security
```bash
# Update base images regularly
docker-compose pull
docker-compose build --no-cache

# Scan for vulnerabilities
docker scan sma-tunas-harapan:latest

# Remove unused images
docker image prune -a
```

### Network Security
```bash
# Use custom networks
docker network create sma-network --driver bridge

# Firewall rules (Ubuntu/Debian)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## 🚨 Troubleshooting

### Common Issues

#### 1. "Port already in use"
```bash
# Check what's using the port
sudo netstat -tulpn | grep :80

# Stop conflicting service
sudo systemctl stop apache2  # or nginx

# Or change port in docker-compose.yml
ports:
  - "8080:80"  # Use port 8080 instead
```

#### 2. "Database connection failed"
```bash
# Check database container
docker-compose logs database

# Verify database is running
docker-compose exec database mysql -u root -p -e "SELECT 1"

# Reset database
docker-compose down
docker volume rm smath4_mysql_data
docker-compose up -d
```

#### 3. "Permission denied" errors
```bash
# Fix file permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 755 storage bootstrap/cache
```

#### 4. "Memory limit exceeded"
```bash
# Increase memory limits in docker-compose.yml
deploy:
  resources:
    limits:
      memory: 1G

# Or increase PHP memory limit in docker/php.ini
memory_limit = 512M
```

### Emergency Recovery
```bash
# Stop all containers
docker-compose down

# Remove all containers and volumes (⚠️  DATA LOSS)
docker-compose down -v

# Rebuild from scratch
docker-compose build --no-cache
docker-compose up -d

# Restore from backup
docker-compose exec -T database mysql -u root -p sma_tunas_harapan < backup_file.sql
```

---

## 📚 Useful Commands

### Docker Management
```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Restart specific service
docker-compose restart app

# View logs
docker-compose logs -f app

# Execute commands in container
docker-compose exec app bash
docker-compose exec app php artisan migrate

# Scale services
docker-compose up -d --scale app=2
```

### Laravel Commands in Container
```bash
# Artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# Composer commands
docker-compose exec app composer install
docker-compose exec app composer update

# NPM commands
docker-compose exec app npm install
docker-compose exec app npm run build
```

### System Monitoring
```bash
# Container resource usage
docker stats --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.NetIO}}\t{{.BlockIO}}"

# Disk usage
docker system df

# Clean up unused resources
docker system prune -a
```

---

## 🎯 Production Checklist

### Pre-deployment
- [ ] Domain dan DNS configured
- [ ] SSL certificate ready
- [ ] Database backup strategy
- [ ] Monitoring setup
- [ ] Firewall configured

### Post-deployment
- [ ] Test website functionality
- [ ] Verify admin panel access
- [ ] Check SSL certificate
- [ ] Test contact forms
- [ ] Monitor performance
- [ ] Verify backup system
- [ ] Check error logs

### Ongoing Maintenance
- [ ] Regular security updates
- [ ] Database backups
- [ ] Log rotation
- [ ] Performance monitoring
- [ ] Capacity planning

---

## 📞 Support

### Dokumentasi Tambahan
- **Docker Documentation**: https://docs.docker.com/
- **Laravel in Docker**: https://laravel.com/docs/sail
- **Docker Compose Reference**: https://docs.docker.com/compose/

### Troubleshooting
Jika mengalami masalah:
1. Check container logs: `docker-compose logs app`
2. Verify environment configuration
3. Check database connectivity
4. Review file permissions
5. Monitor resource usage

---

🎉 **Selamat! Aplikasi SMA Tunas Harapan berhasil di-deploy dengan Docker!**

Website dapat diakses di: `http://localhost` atau domain Anda  
Admin panel di: `http://localhost/admin`  
PHPMyAdmin: `http://localhost:8080` (jika enabled)