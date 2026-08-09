#!/bin/bash

# Exit on error
set -e

echo "Starting Deployment Process..."

# 1. Bring application down for maintenance
# php artisan down --render="errors::503" --secret="1630542a-246b-4b66-afa1-dd72a4c43515"

# 2. Update codebase from git
echo "Pulling latest changes from git..."
git fetch origin main
git reset --hard origin/main

# 3. Install/update PHP dependencies (optimized for production)
echo "Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 4. Install/update Node dependencies and build assets
echo "Building frontend assets..."
npm ci
npm run build

# 5. Clear and cache configuration, routes, and views
echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Run database migrations safely
echo "Running database migrations..."
php artisan migrate --force

# 7. Bring application back up
# php artisan up

echo "Deployment completed successfully!"
