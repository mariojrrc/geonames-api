# GeoNames API

A REST API for geographic data (cities and states) built with **Laravel 12** and **MongoDB**.

## Endpoints

- `GET/POST /api/states` — List / Create states
- `GET/PUT/DELETE /api/states/{id}` — Show / Update / Delete a state
- `DELETE /api/states` — Delete all states
- `GET/POST /api/cities` — List / Create cities
- `GET/PUT/DELETE /api/cities/{id}` — Show / Update / Delete a city
- `DELETE /api/cities` — Delete all cities

## Authentication

All requests require a token header:

```
Authorization: Geonames <token>
```

Tokens are configured in `data/token-config.php`.

## Running the project

1. Run `docker compose up -d`
2. Run `docker exec geonames_app composer install`
3. Test the API at `http://localhost:8080/api/states`

## Running tests

```bash
docker exec geonames_app php artisan test
```

## Linting

```bash
docker exec geonames_app ./vendor/bin/pint --test
```
