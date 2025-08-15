# 🚀 PANDUAN DEPLOYMENT SMA TUNAS HARAPAN

## 📋 Daftar Isi
1. [Persiapan Server](#persiapan-server)
2. [Upload File ke Server](#upload-file-ke-server) 
3. [Konfigurasi Environment](#konfigurasi-environment)
4. [Deployment Otomatis](#deployment-otomatis)
5. [Deployment Manual](#deployment-manual)
6. [Verifikasi & Testing](#verifikasi--testing)
7. [Maintenance & Monitoring](#maintenance--monitoring)
8. [Troubleshooting](#troubleshooting)

---

## 🖥️ Persiapan Server

### Requirements Minimum
- **OS**: Ubuntu 20.04+ / CentOS 8+ / shared hosting dengan PHP
- **PHP**: 8.2 atau lebih tinggi
- **Database**: MySQL 8.0+ atau MariaDB 10.4+
- **Web Server**: Nginx atau Apache
- **Storage**: Minimal 1GB free space
- **Memory**: Minimal 1GB RAM

### 1. Install Dependencies (VPS/Dedicated Server)

#### Ubuntu/Debian:
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 dan extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath php8.2-cli

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js dan NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Nginx
sudo apt install -y nginx

# Install MySQL
sudo apt install -y mysql-server
```

#### CentOS/RHEL:
```bash
# Install PHP 8.2
sudo dnf install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath

# Install Composer, Node.js, Nginx, MySQL dengan cara yang sama
```

### 2. Setup Database
```sql
# Login ke MySQL
mysql -u root -p

# Buat database dan user
CREATE DATABASE sma_tunas_harapan;
CREATE USER 'sma_user'@'localhost' IDENTIFIED BY 'password_kuat_123';
GRANT ALL PRIVILEGES ON sma_tunas_harapan.* TO 'sma_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 📤 Upload File ke Server

### Opsi 1: Git Clone (Recommended)
```bash
# Clone repository ke server
cd /var/www/
sudo git clone https://github.com/indramaulin76/smath4.git sma-tunas-harapan
sudo chown -R www-data:www-data sma-tunas-harapan
cd sma-tunas-harapan
```

### Opsi 2: Upload via FTP/cPanel
1. Download atau export project sebagai ZIP
2. Upload ke direktori web server (biasanya `public_html` atau `www`)
3. Extract file ZIP

### Opsi 3: rsync (untuk update)
```bash
# Sync file dari local ke server
rsync -avz --delete ./smath4/ user@server:/var/www/sma-tunas-harapan/
```

---

## ⚙️ Konfigurasi Environment

### 1. Copy dan Edit File Environment
```bash
# Copy template production
cp .env.production.example .env

# Edit file .env
nano .env
```

### 2. Konfigurasi Wajib di .env
```bash
# Aplikasi
APP_NAME="SMA Tunas Harapan"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sma_tunas_harapan
DB_USERNAME=sma_user
DB_PASSWORD=password_kuat_123

# Session untuk HTTPS
SESSION_SECURE_COOKIES=true
SESSION_DOMAIN=domain-anda.com

# Email (opsional untuk fitur kontak)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email@domain-anda.com
MAIL_PASSWORD=app_password
MAIL_FROM_ADDRESS=noreply@domain-anda.com
```

---

## 🚀 Deployment Otomatis

### Menggunakan Script Otomatis (Recommended)
```bash
# Pastikan script executable
chmod +x deploy.sh

# Jalankan deployment
./deploy.sh
```

Script akan otomatis:
- ✅ Install dependencies
- ✅ Build assets
- ✅ Generate app key
- ✅ Run migrations
- ✅ Create storage link
- ✅ Cache optimizations
- ✅ Set permissions
- ✅ Health checks

---

## 🔧 Deployment Manual

Jika script otomatis gagal, lakukan langkah manual:

### 1. Install Dependencies
```bash
# PHP dependencies
composer install --no-dev --optimize-autoloader

# Node.js dependencies
npm ci
npm run build
```

### 2. Laravel Setup
```bash
# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link

# Seed database (first time only)
php artisan db:seed
```

### 3. Cache Optimizations
```bash
# Clear existing cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Create production cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 4. File Permissions
```bash
# Set ownership
sudo chown -R www-data:www-data storage bootstrap/cache

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 775 storage/app/public
chmod 600 .env
```

---

## 🌐 Konfigurasi Web Server

### Nginx Configuration
```nginx
# /etc/nginx/sites-available/sma-tunas-harapan
server {
    listen 80;
    listen [::]:80;
    server_name domain-anda.com www.domain-anda.com;
    root /var/www/sma-tunas-harapan/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/sma-tunas-harapan /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Apache Configuration (.htaccess sudah tersedia)
Pastikan mod_rewrite enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## 🔒 Setup SSL (HTTPS)

### Menggunakan Certbot (Free SSL)
```bash
# Install Certbot
sudo apt install snapd
sudo snap install core; sudo snap refresh core
sudo snap install --classic certbot

# Generate SSL certificate
sudo certbot --nginx -d domain-anda.com -d www.domain-anda.com

# Auto-renewal
sudo crontab -e
# Tambahkan: 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## ✅ Verifikasi & Testing

### 1. Health Check
```bash
# Jalankan health check
./health-check.sh
```

### 2. Manual Testing
- ✅ Buka website: `https://domain-anda.com`
- ✅ Test halaman utama load dengan benar
- ✅ Test halaman admin: `https://domain-anda.com/admin`
- ✅ Login dengan: admin@smath.com / password
- ✅ Test upload gambar di admin panel
- ✅ Test responsive design di mobile

### 3. Performance Check
```bash
# Check response time
curl -w "@curl-format.txt" -o /dev/null -s "https://domain-anda.com"

# Check memory usage
php artisan route:list | wc -l
```

---

## 📊 Maintenance & Monitoring

### 1. Log Monitoring
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

### 2. Backup Database
```bash
# Manual backup
mysqldump -u sma_user -p sma_tunas_harapan > backup_$(date +%Y%m%d_%H%M%S).sql

# Automated backup (add to crontab)
0 2 * * * mysqldump -u sma_user -p'password' sma_tunas_harapan > /backups/db_$(date +\%Y\%m\%d_\%H\%M\%S).sql
```

### 3. Update Aplikasi
```bash
# Backup dulu
cp .env .env.backup
mysqldump -u sma_user -p sma_tunas_harapan > backup_before_update.sql

# Pull update dari Git
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Update database
php artisan migrate --force

# Clear dan rebuild cache
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔧 Troubleshooting

### 🚨 Issues Umum

#### 1. "500 Internal Server Error"
```bash
# Check logs
tail -20 storage/logs/laravel.log

# Common fixes:
chmod -R 755 storage bootstrap/cache
php artisan config:clear
php artisan cache:clear
```

#### 2. "Storage link not working"
```bash
# Remove dan recreate symlink
rm public/storage
php artisan storage:link
```

#### 3. "Permission denied"
```bash
# Fix ownership dan permissions
sudo chown -R www-data:www-data .
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public
```

#### 4. "Database connection failed"
```bash
# Test koneksi
php artisan migrate:status

# Check config
php artisan config:show database
```

#### 5. "Assets not loading"
```bash
# Rebuild assets
npm run build
php artisan config:cache
```

### 🆘 Emergency Commands
```bash
# Reset semua cache
php artisan optimize:clear

# Reset permissions
sudo ./fix-permissions.sh

# Restore from backup
mysql -u sma_user -p sma_tunas_harapan < backup_file.sql
```

---

## 📞 Support

### 🔗 Resource Berguna
- **Laravel Documentation**: https://laravel.com/docs
- **Filament Documentation**: https://filamentphp.com/docs
- **Server Requirements**: Lihat `SECURITY_FINAL.md`

### 📧 Bantuan Deployment
Jika mengalami kesulitan deployment, silakan:
1. Jalankan `./health-check.sh` dan kirim hasilnya
2. Check log error di `storage/logs/laravel.log`
3. Pastikan semua requirements terpenuhi

---

## ✨ Tips Deployment

### 🚀 Untuk Performa Terbaik:
1. **Enable OPcache** di PHP
2. **Use Redis** untuk cache dan session
3. **Enable Gzip** compression
4. **Optimize images** sebelum upload
5. **Use CDN** untuk assets static

### 🔒 Untuk Keamanan:
1. **Selalu update** sistem dan dependencies
2. **Backup regular** database dan files
3. **Monitor logs** untuk aktivitas mencurigakan
4. **Use strong passwords** dan 2FA jika tersedia
5. **Hide server info** (server tokens off)

### 💡 Untuk Maintenance:
1. **Schedule downtime** untuk update major
2. **Test di staging** sebelum production
3. **Monitor disk space** dan memory usage
4. **Keep backups** minimal 7 hari terakhir
5. **Document semua changes**

---

🎉 **Selamat! Website SMA Tunas Harapan berhasil di-deploy!**

Website dapat diakses di: `https://domain-anda.com`  
Admin panel di: `https://domain-anda.com/admin`