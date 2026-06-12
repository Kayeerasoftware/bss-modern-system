#!/bin/bash

echo "Deploying BSS System..."

source scripts/render-prepare-cert.sh

git pull origin master

composer install --no-dev --optimize-autoloader
php scripts/render-seed-migrations.php
php artisan migrate --force --no-interaction

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:restart

echo "Deployment completed!"
