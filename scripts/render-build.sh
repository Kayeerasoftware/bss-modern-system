#!/usr/bin/env bash
set -euo pipefail

source scripts/render-prepare-cert.sh

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache public/uploads storage/app/public

# Persist uploads on Render disk when available.
PERSISTENT_DISK_PATH="${PERSISTENT_DISK_PATH:-/var/data}"
if [[ -d "${PERSISTENT_DISK_PATH}" ]]; then
  mkdir -p "${PERSISTENT_DISK_PATH}/uploads" "${PERSISTENT_DISK_PATH}/storage-public"
  rm -rf public/uploads storage/app/public
  ln -sfn "${PERSISTENT_DISK_PATH}/uploads" public/uploads
  ln -sfn "${PERSISTENT_DISK_PATH}/storage-public" storage/app/public
fi

composer install --no-dev --prefer-dist --optimize-autoloader --classmap-authoritative --no-interaction --no-scripts
composer dump-autoload --optimize --classmap-authoritative --no-dev --no-interaction
php artisan package:discover --ansi

php artisan storage:link || true
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache framework metadata for faster boot.
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true
