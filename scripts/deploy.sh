#!/bin/bash

echo "Deploying BSS System..."

git pull origin master

composer install --no-dev --optimize-autoloader
php artisan deploy:seed-imported-migrations --no-interaction || true
php artisan migrate --force --no-interaction

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:restart

echo "Deployment completed!"
