#!/usr/bin/env bash
set -euo pipefail

source scripts/render-prepare-cert.sh

if [[ -z "${APP_KEY:-}" ]]; then
  echo "APP_KEY is missing. Set APP_KEY in Render environment variables."
  exit 1
fi

php -d opcache.enable_cli=1 artisan queue:work --queue=default --tries=3 --timeout=120 --sleep=1 --max-jobs=1000 --max-time=3600 --memory=256
