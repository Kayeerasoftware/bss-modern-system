# BSS System Deployment Guide

## Server Requirements

- Ubuntu 20.04 LTS or higher
- PHP 8.2+
- MySQL 8.0+
- Nginx or Apache
- Composer
- Node.js 18+
- SSL Certificate (Let's Encrypt recommended)

## Pre-Deployment Checklist

- [ ] Server provisioned and accessible
- [ ] Domain name configured
- [ ] SSL certificate obtained
- [ ] Database created
- [ ] Backup strategy in place
- [ ] Monitoring tools configured

## Deployment Steps

### 1. Server Setup

#### Update System
```bash
sudo apt update && sudo apt upgrade -y
```

#### Install PHP 8.2
```bash
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
  php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd
```

#### Install MySQL
```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

#### Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### Install Node.js
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Application Deployment

#### Clone Repository
```bash
cd /var/www
sudo git clone <repository-url> bss-system
cd bss-system
```

#### Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/bss-system
sudo chmod -R 755 /var/www/bss-system
sudo chmod -R 775 /var/www/bss-system/storage
sudo chmod -R 775 /var/www/bss-system/bootstrap/cache
```

#### Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

#### Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with production values:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=bss_production
DB_USERNAME=bss_user
DB_PASSWORD=secure_password
```

#### Run Migrations
```bash
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder
```

#### Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 3. Web Server Configuration

#### Nginx Configuration
Create `/etc/nginx/sites-available/bss-system`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/bss-system/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

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

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/bss-system /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 4. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 5. Queue Worker Setup

Create systemd service `/etc/systemd/system/bss-queue.service`:

```ini
[Unit]
Description=BSS Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=5s
ExecStart=/usr/bin/php /var/www/bss-system/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable bss-queue
sudo systemctl start bss-queue
```

### 6. Scheduled Tasks

Add to crontab:
```bash
sudo crontab -e -u www-data
```

Add line:
```
* * * * * cd /var/www/bss-system && php artisan schedule:run >> /dev/null 2>&1
```

### 7. Monitoring & Logging

#### Install Supervisor
```bash
sudo apt install supervisor
```

Create `/etc/supervisor/conf.d/bss-worker.conf`:
```ini
[program:bss-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/bss-system/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/bss-system/storage/logs/worker.log
```

Reload supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bss-worker:*
```

## Backup Strategy

### Database Backup Script
Create `/usr/local/bin/backup-bss-db.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/bss"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="bss_production"
DB_USER="bss_user"
DB_PASS="secure_password"

mkdir -p $BACKUP_DIR
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/bss_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "bss_*.sql.gz" -mtime +30 -delete
```

Make executable and add to cron:
```bash
sudo chmod +x /usr/local/bin/backup-bss-db.sh
sudo crontab -e
```

Add:
```
0 2 * * * /usr/local/bin/backup-bss-db.sh
```

## Zero-Downtime Deployment

### Using Deployer or Envoyer

1. Set up deployment tool
2. Configure deployment script
3. Use symlinks for releases
4. Maintain shared directories (storage, .env)
5. Run migrations before switching
6. Switch symlink atomically

## Rollback Procedure

```bash
# If using symlinks
cd /var/www/bss-system
ln -sfn releases/previous current

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo supervisorctl restart bss-worker:*
```

## Security Hardening

1. **Firewall Configuration**
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

2. **Disable Directory Listing**
Already configured in Nginx

3. **Hide PHP Version**
Edit `/etc/php/8.2/fpm/php.ini`:
```ini
expose_php = Off
```

4. **Rate Limiting**
Configure in Nginx or use Laravel's rate limiting

5. **Regular Updates**
```bash
sudo apt update && sudo apt upgrade
composer update
```

## Monitoring

### Application Monitoring
- Laravel Telescope (development)
- Laravel Horizon (queue monitoring)
- New Relic or Datadog (production)

### Server Monitoring
- Uptime monitoring (UptimeRobot, Pingdom)
- Server metrics (Netdata, Prometheus)
- Log aggregation (ELK Stack, Papertrail)

## Troubleshooting

### Check Logs
```bash
tail -f /var/www/bss-system/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
tail -f /var/log/php8.2-fpm.log
```

### Check Services
```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
sudo supervisorctl status
```

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

## Performance Optimization

1. **Enable OPcache**
2. **Use Redis for cache and sessions**
3. **Enable HTTP/2**
4. **Implement CDN for static assets**
5. **Database query optimization**
6. **Enable Gzip compression**

## Post-Deployment Verification

- [ ] Application accessible via HTTPS
- [ ] All pages load correctly
- [ ] Database connections working
- [ ] Queue workers running
- [ ] Scheduled tasks executing
- [ ] Email notifications working
- [ ] SMS notifications working
- [ ] Payment gateway functional
- [ ] Backups running
- [ ] Monitoring active
