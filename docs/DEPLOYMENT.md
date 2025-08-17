# Deployment Guide

## Production Deployment

### Prerequisites
- PHP 7.4+ with extensions: PDO, MySQL/SQLite, mbstring
- Web server (Apache/Nginx)
- MySQL 5.7+ or MariaDB 10.2+
- Composer

### 1. Server Setup

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/email-marketing/public
    
    <Directory /path/to/email-marketing/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security headers
    Header always set X-Frame-Options DENY
    Header always set X-Content-Type-Options nosniff
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/email-marketing/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
}
```

### 2. File Permissions
```bash
# Set proper permissions
chown -R www-data:www-data /path/to/email-marketing
chmod -R 755 /path/to/email-marketing
chmod -R 777 /path/to/email-marketing/storage
```

### 3. Environment Configuration

Create `.env` file:
```env
# Production Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=bulk_mailer
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# SMTP Configuration
SMTP_HOST=smtp.yourserver.com
SMTP_PORT=587
SMTP_USERNAME=your_email@yourdomain.com
SMTP_PASSWORD=your_app_password
SMTP_ENCRYPTION=tls
SMTP_FROM_EMAIL=noreply@yourdomain.com
SMTP_FROM_NAME="Your Email Marketing Tool"

# Security (Production)
APP_DEBUG=false
APP_ENV=production
```

### 4. Database Setup
```bash
# Create production database
mysql -u root -p
CREATE DATABASE bulk_mailer;
GRANT ALL PRIVILEGES ON bulk_mailer.* TO 'your_user'@'localhost' IDENTIFIED BY 'your_password';
FLUSH PRIVILEGES;

# Run migrations
php database/migrate.php
```

### 5. Security Checklist

#### File Security
```bash
# Protect sensitive files
chmod 600 .env
chmod 600 config/*.php

# Remove development files
rm -f QUICKSTART.md
rm -f storage/database.sqlite
```

#### Configuration Updates
- Set `APP_DEBUG=false` in .env
- Enable HTTPS and update security headers
- Configure firewall rules
- Set up SSL certificates
- Enable secure cookies

### 6. Performance Optimization

#### PHP Configuration (php.ini)
```ini
# Memory and execution limits
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 10M
post_max_size = 10M

# OPcache (recommended)
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

#### Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_email_logs_campaign_created ON email_logs(campaign_id, created_at);
CREATE INDEX idx_campaigns_user_status ON campaigns(user_id, status);
```

### 7. Monitoring & Maintenance

#### Log Rotation
```bash
# Add to crontab
0 0 * * 0 find /path/to/email-marketing/storage/logs -name "*.log" -mtime +30 -delete
```

#### Backup Strategy
```bash
#!/bin/bash
# Daily backup script
mysqldump -u user -p bulk_mailer > backup_$(date +%Y%m%d).sql
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/
```

#### Health Checks
- Monitor disk space in `storage/` directory
- Check database connection regularly
- Monitor SMTP connectivity
- Review error logs weekly

### 8. SSL Certificate (Let's Encrypt)
```bash
# Install certbot
sudo apt install certbot python3-certbot-apache

# Obtain certificate
sudo certbot --apache -d yourdomain.com

# Auto-renewal
sudo crontab -e
0 12 * * * /usr/bin/certbot renew --quiet
```

### 9. Scaling Considerations

For high-volume deployments:
- Use Redis for session storage
- Implement queue system for email sending
- Consider MySQL master-slave replication
- Use CDN for static assets
- Implement horizontal scaling with load balancer

### 10. Security Hardening

```bash
# Disable directory browsing
echo "Options -Indexes" > public/.htaccess

# Hide PHP version
echo "expose_php = Off" >> /etc/php/8.1/apache2/php.ini

# Fail2ban for SSH protection
sudo apt install fail2ban
```

---

## Troubleshooting

### Common Issues

**500 Internal Server Error**
- Check file permissions
- Review error logs
- Verify .htaccess configuration

**Database Connection Failed**
- Verify credentials in .env
- Check MySQL service status
- Confirm database exists

**Email Sending Issues**
- Test SMTP credentials
- Check firewall rules for SMTP ports
- Verify DNS records

### Support
For production deployment assistance, contact your system administrator or hosting provider.