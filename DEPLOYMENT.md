# Deploying POWER to cPanel

This app is a standard Laravel 11 application with no Redis/Memcached/queue-worker
dependency — it runs on plain cPanel shared hosting (PHP 8.2+, MySQL, no SSH required
though SSH makes this much faster if your host offers it).

## 1. Build assets locally first

cPanel PHP hosting doesn't run Node, so compile the frontend before uploading:

```bash
npm install
npm run build
```

This produces `public/build/` — commit/upload it as part of the deploy.

## 2. Upload the app outside the public web root

On shared hosting, put the whole Laravel app in a folder *above* `public_html`
(e.g. `/home/youruser/power-app`), then point `public_html` at the app's `public/`
folder — either via cPanel's "Application Manager" (if it offers Laravel/PHP app
support) or by editing `public_html/index.php` to require the app's
`public/index.php`, adjusting the two `require`/`realpath` lines in
`public/index.php` to point at the real `vendor/autoload.php` and
`bootstrap/app.php` locations. If your host lets you set the domain's document
root directly to the app's `public/` folder, do that instead — it's simpler and
is what these instructions assume.

## 3. Install dependencies

Via SSH (or cPanel's Terminal if available):

```bash
composer install --no-dev --optimize-autoloader
```

## 4. Configure `.env`

Copy `.env.example` to `.env` and fill in:
- `APP_URL` — your real domain
- `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` — the MySQL database cPanel created
  (create one in cPanel's "MySQL Databases" first)
- `ADMIN_EMAIL` / `ADMIN_PASSWORD` — the store manager login `AdminSeeder` creates
- Mail settings if you want order-related emails to send (optional — nothing in
  the app currently requires mail to function)

Then generate the app key and run migrations/seeders:

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
```

## 5. Make `public/uploads/` writable

Product/category images are stored on a custom `uploads` disk rooted at
`public/uploads/` (no `storage:link` symlink needed — shared hosts sometimes
block symlinks, so this setup avoids that entirely). Make sure the directory
exists and is writable by the web server user:

```bash
mkdir -p public/uploads/products public/uploads/categories
chmod -R 755 public/uploads
```

## 6. Cache config/routes/views for production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Re-run these three after any subsequent code deploy or `.env` change — a stale
config cache is the most common source of "it works locally but not on the
server" bugs.

## 7. Log in and change the admin password

Visit `https://your-domain.tld/admin/login` with the `ADMIN_EMAIL`/`ADMIN_PASSWORD`
from `.env`, then change the password immediately (there's no self-service
password-change screen yet — update it via `php artisan tinker` or a fresh
`db:seed` run with new `.env` values if needed).

## 8. Meta Pixel / Conversion API (optional)

Under `/admin/marketing`, paste in the Meta Pixel ID and a Conversion API
access token to start tracking `ViewContent`, `AddToCart`, `InitiateCheckout`
and `Purchase` events (client-side pixel + server-side CAPI, deduplicated via a
shared event ID). Leave both blank to keep tracking off — no pixel script loads
at all until a Pixel ID is set.

## Notes on scaling

- **Queue**: currently `sync` (Conversion API calls happen inline on the
  request). If traffic grows enough that this adds noticeable checkout latency,
  switch `QUEUE_CONNECTION` to `database` and add a cron entry for
  `php artisan queue:work --stop-when-empty` every minute (or run a persistent
  worker if your host allows long-running processes).
- **Cache/session**: both default to the `database` driver, which needs no extra
  services beyond the MySQL database already in use.
