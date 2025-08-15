# 🔧 Troubleshooting Guide - SMA Tunas Harapan

## Panduan Mengatasi Masalah Deployment

Panduan ini membantu mendiagnosis dan mengatasi masalah umum yang mungkin terjadi saat deployment atau operasional website SMA Tunas Harapan.

---

## 🚨 Quick Diagnosis

### Jalankan Health Check
```bash
# Comprehensive health check
./health-check.sh

# Check system requirements
./check-requirements.sh

# Check deployment logs
tail -20 storage/logs/deployment.log

# Check Laravel logs
tail -20 storage/logs/laravel.log
```

---

## ❌ Common Issues & Solutions

### 1. **500 Internal Server Error**

#### Gejala:
- Website menampilkan halaman error 500
- "The website is temporarily unable to service your request"

#### Diagnosis:
```bash
# Check Laravel logs
tail -50 storage/logs/laravel.log

# Check web server logs
sudo tail -20 /var/log/nginx/error.log
# or
sudo tail -20 /var/log/apache2/error.log

# Check PHP-FPM logs
sudo tail -20 /var/log/php8.2-fpm.log
```

#### Solusi:
```bash
# 1. Clear all caches
php artisan optimize:clear

# 2. Fix permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public
chmod -R 775 storage/logs

# 3. Set ownership (if using web server)
sudo chown -R www-data:www-data storage bootstrap/cache

# 4. Check .env file
php artisan config:show database

# 5. Generate app key if missing
php artisan key:generate --force
```

### 2. **Database Connection Failed**

#### Gejala:
- "SQLSTATE[HY000] [2002] Connection refused"
- "Database connection failed"

#### Diagnosis:
```bash
# Test database connection
php artisan migrate:status

# Check database service
sudo systemctl status mysql
# or
sudo systemctl status mariadb

# Test manual connection
mysql -h localhost -u username -p database_name
```

#### Solusi:
```bash
# 1. Start database service
sudo systemctl start mysql
sudo systemctl enable mysql

# 2. Check database credentials in .env
grep "DB_" .env

# 3. Create database if not exists
mysql -u root -p
CREATE DATABASE sma_tunas_harapan;
CREATE USER 'sma_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON sma_tunas_harapan.* TO 'sma_user'@'localhost';
FLUSH PRIVILEGES;

# 4. Update .env with correct credentials
nano .env

# 5. Test connection again
php artisan migrate:status
```

### 3. **Storage/Upload Issues**

#### Gejala:
- Gambar tidak muncul
- Upload file gagal
- "The link has been created" but images not showing

#### Diagnosis:
```bash
# Check storage symlink
ls -la public/storage

# Check permissions
ls -la storage/app/public

# Check disk space
df -h
```

#### Solusi:
```bash
# 1. Recreate storage symlink
rm public/storage
php artisan storage:link

# 2. Fix permissions
chmod -R 775 storage/app/public
chmod -R 755 storage

# 3. Set ownership
sudo chown -R www-data:www-data storage/app/public

# 4. Check web server config points to public/
# Nginx: root /var/www/html/public;
# Apache: DocumentRoot /var/www/html/public
```

### 4. **Permission Denied Errors**

#### Gejala:
- "Permission denied" in logs
- Cannot write to storage directory
- Cache files cannot be created

#### Solusi:
```bash
# Fix all permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 775 storage/app/public
chmod -R 775 storage/framework
chmod -R 775 storage/logs
chmod 600 .env

# Set ownership to web server user
sudo chown -R www-data:www-data storage bootstrap/cache

# For SELinux systems (CentOS/RHEL)
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_unified 1
```

### 5. **Assets Not Loading (CSS/JS)**

#### Gejala:
- Website tampil tanpa styling
- JavaScript tidak berfungsi
- 404 error untuk asset files

#### Diagnosis:
```bash
# Check if assets exist
ls -la public/build/

# Check build directory
ls -la public/build/assets/

# Check web server access
curl -I http://yoursite.com/build/assets/app-xyz.css
```

#### Solusi:
```bash
# 1. Rebuild assets
npm ci
npm run build

# 2. Check Vite configuration
cat vite.config.js

# 3. Clear Laravel caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Check .env APP_URL matches your domain
grep APP_URL .env

# 5. For production, cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. **Admin Panel Login Issues**

#### Gejala:
- Cannot access /admin
- Login credentials not working
- 404 error on admin routes

#### Diagnosis:
```bash
# Check admin user exists
php artisan tinker
>>> App\Models\User::where('email', 'admin@smath.com')->first();

# Check routes
php artisan route:list | grep admin
```

#### Solusi:
```bash
# 1. Seed admin user
php artisan db:seed --class=UserSeeder

# 2. Or create manually
php artisan tinker
>>> App\Models\User::create(['name' => 'Admin', 'email' => 'admin@smath.com', 'password' => bcrypt('password')]);

# 3. Reset password
php artisan tinker
>>> $user = App\Models\User::where('email', 'admin@smath.com')->first();
>>> $user->password = bcrypt('newpassword');
>>> $user->save();

# 4. Clear route cache
php artisan route:clear
php artisan route:cache
```

### 7. **SSL/HTTPS Issues**

#### Gejala:
- "Not secure" warning in browser
- Mixed content errors
- SSL certificate errors

#### Diagnosis:
```bash
# Check SSL certificate
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com

# Check certificate expiry
echo | openssl s_client -connect yourdomain.com:443 2>/dev/null | openssl x509 -noout -dates
```

#### Solusi:
```bash
# 1. Install Let's Encrypt certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# 2. Update .env for HTTPS
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIES=true

# 3. Force HTTPS in web server
# Nginx - add to server block:
return 301 https://$server_name$request_uri;

# 4. Clear config cache
php artisan config:clear
php artisan config:cache
```

### 8. **Performance Issues**

#### Gejala:
- Website loading lambat
- High server load
- Memory exhausted errors

#### Diagnosis:
```bash
# Check server resources
htop
df -h
free -h

# Check PHP memory limit
php -i | grep memory_limit

# Check slow queries
sudo tail -20 /var/log/mysql/slow.log
```

#### Solusi:
```bash
# 1. Enable PHP OPcache
# Add to PHP configuration:
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000

# 2. Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Use Redis for caching
# Update .env:
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1

# 4. Optimize database
php artisan tinker
>>> DB::statement('OPTIMIZE TABLE table_name');

# 5. Enable Gzip in web server
# Nginx:
gzip on;
gzip_types text/css application/javascript text/javascript application/json;
```

---

## 🛠️ Diagnostic Tools

### Laravel Debugging
```bash
# Enable debug mode temporarily (ONLY for troubleshooting)
# In .env: APP_DEBUG=true (NEVER in production!)

# Check Laravel configuration
php artisan config:show

# List all routes
php artisan route:list

# Check database status
php artisan migrate:status

# Laravel Tinker for testing
php artisan tinker
```

### System Monitoring
```bash
# Monitor real-time logs
tail -f storage/logs/laravel.log

# Monitor web server logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# Monitor system resources
watch -n 1 'free -h && df -h'

# Monitor PHP processes
ps aux | grep php

# Monitor database connections
mysqladmin processlist
```

### Network Diagnostics
```bash
# Test domain resolution
nslookup yourdomain.com

# Test HTTP response
curl -I http://yourdomain.com

# Test HTTPS response
curl -I https://yourdomain.com

# Check open ports
netstat -tulpn | grep :80
netstat -tulpn | grep :443
```

---

## 🚨 Emergency Recovery

### Quick Recovery Commands
```bash
# 1. Stop services
sudo systemctl stop nginx
sudo systemctl stop php8.2-fpm

# 2. Restore from backup (if available)
./backup-rollback.sh restore backup_name

# 3. Reset permissions
chmod -R 755 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# 4. Clear all caches
php artisan optimize:clear

# 5. Restart services
sudo systemctl start php8.2-fpm
sudo systemctl start nginx
```

### Complete Reset (Last Resort)
```bash
# ⚠️ WARNING: This will reset everything!

# 1. Backup current state
./backup-rollback.sh backup emergency_backup

# 2. Reset database
php artisan migrate:fresh --seed

# 3. Clear all caches and storage
php artisan optimize:clear
rm -rf storage/app/public/*
php artisan storage:link

# 4. Rebuild assets
npm ci
npm run build

# 5. Recache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📞 Getting Help

### Information to Collect
When seeking help, provide:

1. **Error Details:**
   ```bash
   # Copy exact error message
   tail -20 storage/logs/laravel.log
   ```

2. **System Information:**
   ```bash
   ./check-requirements.sh
   php --version
   composer --version
   npm --version
   ```

3. **Configuration:**
   ```bash
   # Environment (remove sensitive data)
   grep -v "PASSWORD\|KEY\|SECRET" .env
   ```

4. **Server Logs:**
   ```bash
   sudo tail -20 /var/log/nginx/error.log
   ```

### Support Resources
- **Documentation**: PANDUAN_DEPLOYMENT.md
- **Laravel Docs**: https://laravel.com/docs
- **Filament Docs**: https://filamentphp.com/docs

### Professional Support
Untuk bantuan profesional:
1. Kumpulkan informasi di atas
2. Jelaskan langkah yang sudah dicoba
3. Sertakan screenshot jika ada masalah UI

---

## ✅ Prevention Tips

### Regular Maintenance
```bash
# Weekly maintenance script
cat > weekly-maintenance.sh << 'EOF'
#!/bin/bash
echo "🔧 Weekly Maintenance - $(date)"

# 1. Create backup
./backup-rollback.sh backup weekly_$(date +%Y%m%d)

# 2. Update system
sudo apt update && sudo apt upgrade -y

# 3. Clear logs
php artisan log:clear

# 4. Optimize database
php artisan optimize

# 5. Check disk space
df -h

echo "✅ Maintenance completed"
EOF

chmod +x weekly-maintenance.sh
```

### Monitoring Setup
```bash
# Add to crontab for monitoring
crontab -e

# Add these lines:
# Health check every hour
0 * * * * /path/to/project/health-check.sh --quiet >> /var/log/sma-health.log 2>&1

# Weekly backup
0 2 * * 0 /path/to/project/backup-rollback.sh backup weekly_$(date +%Y%m%d)

# Daily log rotation
0 1 * * * find /path/to/project/storage/logs -name "*.log" -size +100M -exec gzip {} \;
```

---

🎯 **Remember**: Always backup before making changes and test in a staging environment when possible!

📞 **Emergency Contact**: Dokumentasikan kontak sistem administrator untuk emergency support.

🔄 **Keep This Updated**: Update panduan ini sesuai dengan perubahan sistem dan masalah baru yang ditemukan.