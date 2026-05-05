# Ntsava CDN Setup Guide

## Complete Installation and Configuration Guide

This guide covers everything from server requirements to fully operational CDN with global edge caching.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation](#installation)
3. [Environment Configuration](#environment-configuration)
4. [Database Setup](#database-setup)
5. [Subdomain Configuration](#subdomain-configuration)
6. [DNS Configuration](#dns-configuration)
7. [CDN Integration](#cdn-integration)
8. [Verification](#verification)
9. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### System Requirements

| Component | Minimum | Recommended | Notes |
|-----------|---------|-------------|-------|
| PHP | 8.1 | 8.3+ | Required extensions: gd, pdo, mysql/pgsql, curl, json, mbstring, zip |
| MySQL | 5.7 | 8.0+ | Or MariaDB 10.2+ / PostgreSQL 12+ |
| RAM | 1GB | 2GB+ | More for image processing |
| CPU | 1 core | 2 cores+ | Image resizing is CPU-intensive |
| Storage | 10GB | 50GB+ | Depends on expected file uploads |

### Required PHP Extensions

```bash
# Debian/Ubuntu
apt install -y php8.1-cli php8.1-fpm php8.1-common \
    php8.1-mysql php8.1-pgsql php8.1-gd \
    php8.1-curl php8.1-json php8.1-mbstring \
    php8.1-zip php8.1-opcache

# CentOS/RHEL
dnf install -y php81-cli php81-fpm php81-common \
    php81-mysqlnd php81-pgsql php81-gd \
    php81-curl php81-json php81-mbstring \
    php81-zip php81-opcache

# Verify installation
php -v
php -m | grep -E "gd|pdo_mysql|pdo_pgsql|curl"
```

### Domain Requirements

```
You need:
  - 1 main domain (e.g., yourdomain.com)
  - Ability to create subdomains:
    - api.yourdomain.com (for API endpoints)
    - cdn.yourdomain.com (for static file serving)
  - DNS management access
  - SSL certificates (free from Let's Encrypt or Cloudflare)

Optional but recommended:
  - storage.yourdomain.com (if separating storage)
  - resize.yourdomain.com (dedicated image processor)
```

### Supported Databases

| Database | Version | Notes |
|----------|---------|-------|
| MySQL | 5.7+ | Fully supported (production) |
| MariaDB | 10.2+ | Fully supported (production) |
| SQLite | 3.35+ | Development only |

---

## Installation

### Step 1: Clone Repository

```bash
# From GitHub
git clone https://github.com/lizzyman04/ntsava-app
cd ntsava-app

# Or download release
wget https://github.com/lizzyman04/ntsava-app/archive/v1.0.0.tar.gz
tar -xzf v1.0.0.tar.gz
cd ntsava-app-1.0.0
```

### Step 2: Install Composer Dependencies

```bash
# Install Composer if not present
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install dependencies
composer install --no-dev --optimize-autoloader

# For development environment
composer install
```

### Step 3: Set Directory Permissions

```bash
# Linux/Unix
chmod -R 755 storage/
chmod -R 755 public/

# Set ownership (adjust user/group to your web server user)
chown -R www-data:www-data storage/
chown -R www-data:www-data public/

# Shared hosting (often requires 755 or 775)
chmod 775 storage/
chmod 775 public/
```

### Step 4: Create Required Directories

```bash
# Create base storage directory
# Note: storage/cache is auto-created by the image resize service on first use
mkdir -p storage/u

# Set permissions
chmod -R 755 storage/
```

---

## Environment Configuration

### Step 1: Create Environment File

```bash
cp .env.example .env
nano .env  # or vim .env, or use your preferred editor
```

### Step 2: Configure Basic Settings

```env
# Application Configuration
APP_NAME="Ntsava App"
APP_ENV=production
APP_DEBUG=false
APP_PORT=80
APP_TIMEZONE=UTC

# Authentication
# AUTH_SECRET_KEY=
# AUTH_SESSION_EXPIRY=1800
# AUTH_REMEMBER_EXPIRY=2592000

# Mail Configuration
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=
# MAIL_PASSWORD=
# MAIL_FROM_ADDRESS=noreply@example.com
# MAIL_FROM_NAME="Ntsava App"

# Upload Configuration (DEPRECATED)
# Upload limits are now configured per plan in the database.
# Run `composer seed` to insert default plan limits.
```

Generate secure keys:

```bash
# Generate random strings for secrets
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
# Copy output to APP_SECRET

php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
# Copy output to AUTH_SECRET_KEY
```

### Step 3: Configure Domains

```env
# CDN Domains (Critical - these must match your DNS)
CDN_URL=https://cdn.yourdomain.com
API_URL=https://api.yourdomain.com

# Optional: Separate storage domain
STORAGE_URL=https://storage.yourdomain.com
```

### Step 4: Configure Database

Choose your database:

**Option A: MySQL / MariaDB**

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ntsava_cdn
DB_USERNAME=ntsava_user
DB_PASSWORD=your_secure_password
```

**Option B: SQLite (Development Only)**

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### Step 5: Configure Image Processing

Upload limits (max file size and allowed types) are **configured per plan in the database**, not via environment variables. `UPLOAD_MAX_SIZE` and `UPLOAD_ALLOWED_TYPES` are deprecated and have no effect. Run `composer seed` after migrations to populate default plan limits.

```env
# Image processing
IMAGE_QUALITY=85
IMAGE_MAX_WIDTH=4096
IMAGE_MAX_HEIGHT=4096
```

### Step 6: Configure Email (Optional)

```env
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@yourdomain.com
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@yourdomain.com
MAIL_FROM_NAME="Ntsava CDN"
```

---

## Database Setup

### Step 1: Create Database

**MySQL / MariaDB:**

```sql
CREATE DATABASE ntsava_cdn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ntsava_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON ntsava_cdn.* TO 'ntsava_user'@'localhost';
FLUSH PRIVILEGES;
```

**PostgreSQL:**

```sql
CREATE DATABASE ntsava_cdn;
CREATE USER ntsava_user WITH PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE ntsava_cdn TO ntsava_user;
\c ntsava_cdn
GRANT ALL ON SCHEMA public TO ntsava_user;
```

### Step 2: Run Migrations

```bash
# Run all migrations (Phinx)
composer migrate

# Check migration status
composer migrate:status

# Verify tables were created
# MySQL:
mysql -u root -p -e "USE ntsava_cdn; SHOW TABLES;"

# PostgreSQL:
psql -U ntsava_user -d ntsava_cdn -c "\dt"
```

Expected tables:
- users
- files
- api_tokens
- plans
- credits
- credit_transactions
- notifications
- user_roles

### Step 3: Seed Default Data

```bash
# Seed default plans (Free, Plus, Pro, Business)
composer seed
```

### Default Plans Created

| Plan | Slug | Storage | Bandwidth | Max File Size | Allowed Types | Price (MZN) |
|------|------|---------|-----------|---------------|---------------|-------------|
| Free | free | 1 GB | 20 GB | 100 MB | jpg, jpeg, png, gif, webp | 0 |
| Plus | plus | 5 GB | 50 GB | 500 MB | + pdf, doc, docx | 100 |
| Pro | pro | 50 GB | 200 GB | 1 GB | + zip | 500 |
| Business | business | Unlimited | Unlimited | 2.5 GB | + mp4, mov | Custom |

---

## Subdomain Configuration

### Option A: cPanel / DirectAdmin (Shared Hosting)

**Subdomain 1 - API:**

```
Domain: api.yourdomain.com
Document Root: /home/username/public_html/ntsava-app/public
PHP Version: 8.1 or higher
PHP Execution: Enabled
```

**Subdomain 2 - CDN (Static Files):**

```
Domain: cdn.yourdomain.com
Document Root: /home/username/public_html/ntsava-app/storage
PHP Version: Any (will be blocked)
PHP Execution: DISABLED (Critical for security)
```

### Option B: Nginx (VPS/Dedicated)

**API Subdomain Configuration:**

```nginx
# /etc/nginx/sites-available/api.yourdomain.com
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name api.yourdomain.com;
    
    root /var/www/ntsava-app/public;
    index index.php;
    
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # Security headers
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "DENY" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

**CDN Subdomain Configuration:**

```nginx
# /etc/nginx/sites-available/cdn.yourdomain.com
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name cdn.yourdomain.com;
    
    root /var/www/ntsava-app/storage;
    
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # Block PHP execution completely
    location ~ \.php$ {
        return 403;
    }
    
    location ~ \.phtml$ {
        return 403;
    }
    
    # Serve static files with cache headers
    location / {
        try_files $uri $uri/ =404;
        
        # Cache control for CDN
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Access-Control-Allow-Origin "*";
    }
    
    # Disable directory listing
    autoindex off;
}
```

**Enable sites:**

```bash
ln -s /etc/nginx/sites-available/api.yourdomain.com /etc/nginx/sites-enabled/
ln -s /etc/nginx/sites-available/cdn.yourdomain.com /etc/nginx/sites-enabled/
nginx -t && systemctl restart nginx
```

### Option C: Apache (VPS/Dedicated)

**API Subdomain Configuration:**

```apache
# /etc/apache2/sites-available/api.yourdomain.com.conf
<VirtualHost *:443>
    ServerName api.yourdomain.com
    DocumentRoot /var/www/ntsava-app/public
    
    <Directory /var/www/ntsava-app/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem
    
    ErrorLog ${APACHE_LOG_DIR}/api-error.log
    CustomLog ${APACHE_LOG_DIR}/api-access.log combined
</VirtualHost>
```

**CDN Subdomain Configuration:**

```apache
# /etc/apache2/sites-available/cdn.yourdomain.com.conf
<VirtualHost *:443>
    ServerName cdn.yourdomain.com
    DocumentRoot /var/www/ntsava-app/storage
    
    <Directory /var/www/ntsava-app/storage>
        Options -Indexes
        AllowOverride All
        Require all granted
        
        # Block PHP execution
        <FilesMatch "\.(php|phtml|php3|php4|php5|phps|inc)$">
            Require all denied
        </FilesMatch>
    </Directory>
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem
</VirtualHost>
```

**Enable sites:**

```bash
a2ensite api.yourdomain.com.conf
a2ensite cdn.yourdomain.com.conf
a2enmod ssl rewrite headers
systemctl restart apache2
```

### Critical Security: Storage .htaccess

Create `/storage/.htaccess` (if using Apache):

```apache
# Block PHP execution completely
<FilesMatch "\.(php|phtml|php3|php4|php5|phps|inc|phtml)$">
    Require all denied
</FilesMatch>

# Cache headers for CDN
<IfModule mod_headers.c>
    <FilesMatch "\.(jpg|jpeg|png|gif|webp|avif|mp4|webm|pdf|css|js|woff2)$">
        Header set Cache-Control "public, max-age=31536000, immutable"
        Header set CDN-Cache-Control "public, max-age=31536000"
        Header set Access-Control-Allow-Origin "*"
    </FilesMatch>
</IfModule>

# Disable directory listing
Options -Indexes

# Prevent access to hidden files
RedirectMatch 403 /\..*$
```

---

## DNS Configuration

### Basic DNS Records

| Type | Name | Value | TTL | Proxy Status |
|------|------|-------|-----|--------------|
| A | @ | YOUR_SERVER_IP | Auto | DNS Only |
| A | api | YOUR_SERVER_IP | Auto | Proxied (if using CDN) |
| A | cdn | YOUR_SERVER_IP | Auto | Proxied (if using CDN) |
| CNAME | www | yourdomain.com | Auto | DNS Only |

### For Shared Hosting (No Dedicated IP)

| Type | Name | Value | TTL |
|------|------|-------|-----|
| A | @ | SHARED_HOSTING_IP | Auto |
| CNAME | api | yourdomain.com | Auto |
| CNAME | cdn | yourdomain.com | Auto |

### Verify DNS Propagation

```bash
# Check DNS records
dig api.yourdomain.com +short
dig cdn.yourdomain.com +short

# Check from multiple locations
nslookup api.yourdomain.com 8.8.8.8
nslookup cdn.yourdomain.com 1.1.1.1

# Online tools (alternative)
# https://dnschecker.org
# https://www.whatsmydns.net
```

---

## CDN Integration

Ntsava is CDN-agnostic. Choose your preferred provider:

### Option 1: Cloudflare (Recommended for Free Tier)

Cloudflare provides 300+ PoPs including Maputo, Mozambique.

**Step 1: Add Domain to Cloudflare**

```
1. Sign up at cloudflare.com
2. Click "Add Site"
3. Enter yourdomain.com
4. Select Free plan
5. Copy the provided nameservers
```

**Step 2: Update Nameservers at Registrar**

```
At your domain registrar (GoDaddy, Namecheap, etc.):
Change nameservers to:
  - ns1.cloudflare.com
  - ns2.cloudflare.com

Propagation time: 5-30 minutes (up to 24 hours)
```

**Step 3: Configure DNS in Cloudflare**

```
Cloudflare Dashboard -> DNS -> Records

Type: A
Name: api
IPv4: YOUR_SERVER_IP
Proxy status: Proxied (orange cloud)

Type: A
Name: cdn
IPv4: YOUR_SERVER_IP
Proxy status: Proxied (orange cloud)
```

**Step 4: Create Cache Rules**

```
Cloudflare Dashboard -> Rules -> Page Rules

Rule 1:
  URL: cdn.yourdomain.com/*
  Settings:
    - Cache Level: Cache Everything
    - Edge Cache TTL: 1 month (2592000 seconds)
    - Browser Cache TTL: 1 year

Rule 2:
  URL: api.yourdomain.com/api/*
  Settings:
    - Cache Level: Bypass
```

**Step 5: Enable Optimizations**

```
Cloudflare Dashboard -> Speed -> Optimization

Enable:
  - Auto Minify: HTML, CSS, JavaScript
  - Brotli
  - Rocket Loader (optional)
  - HTTP/2
  - HTTP/3 (QUIC)
```

**Step 6: Configure SSL/TLS**

```
Cloudflare Dashboard -> SSL/TLS

SSL/TLS Encryption: Full (strict)
Always Use HTTPS: ON
Automatic HTTPS Rewrites: ON
Minimum TLS Version: 1.2
```

### Option 2: Bunny.net (Best for Video)

```bash
# 1. Sign up at bunny.net
# 2. Create a Pull Zone
# 3. Configure Origin URL: https://cdn.yourdomain.com
# 4. Set Edge Rules:
#    - Cache static files: 1 year
#    - Enable TLS 1.2+
# 5. Update DNS:
#    CNAME cdn.yourdomain.com -> yourzone.b-cdn.net
```

### Option 3: AWS CloudFront (For AWS Ecosystem)

```bash
# 1. Create S3 bucket or use your origin
# 2. Create CloudFront distribution
#    - Origin Domain: cdn.yourdomain.com
#    - Origin Protocol: HTTPS only
#    - Cache Policy: Managed-CachingOptimized
# 3. Update DNS:
#    CNAME cdn.yourdomain.com -> distribution.cloudfront.net
```

### Option 4: Custom Varnish PoP (For Complete Control)

```bash
# Install Varnish on edge server
apt install -y varnish

# Configure Varnish (default.vcl)
cat > /etc/varnish/default.vcl <<'EOF'
vcl 4.0;

backend origin {
    .host = "your-origin-server.com";
    .port = "443";
    .ssl = true;
}

sub vcl_recv {
    # Cache static files
    if (req.url ~ "^/u/.*\.(jpg|png|gif|webp|mp4|pdf)$") {
        return (hash);
    }
    
    # Pass API requests
    if (req.url ~ "^/api/") {
        return (pass);
    }
}

sub vcl_backend_response {
    # Cache for 30 days
    set beresp.ttl = 30d;
    set beresp.grace = 1h;
    set beresp.http.X-Cache-Node = "maputo-edge-01";
}

sub vcl_deliver {
    # Add PoP location header
    set resp.http.X-PoP-Location = "Maputo, Mozambique";
}
EOF

# Configure Nginx as reverse proxy
systemctl restart varnish
```

---

## Verification

### Test 1: Application Responds

```bash
# Check the app is responding
curl -I https://api.yourdomain.com/

# Expected: HTTP/2 200 or 302 (redirect to login)
```

### Test 2: Database Connection

```bash
# Test database connectivity
php -r "new PDO('mysql:host=localhost;dbname=ntsava_cdn', 'user', 'pass');"
echo $?  # Should output 0 (success)
```

### Test 3: File Upload

```bash
# Create test file
echo "Hello Ntsava CDN" > test.txt

# Upload via API (replace with your credentials)
curl -X POST https://api.yourdomain.com/api/v1/upload \
  -H "X-User-UUID: YOUR_USER_UUID" \
  -H "X-Token: YOUR_API_TOKEN" \
  -F "file=@test.txt"

# Expected response includes CDN URL:
# {"success":true,"data":{"url":"https://cdn.yourdomain.com/u/..."}}
```

### Test 4: CDN Access

```bash
# Access file via CDN
curl -I https://cdn.yourdomain.com/u/username/test.txt

# Expected headers:
# HTTP/2 200
# cache-control: public, max-age=31536000, immutable
# cf-cache-status: HIT (if using Cloudflare)
# content-type: text/plain
```

### Test 5: Image Processing

```bash
# Upload an image first, then test resizing
curl -I "https://cdn.yourdomain.com/u/username/photo.jpg?w=200&format=webp"

# Expected:
# HTTP/2 200
# content-type: image/webp
# cf-cache-status: MISS (first request) or HIT (subsequent)
```

### Test 6: Global PoP Latency (Maputo Focus)

```bash
# Test from Maputo (use a server in Mozambique if available)
curl -w "Total time: %{time_total}s\n" -o /dev/null -s \
  https://cdn.yourdomain.com/u/username/test.jpg

# Expected for cached file:
# Total time: < 0.05s (50ms) from Maputo PoP

# Expected for uncached file:
# Total time: < 0.3s (300ms) from origin
```

---

## Troubleshooting

### Issue: 404 Not Found on CDN

**Symptoms:** Files return 404 even though they exist in storage

**Solutions:**

```bash
# Check if file exists in storage
ls -la storage/u/username/

# Verify .htaccess in storage folder
cat storage/.htaccess

# Check web server configuration
sudo nginx -t  # or apachectl configtest

# Check file permissions
ls -la storage/u/username/file.jpg
# Should be readable by web server (644 or 755)
```

### Issue: PHP Files Executing in Storage Directory

**Symptoms:** PHP files in storage are being executed instead of downloaded

**Solutions:**

```apache
# Add to storage/.htaccess (Apache)
<FilesMatch "\.php$">
    Require all denied
</FilesMatch>
```

```nginx
# For Nginx
location ~ \.php$ {
    return 403;
}
```

### Issue: Cloudflare Not Caching

**Symptoms:** `CF-Cache-Status: DYNAMIC` or `MISS` on every request

**Solutions:**

```bash
# Check response headers
curl -I https://cdn.yourdomain.com/file.jpg

# Look for Cache-Control header
# If missing, add to .htaccess:

<FilesMatch "\.(jpg|jpeg|png|gif)$">
    Header set Cache-Control "public, max-age=31536000"
</FilesMatch>

# Verify Page Rule in Cloudflare
# URL: cdn.yourdomain.com/*
# Cache Level: Cache Everything
```

### Issue: Database Connection Failed

**Symptoms:** API returns 500 errors or "Database connection failed"

**Solutions:**

```bash
# Test database connection
php -r "new PDO('mysql:host=localhost;dbname=ntsava_cdn', 'user', 'pass');"

# Check .env file
cat .env | grep DB_

# Verify database service is running
sudo systemctl status mysql   # or postgresql

# Check database credentials
mysql -u ntsava_user -p -e "SELECT 1"
```

### Issue: Upload Fails with 413 Payload Too Large

**Symptoms:** Large file uploads fail

**Solutions:**

```bash
# Increase PHP limits
sudo nano /etc/php/8.1/fpm/php.ini

upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 256M
max_execution_time = 300

# Restart PHP-FPM
sudo systemctl restart php8.1-fpm

# For Nginx, also check:
client_max_body_size 100M;
```

### Issue: Image Resize Fails

**Symptoms:** Images return error or don't resize

**Solutions:**

```bash
# Check GD extension
php -m | grep gd

# Install GD if missing
apt install php8.1-gd  # Debian/Ubuntu
dnf install php81-gd   # CentOS/RHEL

# Check memory limit for large images in .env
```

### Issue: CDN PoP Not Detected (Maputo)

**Symptoms:** Users in Mozambique getting high latency

**Solutions:**

```bash
# Check which PoP is serving the request
curl -I https://cdn.yourdomain.com/test.jpg | grep "CF-Ray"

# Example response includes PoP code:
# CF-Ray: 7a8b9c10-MPM (Maputo)
# CF-Ray: 7a8b9c10-JNB (Johannesburg - not ideal)

# If not routing to Maputo:
# 1. Ensure Cloudflare proxy is enabled (orange cloud)
# 2. Check Cloudflare network map for PoP availability
# 3. Consider using a different CDN provider with Maputo presence
```

### Issue: SSL Certificate Errors

**Symptoms:** Browser shows security warnings

**Solutions:**

```bash
# Cloudflare (Free SSL):
# SSL/TLS -> Full (strict)

# Let's Encrypt (VPS):
certbot --nginx -d yourdomain.com -d api.yourdomain.com -d cdn.yourdomain.com

# Verify certificate:
openssl s_client -connect api.yourdomain.com:443 -servername api.yourdomain.com
```

---

## Performance Baseline

After successful setup, run benchmark:

```bash
# Create curl format file
cat > curl-format.txt <<'EOF'
    time_namelookup:  %{time_namelookup}s
       time_connect:  %{time_connect}s
    time_appconnect:  %{time_appconnect}s
   time_pretransfer:  %{time_pretransfer}s
      time_redirect:  %{time_redirect}s
 time_starttransfer:  %{time_starttransfer}s
---------------------
         time_total:  %{time_total}s
EOF

# Test cached file
curl -w "@curl-format.txt" -o /dev/null -s \
  https://cdn.yourdomain.com/u/username/test.jpg

# Expected results (cached):
# time_total: < 0.05s (50ms) from nearby PoP
# time_total: < 0.15s (150ms) from distant PoP

# Test uncached file (first request after upload)
curl -w "@curl-format.txt" -o /dev/null -s \
  https://cdn.yourdomain.com/u/username/newfile.jpg

# Expected results (uncached):
# time_total: < 0.5s (500ms) depending on origin location
```

---

## Next Steps

Setup complete. Continue with:

- [ARCHITECTURE.md](ARCHITECTURE.md) - Deep dive into system design
- [DEPLOYMENT.md](DEPLOYMENT.md) - Multi-server and high availability
- Create admin user for dashboard access
- Configure automated backups
- Set up monitoring and alerts
- Configure custom domain with SSL

---

## Support

If you encounter issues not covered here:

- GitHub Issues: https://github.com/lizzyman04/ntsava-app/issues
- Documentation: https://github.com/lizzyman04/ntsava-app/docs
- Email: admin@tudocomlizzyman.com

---

[Back to Top](#ntsava-cdn-setup-guide)