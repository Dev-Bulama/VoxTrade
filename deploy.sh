#!/bin/bash
set -e

echo "🚀 VoxTrade Deployment Script"

# Install composer dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Copy env if not exists
[ ! -f .env ] && cp .env.example .env

# Generate key if empty
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Seed if first deploy (settings + admin)
php artisan db:seed --force 2>/dev/null || true

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment complete"
