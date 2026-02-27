#!/usr/bin/env bash
set -euo pipefail

bash scripts/render-prepare-cert.sh

php artisan queue:work --tries=3 --timeout=120 --sleep=2 --max-time=3600

