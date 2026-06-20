# Laravel Short URL

A REST API service for creating and managing short URLs, built with Laravel 13. Supports JWT authentication, click tracking via queued jobs, Redis caching, and an admin panel for managing all URLs.

## Tech Stack

| | |
|---|---|
| **Runtime** | PHP 8.3+, Laravel 13 |
| **Auth** | JWT (`tymon/jwt-auth`) with refresh token rotation |
| **Database** | SQLite (default) / MySQL |
| **Cache / Queue** | Redis |
| **API Docs** | Scramble (`/docs/api`) |
| **Dev Environment** | Laravel Sail (Docker) |

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (macOS/Windows) or Docker Engine + Docker Compose (Linux)
- Git

---

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd laravel-short-url
```

### 2. Copy environment file

```bash
cp .env.example .env
```

### 3. Install Composer dependencies

If you have PHP 8.3+ and Composer installed locally:

```bash
composer install
```

Without local PHP/Composer, use the Sail Docker image:

```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  laravelsail/php85-composer:latest \
  composer install --ignore-platform-reqs
```

### 4. Configure `.env` for Sail (Docker)

Update the following values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_CLIENT=predis
REDIS_HOST=redis
REDIS_PORT=6379

QUEUE_CONNECTION=database
```

**Linux only** — set file ownership to your user (check with `id -u` and `id -g`):

```env
WWWUSER=1000
WWWGROUP=1000
```

### 5. Configure JWT secrets

```env
JWT_SECRET=<generate-a-random-secret>
JWT_TTL=15

REFRESH_TOKEN_HASH_KEY=<generate-a-random-key>
```

Generate a JWT secret after starting containers (see step 7).

### 6. Start containers

```bash
./vendor/bin/sail up -d --build
```

### 7. Finish setup

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan jwt:secret
./vendor/bin/sail artisan migrate
```

Open [http://localhost](http://localhost).

---

## Daily Development Commands

| Task | Command |
|------|---------|
| Start | `./vendor/bin/sail up -d` |
| Stop | `./vendor/bin/sail down` |
| Artisan | `./vendor/bin/sail artisan <cmd>` |
| Composer | `./vendor/bin/sail composer <cmd>` |
| NPM | `./vendor/bin/sail npm <cmd>` |
| Shell | `./vendor/bin/sail shell` |
| Logs | `./vendor/bin/sail logs -f` |
| Run tests | `./vendor/bin/sail composer test` |

---

## API Reference

Base URL: `http://localhost/api`

Interactive docs are available at [http://localhost/docs/api](http://localhost/docs/api).

### Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/auth/register` | — | Register a new user |
| `POST` | `/auth/admin-register` | — | Register a new admin user |
| `POST` | `/auth/login` | — | Login, returns `access_token` + `refresh_token` |
| `POST` | `/auth/refresh` | — | Rotate refresh token, returns new token pair |
| `POST` | `/auth/logout` | Bearer | Revoke refresh token |
| `GET` | `/auth/me` | Bearer | Get current user info |

### Short URLs (User)

Requires `Authorization: Bearer <access_token>` header except for redirect.

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/short-urls/{shortCode}` | — | Redirect to original URL (increments click count) |
| `GET` | `/short-urls` | Bearer | List your short URLs (paginated) |
| `POST` | `/short-urls` | Bearer | Create a new short URL |
| `GET` | `/short-urls/info/{id}` | Bearer | Get details of a specific short URL |
| `PUT` | `/short-urls/{id}` | Bearer | Update a short URL |
| `DELETE` | `/short-urls/{id}` | Bearer | Delete a short URL |

**Create short URL payload:**

```json
{
  "original_url": "https://example.com/very/long/path",
  "title": "Optional title",
  "expires_at": "2026-12-31 23:59:59"
}
```

### Admin Short URLs

Requires Bearer token from an admin account.

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `GET` | `/admin/short-urls` | Admin | List all short URLs (paginated) |
| `GET` | `/admin/short-urls/{id}` | Admin | Get any short URL by ID |
| `PUT` | `/admin/short-urls/{id}` | Admin | Update any short URL |
| `DELETE` | `/admin/short-urls/{id}` | Admin | Delete any short URL |

---

## Troubleshooting

| Issue | Fix |
|-------|-----|
| `vendor/laravel/sail` not found | Run `composer install` before `sail up` |
| Database connection error | Ensure `.env` uses `DB_HOST=mysql` (not `127.0.0.1`) |
| Permission errors on Linux | Set `WWWUSER` and `WWWGROUP` in `.env` |
| Port already in use | Set `APP_PORT`, `FORWARD_DB_PORT`, or `FORWARD_REDIS_PORT` in `.env` |
| JWT token error | Run `./vendor/bin/sail artisan jwt:secret` |
| Queue jobs not processing | Run `./vendor/bin/sail artisan queue:work` |
