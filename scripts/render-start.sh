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

# Ensure app key exists in environment.
if [[ -z "${APP_KEY:-}" ]]; then
  echo "APP_KEY is missing. Set APP_KEY in Render environment variables."
  exit 1
fi

# Improve concurrency on PHP's built-in server in Render Linux runtime.
export PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}"

# Use CLI opcache to reduce request bootstrap overhead.
php -d opcache.enable_cli=1 artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
