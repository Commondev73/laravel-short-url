# Laravel Short URL

Local development uses [Laravel Sail](https://laravel.com/docs/sail). The `vendor/` directory and `.env` file are not committed — set them up after cloning.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (macOS/Windows) or Docker Engine + Docker Compose (Linux)

## First-time setup

```bash
git clone <repository-url>
cd laravel-short-url

cp .env.example .env
```

Update `.env` for Sail:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis
REDIS_PORT=6379
```

On Linux, set file ownership (use values from `id -u` and `id -g`):

```env
WWWUSER=1000
WWWGROUP=1000
```

Install dependencies before starting Docker:

```bash
composer install
```

Without local PHP/Composer:

```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  laravelsail/php85-composer:latest \
  composer install --ignore-platform-reqs
```

Start containers:

```bash
./vendor/bin/sail up -d --build
# or: docker compose up -d --build
```

Finish setup:

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

Open [http://localhost](http://localhost).

## Daily commands

| Task | Command |
|------|---------|
| Start | `./vendor/bin/sail up -d` |
| Stop | `./vendor/bin/sail down` |
| Artisan | `./vendor/bin/sail artisan ...` |
| Composer | `./vendor/bin/sail composer ...` |
| NPM | `./vendor/bin/sail npm ...` |
| Shell | `./vendor/bin/sail shell` |
| Logs | `./vendor/bin/sail logs -f` |

## Troubleshooting

| Issue | Fix |
|-------|-----|
| Build fails — `vendor/laravel/sail` not found | Run `composer install` before `sail up` |
| Database connection error | Ensure `.env` uses `DB_HOST=mysql` |
| Permission errors on Linux | Set `WWWUSER` and `WWWGROUP` in `.env` |
| Port already in use | Set `APP_PORT`, `FORWARD_DB_PORT`, or `FORWARD_REDIS_PORT` in `.env` |
