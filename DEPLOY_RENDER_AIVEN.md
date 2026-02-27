# Deploy BSS System to Render with Aiven MySQL

This project now includes deployment assets for Render:

- `render.yaml` (web + worker blueprint)
- `scripts/render-build.sh`
- `scripts/render-start.sh`
- `scripts/render-worker.sh`
- `scripts/render-prepare-cert.sh`
- `.env.render.example`

Use this guide to deploy successfully with a separate Aiven DB.

## 1. Prerequisites

- GitHub repo with this code.
- Render account.
- Aiven MySQL service created and running.
- Aiven database/user credentials + CA certificate.

## 2. Prepare Aiven MySQL

1. In Aiven, open your MySQL service.
2. Copy:
   - `Host`
   - `Port`
   - `Database`
   - `Username`
   - `Password`
3. Download or copy the CA certificate PEM text from Aiven.

## 3. Prepare app key

Generate a production APP key locally:

```bash
php artisan key:generate --show
```

Copy the resulting `base64:...` value.

## 4. Deploy on Render (Blueprint method)

1. In Render, click `New` -> `Blueprint`.
2. Select this GitHub repo.
3. Render will detect `render.yaml` and propose two services:
   - `bss-system-web`
   - `bss-system-worker`
4. Create the blueprint.

## 5. Set environment variables in Render

Set these vars on **both** services (web + worker), unless noted.

Required:

- `APP_NAME=BSS System`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=base64:...` (from step 3)
- `APP_URL=https://<your-web-service>.onrender.com`
- `DB_CONNECTION=mysql`
- `DB_HOST=<aiven-host>`
- `DB_PORT=<aiven-port>`
- `DB_DATABASE=<aiven-db>`
- `DB_USERNAME=<aiven-user>`
- `DB_PASSWORD=<aiven-password>`
- `AIVEN_CA_PEM=<full PEM certificate text>`
- `QUEUE_CONNECTION=database`
- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `FILESYSTEM_DISK=public`

Optional mail vars:

- `MAIL_MAILER`
- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_ENCRYPTION`
- `MAIL_FROM_ADDRESS`
- `MAIL_FROM_NAME`

## 6. First deploy flow

Render will run:

- build: `bash scripts/render-build.sh`
- pre-deploy (web only): `bash scripts/render-prepare-cert.sh && php artisan migrate --force`
- start web: `bash scripts/render-start.sh`
- start worker: `bash scripts/render-worker.sh`

## 7. Verify deployment

1. Open web URL (`/`).
2. Log in.
3. Verify DB-backed pages load.
4. Trigger a queued action and confirm worker consumes jobs.
5. Check Render logs for both services:
   - no DB SSL errors
   - no `APP_KEY` errors
   - no migration errors

## 8. Common fixes

### DB SSL / certificate errors

- Ensure `AIVEN_CA_PEM` contains the full certificate including:
  - `-----BEGIN CERTIFICATE-----`
  - `-----END CERTIFICATE-----`

### `No application encryption key has been specified`

- Add valid `APP_KEY` to Render env vars for web and worker.

### Queue jobs not processing

- Confirm worker service is running.
- Confirm `QUEUE_CONNECTION=database`.
- Ensure `jobs` table exists (migrations ran).

### File uploads

- Render disk is ephemeral. For persistent uploads, move files to S3-compatible storage and set `FILESYSTEM_DISK` accordingly.

## 9. Manual (non-blueprint) Render setup

If you don't use `render.yaml`:

- Web service:
  - Build command: `bash scripts/render-build.sh`
  - Pre-deploy command: `bash scripts/render-prepare-cert.sh && php artisan migrate --force`
  - Start command: `bash scripts/render-start.sh`
- Worker service:
  - Build command: `bash scripts/render-build.sh`
  - Start command: `bash scripts/render-worker.sh`

