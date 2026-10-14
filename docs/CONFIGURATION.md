# API configuration and deployment

[.env.example](../.env.example) is the local starting point. Configuration reads live in [config](../config); source definitions take precedence over comments. Do not commit `.env`, connection strings, keys, tokens, or mail output containing verification codes.

## Required local settings

| Variables | Purpose and source |
|---|---|
| `APP_KEY`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, `APP_NAME` | Application encryption, environment, errors, URLs, and mail identity in [app.php](../config/app.php). Generate a key for a fresh local installation only. |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | [database.php](../config/database.php). Use `mysql` with an existing local database; the current migrations are MySQL-specific. |
| `FRONTEND_URL` | Client origin, CORS, and public board/email URLs in [app.php](../config/app.php) and [cors.php](../config/cors.php). Match the browser hostname exactly. |
| `MAIL_MAILER`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | [mail.php](../config/mail.php). `log` supports local OTP/reset inspection without an external mail provider. |
| `BROADCAST_CONNECTION`, `REVERB_APP_*`, `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME` | Live updates; see [REALTIME.md](REALTIME.md). Use `null` to disable broadcasting for HTTP-only development. |

For a fresh local database, `php artisan migrate` initializes the schema. Database cache/session tables are included. Redis is not required by the default example. Demo initialization is optional and described in the [README](../README.md).

## Optional settings

| Variables | Behavior |
|---|---|
| `FRONTEND_URL_PROD` | Additional CORS origin read by `config/cors.php`, absent from the example. Set only if a separate allowed origin is needed. |
| `SANCTUM_STATEFUL_DOMAINS`, `SANCTUM_TOKEN_EXPIRATION` | Stateful origins and token lifetime; [sanctum.php](../config/sanctum.php) defaults expiration to 43200 minutes. The client sends bearer tokens. |
| `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` | Optional Google code exchange in [services.php](../config/services.php). The URI must match the browser origin plus `/auth/google/callback`. The frontend needs the public client ID only. |
| `MAIL_SCHEME`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` | SMTP settings for real mail delivery. Use actual provider configuration rather than assuming a default transport. |
| `QUEUE_CONNECTION`, `CACHE_STORE`, `SESSION_DRIVER` | [queue.php](../config/queue.php), [cache.php](../config/cache.php), [session.php](../config/session.php). The example uses `sync`, `database`, and `database`. |
| `REDIS_*`, `MEMCACHED_HOST` | Required only when selecting the corresponding cache/queue/reverb scaling configuration. |
| `FILESYSTEM_DISK`, `AWS_*` | Default disk and optional S3 driver configuration in [filesystems.php](../config/filesystems.php). Current avatar/attachment controllers explicitly use the `public` disk; changing the default alone does not migrate uploads to S3. |
| `APP_DEFAULT_WORKSPACE_NAME` | Name used by the explicit UserService workspace path, not the observer-created team. See [architecture limitations](ARCHITECTURE.md). |
| `SYSTEM_USER_EMAIL`, `SYSTEM_USER_NAME` | Owner identity used by the global template seeder; not a security reporting contact. |
| `LOG_*`, `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE` | Logging and locale settings inherited from Laravel configuration. |

Other framework/provider variables are defined in `config/` for optional drivers. The example is not an exhaustive catalog of every framework option. Backend `VITE_APP_NAME` applies to scaffold assets, not the separate React application's configuration.

## Storage and production

Uploads currently use local public storage. `php artisan storage:link` publishes that disk through the web server. Confirm that the deployment's storage exposure matches the sensitivity of your data before enabling it. Keep secrets and logs outside the public web root. No malware-scanning service is configured.

Serve `public/` only. Set `APP_ENV=production` and `APP_DEBUG=false`; configure database, real mail delivery, HTTPS URLs, CORS, and storage permissions. Preserve the existing application key. Back up data and review migration changes before running `php artisan migrate --force`; never use default demo seeders on a production database.

After setting environment values, Laravel supports:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Rebuild caches after changing configuration and restart long-running processes. These deployment commands were inspected but were not run against the existing installation during the audit.

[REALTIME.md](REALTIME.md) covers Reverb supervision and TLS. A queue worker is needed when using asynchronous queues; current application broadcasts use `ShouldBroadcastNow`, and the example queue is synchronous. No scheduler dispatches due-date reminders. Backend `composer dev` also starts scaffold Vite/Pail processes, does not start Reverb, and does not serve the separate React app.
