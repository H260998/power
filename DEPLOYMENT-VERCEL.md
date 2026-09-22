# Deployment on Vercel

POWER is a Laravel 12 application. The Vercel project uses the community PHP 8.4
runtime (`vercel-php@0.8.0`), Node.js 22, a Supabase PostgreSQL database, and a public
Vercel Blob store for product and category images.

## GitHub and Supabase setup

Import `https://github.com/H260998/power` into the intended Vercel account.
Use the repository root (`./`), framework **Other**, Node.js **22.x**, and
production branch **main**. Build settings are already in `vercel.json`.
Future pushes to `main` deploy through Vercel's GitHub integration.

In the Supabase project dedicated to POWER:

1. Open **Connect** and copy the **Session pooler** connection URL (port 5432).
   Use the exact host and username shown by Supabase. Replace the password
   placeholder and percent-encode any reserved characters in the password.
2. Store the URL as `DB_URL` in Vercel and in an ignored local `.env` for
   migrations. Set `DB_SCHEMA=laravel` and `DB_SSLMODE=require` in both places.

The one-time browser installer creates the private `laravel` schema. The SQL
file at `database/supabase-bootstrap.sql` remains available as a manual fallback.

Do not use the Transaction pooler (port 6543) with this configuration.
Administrator authentication continues to use Laravel, not Supabase Auth.
See [Supabase's Laravel guide](https://supabase.com/docs/guides/getting-started/quickstarts/laravel).

Create or connect a **public Vercel Blob** store to the Vercel project to obtain
`BLOB_READ_WRITE_TOKEN` before enabling administrator image uploads.

## Project configuration

`vercel.json` sets the framework to Other and defines the build and routing.
Vite builds the frontend; `scripts/vercel-static.mjs` copies only public assets
to `dist`. PHP files are excluded from static output. The PHP function retains
the Laravel source and `public/build/manifest.json`.

`api/index.php` initializes disposable view and package caches in `/tmp`.
Sessions and application cache must use the database so they survive restarts.
Uploaded images are stored in Vercel Blob, not the function filesystem.

## Environment variables

Set these for production in the Vercel project settings. Previews should use
their own database and storage instead of modifying production data:

| Variable | Value |
| --- | --- |
| `APP_ENV` | `production` |
| `APP_NAME` | `POWER` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | A persistent Laravel key, stored as a sensitive variable |
| `APP_URL` | The production HTTPS URL |
| `SETUP_TOKEN` | A random secret of at least 32 characters, stored as sensitive |
| `APP_LOCALE`, `APP_FALLBACK_LOCALE` | `fr` |
| `DB_CONNECTION` | `pgsql` |
| `DB_URL` | Supabase Session pooler URL (port 5432), stored as sensitive |
| `DB_SCHEMA` | `laravel` |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER`, `CACHE_STORE` | `database` |
| `SESSION_SECURE_COOKIE`, `SESSION_ENCRYPT` | `true` |
| `SESSION_COOKIE` | `__Host-alo-session` |
| `QUEUE_CONNECTION` | `sync` |
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `info` |
| `UPLOADS_DRIVER` | `vercel-blob` |
| `BLOB_READ_WRITE_TOKEN` | Supplied by the connected public Blob store; sensitive |
| `MAIL_MAILER` | `log`, until an SMTP provider is configured |

Configure a mail provider before enabling outbound email. The initial deployment
uses the `log` mailer. A custom domain requires updating `APP_URL` and redeploying.

## Initialize a new database in the browser

Generate the two required secrets locally and add them to Vercel before the
first deployment:

```sh
php artisan key:generate --show
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

Store the first value as `APP_KEY` and the second as `SETUP_TOKEN`. After the
deployment, open `https://your-domain.tld/setup`, verify that all three checks
are green, then enter `SETUP_TOKEN` and the first administrator's details.

The installer creates the PostgreSQL schema, runs all migrations, loads the
categories, products and store settings, and creates the administrator. It does
not display or store the setup token in the database. As soon as the first
administrator exists, both `/setup` routes return 404 permanently. Remove
`SETUP_TOKEN` from Vercel after installation as an additional precaution.

The setup route is stateless so it works before the database session and cache
tables exist. Never add it behind database-backed session middleware.

## Command-line alternative

Run migrations from a trusted local terminal with the production database
connection in an ignored `.env` and the PHP `pdo_pgsql` extension enabled.
Generate `APP_KEY` once with `php artisan key:generate --show`, store it as a
sensitive Vercel variable, and retain the same key across deployments:

```sh
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan migrate --force
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=ProductSeeder --force
php artisan db:seed --class=SettingSeeder --force
php artisan db:seed --class=AdminSeeder --force
```

The administrator seeder requires a valid `ADMIN_EMAIL` and an initial
`ADMIN_PASSWORD` of at least 12 characters. It does not reset an existing
administrator's password. These initialization values need not remain on Vercel.
Do not run migrations or seeders on every HTTP request or build. Apply future
schema changes deliberately before deploying code that needs them.

## Validation

```sh
npm ci
npm run build
node scripts/vercel-static.mjs
composer install
php vendor/bin/phpunit
node --test tests/js/podium-geometry.test.mjs
```

After deployment, check `/up`, `/`, `/boutique`, `/cart`, `/admin/login`, the
administrator login, and an image upload. No `.env` files or database exports
should be committed or uploaded as static content.
