# AGENTS.md

## Cursor Cloud specific instructions

### Project Overview

GeoNames API — a PHP REST API built on Zend Framework Apigility with MongoDB. Two main endpoints: `/cities` and `/states` (CRUD). Runs in Docker (PHP 7.2 + Apache + MongoDB).

### Starting Services

```bash
# Start Docker daemon (required in cloud VM — no systemd)
sudo containerd &>/dev/null &
sleep 2
sudo dockerd &>/dev/null &
sleep 3

# Start the application
cd /workspace && sudo docker compose up -d
```

After containers are running, install dependencies inside the apigility container:
```bash
sudo docker exec geonames_apigility composer install --no-interaction --no-plugins --ignore-platform-req=composer-plugin-api --ignore-platform-req=ext-memcached
```

The API will be available at `http://localhost:8080`.

### Dockerfile Fixes (already applied)

The original Dockerfile required three fixes for modern environments:
1. **Debian Buster archive repos** — Buster is EOL; apt sources must point to `archive.debian.org`
2. **libcurl3 → libcurl4** — package was renamed in Debian Buster
3. **mongodb PECL extension** — must be pinned to `mongodb-1.15.3` (last version supporting PHP 7.2)

### Composer Fixes (already applied)

- `roave/security-advisories` (dev-master) was removed from `require-dev` — it now blocks PHPUnit 6.x
- `zfcampus/zf-deploy` was removed from `require-dev` — its transitive dependency `herrera-io/json` has been deleted from GitHub
- `platform.php` is set to `7.2.34` in composer.json to constrain dependency resolution to PHP 7.2-compatible versions
- Must use `--no-plugins --ignore-platform-req=composer-plugin-api` flags because `zend-component-installer` requires Composer plugin API v1

### Running Lint

```bash
sudo docker exec geonames_apigility bash -c 'cd /var/www && vendor/bin/phpcs'
```

Pre-existing style violations exist; these are not regressions.

### Running Tests

```bash
sudo docker exec geonames_apigility bash -c 'cd /var/www && vendor/bin/phpunit'
```

- 2 of 4 tests pass (IndexController tests)
- 2 integration tests (StatesApi) fail because the test config intentionally excludes `local.php` (MongoDB config). These are pre-existing failures.

### API Authentication

Requests require `Authorization: Geonames <token>` header. The token `b17d8756cc299c0c897454ee4dd0e58` is configured in `data/token-config.php`.

### Configuration

- Copy `config/autoload/doctrine-mongo-odm.local.php.dist` → `config/autoload/doctrine-mongo-odm.local.php` (done once during setup)
- Copy `config/autoload/local.php.dist` → `config/autoload/local.php` (done once during setup)
- MongoDB connects to `10.5.0.6:27017` (docker-compose network IP) with credentials `root`/`example`

### Docker Network

Custom bridge network `10.5.0.0/16`:
- apigility container: `10.5.0.5`
- mongo container: `10.5.0.6`
