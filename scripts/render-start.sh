#!/usr/bin/env bash
set -euo pipefail

bash scripts/render-prepare-cert.sh

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache public/uploads

# Ensure app key exists in environment.
if [[ -z "${APP_KEY:-}" ]]; then
  echo "APP_KEY is missing. Set APP_KEY in Render environment variables."
  exit 1
fi

php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
