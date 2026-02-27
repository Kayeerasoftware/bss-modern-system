#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# Prepare Aiven CA cert when provided as raw PEM in env.
if [[ -n "${AIVEN_CA_PEM:-}" ]]; then
  mkdir -p storage/certs
  printf '%s\n' "${AIVEN_CA_PEM}" > storage/certs/aiven-ca.pem
  export MYSQL_ATTR_SSL_CA="storage/certs/aiven-ca.pem"
fi

if [[ -z "${APP_KEY:-}" ]]; then
  echo "ERROR: APP_KEY is missing. Set APP_KEY in Render env vars."
  exit 1
fi

PORT="${PORT:-10000}"

# Render requires binding to the provided PORT.
sed -ri "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

mkdir -p public/uploads storage/app/public storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public/uploads || true
chmod -R ug+rwx storage bootstrap/cache public/uploads || true
ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Set RUN_MIGRATIONS=true in Render to auto-run migrations on container boot.
if [[ "${RUN_MIGRATIONS:-false}" == "true" ]]; then
  php artisan migrate --force || exit 1
fi

exec apache2-foreground
