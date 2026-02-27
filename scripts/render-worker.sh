#!/usr/bin/env bash
set -euo pipefail

source scripts/render-prepare-cert.sh

if [[ -z "${APP_KEY:-}" ]]; then
  echo "APP_KEY is missing. Set APP_KEY in Render environment variables."
  exit 1
fi

php artisan queue:work --tries=3 --timeout=120 --sleep=2 --max-time=3600
