#!/usr/bin/env bash
echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Running migrations..."
php artisan migrate:fresh --force

echo "Seeding database..."
php artisan db:seed --force