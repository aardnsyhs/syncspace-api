# API development, tests, and troubleshooting

## Isolating tests

[phpunit.xml](../phpunit.xml) selects MySQL and `syncspace_api_test`. [tests/Pest.php](../tests/Pest.php) applies `RefreshDatabase` to feature tests, so the suite may recreate database tables. Never run against production or valuable local data.

Create a separate empty MySQL-compatible database and restricted test user. Put only local testing credentials in an ignored `.env.testing`:

```dotenv
APP_ENV=testing
APP_KEY=replace-with-your-local-test-application-key
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=syncspace_api_test
DB_USERNAME=your-local-test-user
DB_PASSWORD=your-local-test-password
BROADCAST_CONNECTION=null
MAIL_MAILER=array
CACHE_STORE=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
```

The example key and credentials are placeholders, not usable values. A valid Laravel key can be generated with `php artisan key:generate --show`; put it in the ignored testing file. Match `DB_DATABASE` to `phpunit.xml`. Unset exported production `DB_*` and `DB_URL` values in the test shell; environment variables can override dotenv configuration. Ensure cached application configuration does not point tests at a different database. Use a separate checkout/container for testing when sharing an application installation.

Then, from the repository:

```bash
composer test
```

The script clears configuration cache and runs `php artisan test`. The audit instead ran `vendor/bin/pest` with a temporary XML configuration that forced a disposable Unix-socket database and disabled mail/broadcast delivery. That avoids changing the existing installation's `.env` or cached configuration. It is not evidence that arbitrary contributor database setups work.

## Tooling and coverage

```bash
composer validate --strict
composer check-platform-reqs
vendor/bin/pint --test
```

Pint checks formatting without rewriting files. The baseline has widespread style issues, recorded in the joint audit. No PHPStan/Psalm configuration or tracked CI workflow exists. Feature tests cover authentication, board/card flows, checklists, analytics, public boards, and roles. OTP expiry/resend/reset, Google provider exchanges, real WebSocket delivery, and deployment behavior need further targeted coverage.

`composer setup` also performs migrations and installs/builds scaffold Node assets. It is not used for the minimal API instructions because contributors should configure the database before migration. Backend `npm run dev` runs scaffold Vite only; it is not the React client. There is no tracked backend Node lockfile, so scaffold asset installation is not reproducible from a committed lockfile.

## Setup checks

With a running local server:

```bash
curl -f http://localhost:8000/api/health
curl -f http://localhost:8000/api/health/app
```

The first endpoint checks HTTP availability; the second attempts a database connection. Neither proves application correctness, mail delivery, or broadcast delivery. Inspect `php artisan route:list --path=api` for current paths. API resource responses commonly use a `data` envelope; inspect controller/resources rather than assuming every endpoint shares one shape.

With `MAIL_MAILER=log`, OTP/reset mail goes to configured Laravel logs. Treat those logs as sensitive and redact them from issue reports. For a local demo, inspect the default seeder's accounts; real signup requires the verification flow.

## Troubleshooting

| Symptom | Investigation |
|---|---|
| Database connection fails | Create the database, check `DB_*`, PHP's `pdo_mysql`, grants, and unintended `DB_URL` overrides. |
| Migration fails on PostgreSQL or SQLite | Use a MySQL-compatible database; current raw migration SQL is not portable. |
| Templates are absent | Run the separate template seeder on the intended local database. Default demo seeding does not call it. |
| API returns 401 or 403 | Check token validity, team membership, and policy/controller restrictions. Do not bypass authorization. |
| CORS or Google redirect mismatch | Match frontend origin, CORS configuration, and provider redirect exactly. |
| Mail is not received | The example logs mail; production needs a configured delivery provider. Inspect redacted errors. |
| Changes save but live updates fail | Check [REALTIME.md](REALTIME.md) and server broadcasting selection. |
| Files are missing | Confirm the public disk, upload limits, filesystem permissions, and intended storage link. |
| Workspace name differs from the setting | The observer uses the user's name; the explicit UserService path uses the configurable name. |

The joint [audit](https://github.com/aardnsyhs/syncspace/blob/master/docs/OSS_READINESS_AUDIT.md) records executed checks and remaining maintainer decisions.
