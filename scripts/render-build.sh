#!/usr/bin/env bash
set -euo pipefail

bash scripts/render-prepare-cert.sh

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

php artisan storage:link || true
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache framework metadata for faster boot.
php artisan config:cache
php artisan route:cache
php artisan view:cache

