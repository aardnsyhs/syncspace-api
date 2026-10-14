# Contributing to the SyncSpace API

Read the [README](README.md), [architecture](docs/ARCHITECTURE.md), [configuration](docs/CONFIGURATION.md), and [testing guide](docs/DEVELOPMENT.md). License ownership and private security reporting still require maintainer decisions.

## Local workflow

Fork and clone your fork:

```bash
git clone https://github.com/YOUR_USERNAME/syncspace-api.git
cd syncspace-api
git switch -c fix/describe-the-change
composer install
cp .env.example .env
php artisan key:generate
```

Replace `YOUR_USERNAME` with your GitHub account. Configure an empty local MySQL-compatible database before migrating. Start API and Reverb in separate terminals as described in the README. Run the React client separately when testing browser flows.

`fix/...` and `feature/...` branch names are suggestions, not enforced policy. Recent history uses prefixes such as `fix:` and `chore:`; no commit-message hook enforces them.

Follow the affected controller, policy, resource, and service patterns. Authorize resource operations on the server; client permission checks are insufficient. Use migrations for schema changes and explain rollback/data-loss implications. Keep response envelopes and broadcast names/payloads compatible with the client.

## Validation

```bash
composer validate --strict
composer check-platform-reqs
vendor/bin/pint --test
```

Run `composer test` only after configuring a disposable test database as described in [DEVELOPMENT.md](docs/DEVELOPMENT.md). Tests can recreate tables. Never use application or production data. Pint currently reports broad baseline formatting issues; do not reformat unrelated files in a focused contribution. No configured PHP static-analysis tool or tracked CI workflow exists.

Add meaningful Pest tests for changed authorization, validation, data behavior, or API contracts. Report command outputs and separate baseline failures from regressions. Dependency changes require lockfile review and a fresh advisory/license check; do not silently change versions just to satisfy a check.

## Issues and reviews

Bug reports should include the endpoint/workflow, expected and observed behavior, reproduction steps, PHP/database versions, and sanitized output. Feature requests should explain the current limitation and proposed workflow. Avoid credentials, personal data, and exploitable security details in public issues.

Keep pull requests focused. Explain the resulting behavior, validation, documentation changes, and any matching frontend work. Discuss breaking API/schema changes with the maintainer before implementation and provide a compatibility/deployment plan.

No review SLA, release cadence, CLA, or contributor approval policy is established in tracked files. Maintainers decide merges and may request changes. Be respectful and support technical claims with evidence. Use [SECURITY.md](SECURITY.md) for vulnerabilities.
