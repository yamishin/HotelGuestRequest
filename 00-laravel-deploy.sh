#!/usr/bin/env bash
echo "Running migrations..."
php artisan migrate:fresh --force

echo "Seeding database..."
php artisan db:seed --force