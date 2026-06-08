#!/usr/bin/env bash
set -euo pipefail

php scripts/cloud-seed-migrations.php
php artisan migrate --force --no-interaction
