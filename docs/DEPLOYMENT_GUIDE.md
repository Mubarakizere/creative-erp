# Creative ERP - Deployment Guide

This guide covers the fundamental steps for deploying the Creative ERP application to a production server (like a VPS or cPanel).

## 1. Server Requirements

Ensure your server meets the following requirements:
- PHP >= 8.2
- Node.js & npm (for building Vite assets)
- Composer
- Database: MySQL, MariaDB, or PostgreSQL
- Web Server: Nginx or Apache

**Required PHP Extensions:**
`ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `session`, `tokenizer`, `xml`, `gd` or `imagick` (for Intervention Image).

## 2. Environment Setup

1. Copy `.env.production` to `.env` on your server:
   ```bash
   cp .env.production .env
   ```
2. Update the `.env` file with your production database credentials and `APP_URL`.
3. Generate a new application key:
   ```bash
   php artisan key:generate
   ```

## 3. Deployment Script

If you are using a VPS (via SSH), you can use the included `deploy.sh` script to automate updates.

1. Make the script executable:
   ```bash
   chmod +x deploy.sh
   ```
2. Run the deployment:
   ```bash
   ./deploy.sh
   ```

> [!TIP]
> **cPanel Users:** If you don't have SSH access, you can zip the project (after running `npm run build` locally), upload it to your file manager, extract it, and set the document root to the `public/` directory.

## 4. Directory Permissions

Ensure that the web server (e.g., `www-data` or `nginx`) has write access to the following directories:
```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

## 5. Storage Link

Create the symbolic link for publicly accessible files (like avatars and uploads):
```bash
php artisan storage:link
```

## 6. Nginx Configuration Example

If setting up a VPS manually, your Nginx site configuration should point to the `public` directory:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/creative-erp/public;

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
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
