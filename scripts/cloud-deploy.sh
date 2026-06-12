#!/usr/bin/env bash
set -euo pipefail

# Laravel Cloud deploy commands should stay focused on schema changes.
# Migration history seeding is handled automatically by AppServiceProvider.
php artisan migrate --force --no-interaction --isolated
