# HRM-Pro Deployment Guide

## 🚀 Production Deployment Checklist

### Pre-Deployment

#### 1. Server Requirements
- **PHP**: 8.2 or higher
- **MySQL**: 8.0 or higher
- **Composer**: Latest version
- **Node.js**: 18.x or higher
- **Web Server**: Apache/Nginx
- **SSL Certificate**: Required for production

#### 2. Server Configuration

**PHP Extensions Required:**
```bash
php -m | grep -E 'pdo|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|fileinfo|gd'
```

**PHP.ini Settings:**
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

### Installation Steps

#### 1. Clone Repository
```bash
cd /var/www
git clone <repository-url> hrm-pro
cd hrm-pro
```

#### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

#### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

**Edit .env file:**
```env
APP_NAME="HRM-Pro"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://hrm-pro.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrm_pro_production
DB_USERNAME=hrm_user
DB_PASSWORD=<strong-password>

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=<your-username>
MAIL_PASSWORD=<your-password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@hrm-pro.com"
MAIL_FROM_NAME="${APP_NAME}"

# Multi-Tenancy
TENANT_IDENTIFICATION=subdomain
APP_DOMAIN=hrm-pro.com

# Payroll
PAYROLL_OVERTIME_MULTIPLIER=1.5
PAYROLL_DEFAULT_WORKING_HOURS=8
```

#### 4. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE hrm_pro_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hrm_user'@'localhost' IDENTIFIED BY '<strong-password>';
GRANT ALL PRIVILEGES ON hrm_pro_production.* TO 'hrm_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force

# Seed super admin
php artisan db:seed --class=SuperAdminSeeder
```

#### 5. File Permissions
```bash
chown -R www-data:www-data /var/www/hrm-pro
chmod -R 755 /var/www/hrm-pro
chmod -R 775 /var/www/hrm-pro/storage
chmod -R 775 /var/www/hrm-pro/bootstrap/cache
```

#### 6. Storage Setup
```bash
php artisan storage:link
```

#### 7. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name hrm-pro.com *.hrm-pro.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name hrm-pro.com *.hrm-pro.com;
    root /var/www/hrm-pro/public;

    ssl_certificate /etc/ssl/certs/hrm-pro.crt;
    ssl_certificate_key /etc/ssl/private/hrm-pro.key;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Configuration (.htaccess already included)
```apache
<VirtualHost *:80>
    ServerName hrm-pro.com
    ServerAlias *.hrm-pro.com
    Redirect permanent / https://hrm-pro.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName hrm-pro.com
    ServerAlias *.hrm-pro.com
    DocumentRoot /var/www/hrm-pro/public

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/hrm-pro.crt
    SSLCertificateKeyFile /etc/ssl/private/hrm-pro.key

    <Directory /var/www/hrm-pro/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/hrm-pro-error.log
    CustomLog ${APACHE_LOG_DIR}/hrm-pro-access.log combined
</VirtualHost>
```

### Queue Worker Setup

#### Supervisor Configuration
Create `/etc/supervisor/conf.d/hrm-pro-worker.conf`:
```ini
[program:hrm-pro-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/hrm-pro/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/hrm-pro/storage/logs/worker.log
stopwaitsecs=3600
```

**Start supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start hrm-pro-worker:*
```

### Cron Job Setup

Add to crontab:
```bash
sudo crontab -e -u www-data
```

Add this line:
```cron
* * * * * cd /var/www/hrm-pro && php artisan schedule:run >> /dev/null 2>&1
```

### Security Hardening

#### 1. Disable Debug Mode
```env
APP_DEBUG=false
```

#### 2. Set Strong APP_KEY
```bash
php artisan key:generate
```

#### 3. Configure CORS (if needed)
```bash
composer require fruitcake/laravel-cors
```

#### 4. Enable Rate Limiting
Already configured in routes with throttle middleware.

#### 5. Database Encryption
Sensitive fields (banking details, salary) use Laravel encryption.

#### 6. Firewall Rules
```bash
# Allow HTTP/HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow SSH
sudo ufw allow 22/tcp

# Enable firewall
sudo ufw enable
```

### Monitoring & Logging

#### 1. Laravel Telescope (Development Only)
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

#### 2. Log Rotation
Create `/etc/logrotate.d/hrm-pro`:
```
/var/www/hrm-pro/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

#### 3. Application Monitoring
Consider using:
- **Sentry** for error tracking
- **New Relic** for performance monitoring
- **Uptime Robot** for uptime monitoring

### Backup Strategy

#### 1. Database Backup Script
Create `/usr/local/bin/backup-hrm-db.sh`:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/hrm-pro"
mkdir -p $BACKUP_DIR

mysqldump -u hrm_user -p<password> hrm_pro_production | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete
```

#### 2. File Backup
```bash
# Backup storage directory
tar -czf /var/backups/hrm-pro/storage_$(date +%Y%m%d).tar.gz /var/www/hrm-pro/storage
```

#### 3. Automated Backups (Cron)
```cron
0 2 * * * /usr/local/bin/backup-hrm-db.sh
0 3 * * 0 tar -czf /var/backups/hrm-pro/storage_$(date +%Y%m%d).tar.gz /var/www/hrm-pro/storage
```

### Post-Deployment

#### 1. Create First Tenant
```bash
php artisan db:seed --class=DemoTenantSeeder
```

#### 2. Test Subdomain Routing
Visit: `https://acme.hrm-pro.com`

#### 3. Test Biometric API
```bash
curl -X POST https://hrm-pro.com/api/biometric-push \
  -H "Authorization: Bearer <device-token>" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": "BIO001",
    "employee_code": "EMP001",
    "timestamp": "2025-11-27 09:00:00"
  }'
```

#### 4. Verify Scheduled Tasks
```bash
php artisan schedule:list
```

### Troubleshooting

#### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

#### Check Logs
```bash
tail -f storage/logs/laravel.log
```

#### Queue Issues
```bash
php artisan queue:restart
sudo supervisorctl restart hrm-pro-worker:*
```

#### Permission Issues
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 🔐 Default Credentials

**Super Admin:**
- Email: admin@hrm-pro.com
- Password: password

**Demo Tenant (Acme Corp):**
- URL: https://acme.hrm-pro.com
- Email: admin@acme.com
- Password: password

**⚠️ IMPORTANT: Change all default passwords immediately after deployment!**

---

## 📞 Support

For deployment issues:
- Email: support@hrm-pro.com
- Documentation: https://docs.hrm-pro.com
- GitHub Issues: https://github.com/yourorg/hrm-pro/issues
