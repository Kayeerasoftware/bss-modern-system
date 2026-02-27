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

php artisan storage:link || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Set RUN_MIGRATIONS=true in Render to auto-run migrations on container boot.
if [[ "${RUN_MIGRATIONS:-false}" == "true" ]]; then
  php artisan migrate --force || exit 1
fi

exec apache2-foreground

