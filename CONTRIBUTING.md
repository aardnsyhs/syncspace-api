# Contributing to the SyncSpace API

Read the [README](README.md), [architecture](docs/ARCHITECTURE.md), [configuration](docs/CONFIGURATION.md), and [testing guide](docs/DEVELOPMENT.md). Original first-party code is covered by [LICENSE](LICENSE); third-party material retains its own terms, as recorded in [THIRD_PARTY_NOTICES.md](THIRD_PARTY_NOTICES.md). Private vulnerability reporting is enabled; [SECURITY.md](SECURITY.md) explains how to use it. The shared [Phase 1 report](https://github.com/aardnsyhs/syncspace/blob/master/docs/OSS_GOVERNANCE_PHASE1.md) records current decisions and restrictions.

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

The maintainer is responsible for issue triage, PR review, merge decisions, releases, and security reporting. Review may require changes; no response-time SLA is promised. No mandatory CLA is introduced. Be respectful and support technical claims with evidence. Use [SECURITY.md](SECURITY.md) for vulnerabilities.

## Triage, scope, and attribution

Maintainers assess reproducibility, affected revisions, impact, and client compatibility when triaging issues. Contributors can help with sanitized reproductions and links to duplicates. A request may need more information or fall outside the maintained API workflows; opening an issue does not commit the project to implementation.

For pull requests, explain authorization effects, response/event compatibility, migration risks, validation, and matching client work. Review may require a smaller change or further evidence. Document upgrade/rollback implications and communicate breaking changes before merging or releasing.

Preserve accurate author metadata and upstream notices. Credit collaborators only with their consent. Identify copied/generated material and its source/license in the pull request; do not imply that credit establishes redistribution permission. Do not submit third-party material whose redistribution rights you cannot establish.

## Maintenance and releases

The maintained API development branch is `main`. No historical release or backport commitment is implied. Future releases follow Semantic Versioning and document compatible client/API versions, API/event/schema changes, and upgrade steps. Releases occur when reviewed and verified changes are ready, without a fixed schedule. See the adopted [maintenance policy](https://github.com/aardnsyhs/syncspace/blob/master/docs/OSS_GOVERNANCE_PHASE1.md#governance-improvements).
