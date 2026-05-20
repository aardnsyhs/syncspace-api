# SaaS Boilerplate — Laravel 12 API

The backend API for the SaaS Boilerplate. Built with Laravel 12, Laravel Sanctum, and Laravel Reverb.

> **Full setup instructions are in the frontend README.** This file covers backend-specific details only.

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
# configure DB_* in .env, then:
php artisan migrate:fresh --seed
php artisan serve          # API on :8000
php artisan reverb:start   # WebSocket server on :8080
```

## Key Directories

```
app/
├── Events/          # Broadcastable WebSocket events
├── Http/Controllers # API controllers (Auth, Board, Card, Team, etc.)
├── Models/          # Eloquent models
├── Policies/        # Authorization policies (Board, Card, Column, Team)
├── Services/        # Business logic (Activity, Analytics, Notification, User)
└── Enums/           # TeamRole, ActivityType

config/
├── board.php        # Default columns, done-column keywords
├── workspace.php    # Default workspace name, system user credentials
├── broadcasting.php # Default driver (reverb)
└── sanctum.php      # Token expiration (default: 30 days)

database/
├── migrations/      # All schema migrations
├── seeders/
│   ├── DatabaseSeeder.php       # Demo users, workspace, boards
│   └── BoardTemplateSeeder.php  # 5 global board templates
└── factories/       # Model factories for testing
```

## Environment Variables

See `.env.example` for the full annotated reference. Key variables:

| Variable | Default | Description |
|---|---|---|
| `BROADCAST_CONNECTION` | `reverb` | WebSocket driver: `reverb` or `ably` |
| `SANCTUM_TOKEN_EXPIRATION` | `43200` | Token lifetime in minutes (30 days) |
| `APP_DEFAULT_WORKSPACE_NAME` | `Personal Workspace` | Auto-created workspace name |
| `SYSTEM_USER_EMAIL` | `system@example.com` | Owner of global board templates |

## Running Tests

```bash
composer test
# or
php artisan test
```
