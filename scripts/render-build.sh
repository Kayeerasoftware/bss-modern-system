#!/usr/bin/env bash
set -euo pipefail

bash scripts/render-prepare-cert.sh

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache public/uploads

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts
composer dump-autoload --optimize --no-dev --no-interaction
php artisan package:discover --ansi

php artisan storage:link || true
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache framework metadata for faster boot.
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true
