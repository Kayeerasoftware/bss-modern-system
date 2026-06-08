#!/usr/bin/env bash
set -euo pipefail

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache public/uploads storage/app/public

composer install --no-dev --prefer-dist --optimize-autoloader --classmap-authoritative --no-interaction
npm ci --omit=dev
npm run build
php artisan optimize
