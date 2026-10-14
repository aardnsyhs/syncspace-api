# SyncSpace API

Laravel API for [SyncSpace](https://github.com/aardnsyhs/syncspace), a team workspace and Kanban application. This repository owns authentication, workspace membership, board data, authorization, analytics, notifications, and WebSocket broadcasts.

## Implemented capabilities

[routes/api.php](routes/api.php) defines email/password and OTP authentication, password reset, optional Google code exchange, profile management, teams and roles, boards and columns, cards, comments, labels, checklists, attachments, templates, public board sharing, analytics, dashboard data, and notifications. Sanctum authenticates protected routes, policies authorize resource access, and Reverb broadcasts changes. See [architecture](docs/ARCHITECTURE.md) for roles and implementation caveats.

Feature tests cover authentication, boards, cards, checklists, analytics, roles, and public sharing. Their presence does not guarantee a passing suite; the joint [readiness audit](https://github.com/aardnsyhs/syncspace/blob/master/docs/OSS_READINESS_AUDIT.md) records actual results. Due-date reminder dispatch is not scheduled. Ably configuration is present but has not been verified end to end.

## Prerequisites

- PHP 8.2 or newer compatible with [composer.lock](composer.lock), Composer 2, and extensions required by installed dependencies. Run `composer check-platform-reqs` to verify them. Local MySQL work also needs `pdo_mysql`; uploads need `fileinfo`.
- A MySQL-compatible database and a database/user you can administer. Migrations contain MySQL-specific SQL; PostgreSQL and SQLite are not supported by the current migration chain.
- The separate React frontend for browser workflows. Node/Yarn are required there. Backend Vite assets are optional for the API; [package.json](package.json) contains scaffold asset commands, not the React application.

Redis, managed broadcasting, and external mail accounts are optional with the example's database cache, synchronous queue, Reverb, and log mail configuration.

## Local development

From this repository:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create an empty local database and set `DB_*` in `.env`. Then run:

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```

Use `http://localhost:8000` as the frontend API origin. Run the React dev server separately on `http://localhost:5173`. Match `FRONTEND_URL` to the actual browser origin. See [configuration](docs/CONFIGURATION.md) for mail, Google login, cache, storage, and deployment.

For live updates, configure local Reverb credentials and run in another terminal:

```bash
php artisan reverb:start
```

See [real-time setup](docs/REALTIME.md). For HTTP-only development set `BROADCAST_CONNECTION=null` instead.

## Optional initialization

On a disposable local database only, `php artisan db:seed` creates demo users, a shared workspace, two sample boards, and randomized card data. The default seeder has fixed demonstration passwords and is not idempotent. Do not run it in production. Inspect [DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) for local accounts.

Global board templates need a separate command:

```bash
php artisan db:seed --class=BoardTemplateSeeder
```

[BoardTemplateSeeder.php](database/seeders/BoardTemplateSeeder.php) defines five templates and their system owner. Default seeding does not call it. `migrate:fresh` drops tables; it is not an onboarding or production upgrade command.

## Testing and tooling

```bash
composer validate --strict
composer check-platform-reqs
vendor/bin/pint --test
```

Before running `composer test` or `php artisan test`, configure a dedicated disposable database. Feature tests use `RefreshDatabase`; never point them at application data. [Development and testing](docs/DEVELOPMENT.md) explains isolation and existing failures. No tracked CI workflow or PHP static-analysis configuration exists.

## Deployment and troubleshooting

Serve only `public/` through the web server, set `APP_ENV=production` and `APP_DEBUG=false`, configure production mail/database/storage, and preserve `APP_KEY`. Back up data before `php artisan migrate --force`. Supervise Reverb when enabled. Current broadcast events run synchronously; a queue worker is needed only when selecting queued application work. Review [configuration](docs/CONFIGURATION.md) and [real-time setup](docs/REALTIME.md) before caching configuration or publishing storage.

For CORS, mail, database, and permission issues, follow [development and troubleshooting](docs/DEVELOPMENT.md). No production deployment or hosted demo was verified.

## Community and license

Read [CONTRIBUTING.md](CONTRIBUTING.md) and [SECURITY.md](SECURITY.md). [renovate.json](renovate.json) is the existing dependency automation configuration.

`composer.json` declares MIT, but no LICENSE file or project copyright notice establishes the intended grant and ownership. Licensing remains blocked pending maintainer confirmation. The audit compares MIT and Apache-2.0 without applying either license.
