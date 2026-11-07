#!/usr/bin/env bash
echo "Running composer..."
composer install --no-dev --working-dir=/var/www/html

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Clearing caches to fix Render 404s..."
php artisan view:clear
php artisan route:clear

echo "Running migrations..."
php artisan migrate --force

echo "Seeding the database..."
php artisan db:seed --force