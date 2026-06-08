# Deploy BSS System to Laravel Cloud

This repository is ready for Laravel Cloud, but it should be deployed as a MySQL app with object storage attached.

## What to provision in Laravel Cloud

1. Create a new environment from this Git repository.
2. Attach a Laravel MySQL database to the environment.
3. Attach a Laravel Object Storage bucket if you want uploads, photos, and chat attachments to persist across deployments.
4. Set the environment variables below.

## Required environment variables

Set these on the Cloud environment:

- `APP_NAME=BSS System`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=base64:...`
- `APP_URL=https://<your-cloud-domain>`
- `DB_CONNECTION=mysql`
- `CACHE_STORE=database`
- `SESSION_DRIVER=database`
- `QUEUE_CONNECTION=database`
- `FILESYSTEM_DISK=public`

Laravel Cloud will inject the database credentials automatically when the MySQL resource is attached.

## Recommended Cloud resources

Use these resources for the best experience:

- MySQL for the application database.
- Object storage for photos, chat attachments, and other user uploads.
- Database-backed cache and sessions so the app remains stable across deploys and instances.

## Build command

Use this as the build command in Laravel Cloud:

```bash
bash scripts/cloud-build.sh
```

This script installs PHP and Node dependencies, builds the frontend, and runs Laravel optimization during the build phase.

## Deploy command

Use this as the deploy command in Laravel Cloud:

```bash
bash scripts/cloud-deploy.sh
```

This keeps the deploy phase focused on migrations only.

## Why MySQL

This codebase uses MySQL-specific migrations and DDL. PostgreSQL will fail on statements such as `SET FOREIGN_KEY_CHECKS=0` and `AUTO_INCREMENT`, so the Laravel Cloud database must be MySQL.

## After the first deploy

1. Open the deployed site.
2. Verify login works.
3. Upload a photo or chat attachment to confirm object storage is working.
4. Check `php artisan migrate:status` from a Cloud command if you want to confirm the schema is up to date.

## Local development

For local development, copy `.env.example` to `.env`, then fill in your MySQL credentials.

If you are using Cloud object storage, do not run `storage:link` as part of deployment. Laravel Cloud keeps deploy-time filesystem changes ephemeral.
