#!/bin/bash

echo "Deploying BSS System..."

git pull origin master

composer install --no-dev --optimize-autoloader
source scripts/render-db-check.sh
if render_detect_imported_schema; then
  echo "Imported database schema detected; seeding migration history and skipping migrate."
  php artisan deploy:seed-imported-migrations --no-interaction || true
else
  php artisan migrate --force --no-interaction
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:restart

echo "Deployment completed!"
