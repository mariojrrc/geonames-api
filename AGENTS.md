# AGENTS.md

## Cursor Cloud specific instructions

### Project Overview

GeoNames API — a REST API for geographic data (cities and states) built with **Laravel 12** (PHP 8.4) and **MongoDB**. Runs in Docker.

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

After containers start, install dependencies:
```bash
sudo docker exec geonames_app composer install --no-interaction
```

API available at `http://localhost:8080/api/`.

### Endpoints

- `GET/POST /api/states` — List / Create states
- `GET/PUT/DELETE /api/states/{id}` — Show / Update / Delete a state
- `DELETE /api/states` — Delete all states
- `GET/POST /api/cities` — List / Create cities
- `GET/PUT/DELETE /api/cities/{id}` — Show / Update / Delete a city
- `DELETE /api/cities` — Delete all cities

### Authentication

Requests require `Authorization: Geonames <token>` header. Token `b17d8756cc299c0c897454ee4dd0e58` is configured in `data/token-config.php`.

### Running Tests

```bash
sudo docker exec geonames_app php artisan test
```

### Running Lint

```bash
sudo docker exec geonames_app ./vendor/bin/pint --test
```

Auto-fix: `sudo docker exec geonames_app ./vendor/bin/pint`

### Key Files

- `app/Models/State.php`, `app/Models/City.php` — MongoDB Eloquent models
- `app/Http/Controllers/Api/` — REST controllers
- `app/Http/Requests/` — Form request validation
- `app/Http/Middleware/TokenAuthentication.php` — Token auth middleware
- `routes/api.php` — API route definitions
- `data/token-config.php` — API token configuration
- `config/database.php` — MongoDB connection config

### Docker

- `geonames_app` — PHP 8.4 + Apache + Laravel (port 8080)
- `geonames_mongodb` — MongoDB (port 27017, user: root / password: example)
- Custom bridge network `10.5.0.0/16`
