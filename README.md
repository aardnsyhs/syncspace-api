# SyncSpace API

Backend API untuk aplikasi SyncSpace - Collaborative Kanban Board.

## Tech Stack

-   **Framework**: Laravel 12
-   **PHP**: ^8.2
-   **Auth**: Laravel Sanctum + OAuth (Google)
-   **Realtime**: Ably Broadcasting
-   **Testing**: Pest + PHPUnit

## Quick Start

```bash
# Install dependencies
composer install

# Copy environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
composer dev
```

## API Endpoints

### Authentication

| Method | Endpoint               | Description            |
| ------ | ---------------------- | ---------------------- |
| POST   | `/api/register`        | Register new user      |
| POST   | `/api/login`           | Login                  |
| POST   | `/api/logout`          | Logout                 |
| GET    | `/api/user`            | Get current user       |
| POST   | `/api/forgot-password` | Request password reset |
| POST   | `/api/reset-password`  | Reset password         |
| POST   | `/api/verify-otp`      | Verify OTP             |

### Teams

| Method | Endpoint                  | Description       |
| ------ | ------------------------- | ----------------- |
| GET    | `/api/teams`              | List user's teams |
| POST   | `/api/teams`              | Create team       |
| GET    | `/api/teams/{id}`         | Get team          |
| PUT    | `/api/teams/{id}`         | Update team       |
| DELETE | `/api/teams/{id}`         | Delete team       |
| GET    | `/api/teams/{id}/members` | List members      |
| POST   | `/api/teams/{id}/members` | Add member        |

### Boards

| Method | Endpoint                 | Description  |
| ------ | ------------------------ | ------------ |
| GET    | `/api/teams/{id}/boards` | List boards  |
| POST   | `/api/teams/{id}/boards` | Create board |
| GET    | `/api/boards/{id}`       | Get board    |
| PUT    | `/api/boards/{id}`       | Update board |
| DELETE | `/api/boards/{id}`       | Delete board |

### Cards

| Method | Endpoint                  | Description |
| ------ | ------------------------- | ----------- |
| POST   | `/api/columns/{id}/cards` | Create card |
| GET    | `/api/cards/{id}`         | Get card    |
| PUT    | `/api/cards/{id}`         | Update card |
| DELETE | `/api/cards/{id}`         | Delete card |
| PUT    | `/api/cards/{id}/move`    | Move card   |

## Testing

```bash
# Run all tests
composer test

# Run specific test file
php artisan test tests/Feature/BoardTest.php
```

## Environment Variables

Key environment variables:

-   `APP_URL` - Application URL
-   `DB_*` - Database configuration
-   `ABLY_KEY` - Ably realtime key
-   `GOOGLE_CLIENT_ID/SECRET` - Google OAuth
