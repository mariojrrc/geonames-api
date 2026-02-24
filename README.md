# GeoNames API

A REST API and React frontend for managing geographic data (states and cities), built with **Laravel 12**, **PHP 8.4**, **MongoDB**, and **React + Vite + Tailwind CSS**.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 (PHP 8.4) |
| Database | MongoDB (via [mongodb/laravel-mongodb](https://github.com/mongodb/laravel-mongodb)) |
| Frontend | React 19, Vite 7, Tailwind CSS 4 |
| Testing | PHPUnit 11 |
| Linting | Laravel Pint |
| CI | GitHub Actions |
| Infrastructure | Docker Compose (PHP 8.4 Apache + MongoDB) |

## Getting Started

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and Docker Compose

### Running with Docker

```bash
# Start containers (builds PHP 8.4 + Apache image and MongoDB)
docker compose up -d

# Install PHP dependencies
docker exec geonames_app composer install

# The API is now available at http://localhost:8080/api
# The React frontend is available at http://localhost:8080
```

### Running without Docker

If you prefer to run locally, you will need PHP 8.4 with the `mongodb` extension, Composer, Node.js 22+, and a running MongoDB instance.

```bash
# Install dependencies
composer install
npm install

# Copy environment config and generate app key
cp .env.example .env
php artisan key:generate

# Edit .env to point MONGODB_URI to your MongoDB instance

# Build the frontend
npm run build

# Start the development server
php artisan serve
```

## Frontend

The project includes a React single-page application served at the root URL (`/`). It provides a read-only browser for the API:

- **States tab** — lists all states in a paginated table; click a row to view the raw JSON from `GET /api/states/{id}`
- **Cities tab** — lists all cities with a `stateId` filter; click a row to view detail JSON
- **Token field** — editable authentication token in the header bar (pre-filled with the default token)

To rebuild the frontend after making changes:

```bash
npm run build      # production build
npm run dev        # development server with HMR
```

## API Reference

All API endpoints are prefixed with `/api` and require an `Authorization` header:

```
Authorization: Geonames <token>
```

The default token `b17d8756cc299c0c897454ee4dd0e58` is configured in `data/token-config.php`.

### States

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/states` | List states (paginated). Query params: `pageSize`, `shortName` |
| `POST` | `/api/states` | Create a state. Body: `{ "name": "...", "shortName": "XX" }` |
| `GET` | `/api/states/{id}` | Get a single state |
| `PUT` | `/api/states/{id}` | Update a state |
| `DELETE` | `/api/states/{id}` | Delete a state |
| `DELETE` | `/api/states` | Delete all states |

**Validation rules:**
- `name` — required, string, 3–100 characters
- `shortName` — required, string, exactly 2 characters (auto-uppercased)

### Cities

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/cities` | List cities (paginated). Query params: `pageSize`, `stateId` |
| `POST` | `/api/cities` | Create a city. Body: `{ "name": "...", "stateId": "..." }` |
| `GET` | `/api/cities/{id}` | Get a single city |
| `PUT` | `/api/cities/{id}` | Update a city |
| `DELETE` | `/api/cities/{id}` | Delete a city |
| `DELETE` | `/api/cities` | Delete all cities |

**Validation rules:**
- `name` — required, string, 3–100 characters
- `stateId` — required, string

### Example Requests

```bash
# Create a state
curl -X POST http://localhost:8080/api/states \
  -H "Content-Type: application/json" \
  -H "Authorization: Geonames b17d8756cc299c0c897454ee4dd0e58" \
  -d '{"name": "São Paulo", "shortName": "SP"}'

# List states
curl http://localhost:8080/api/states \
  -H "Authorization: Geonames b17d8756cc299c0c897454ee4dd0e58"

# Create a city
curl -X POST http://localhost:8080/api/cities \
  -H "Content-Type: application/json" \
  -H "Authorization: Geonames b17d8756cc299c0c897454ee4dd0e58" \
  -d '{"name": "São Paulo", "stateId": "<state_id>"}'
```

## Testing

```bash
# Run all tests (23 tests, 50 assertions)
docker exec geonames_app php artisan test

# Run with filter
docker exec geonames_app php artisan test --filter=StatesApiTest
```

## Linting

```bash
# Check code style
docker exec geonames_app ./vendor/bin/pint --test

# Auto-fix code style
docker exec geonames_app ./vendor/bin/pint
```

## CI/CD

GitHub Actions runs on every push to `master` and on pull requests:

- **Lint job** — PHP 8.4 + Laravel Pint
- **Test job** — PHP 8.4 + MongoDB service + Node.js 22 (frontend build) + PHPUnit

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # StateController, CityController
│   │   ├── Middleware/          # TokenAuthentication
│   │   └── Requests/           # Form request validation classes
│   └── Models/                 # State, City (MongoDB Eloquent models)
├── data/
│   └── token-config.php        # API token configuration
├── resources/
│   ├── js/
│   │   ├── app.jsx             # React entry point
│   │   └── components/         # App, StatesPage, CitiesPage
│   └── css/app.css             # Tailwind CSS entry
├── routes/
│   ├── api.php                 # API routes (states, cities)
│   └── web.php                 # SPA catch-all route
├── tests/Feature/              # API integration tests
├── docker-compose.yml          # PHP 8.4 Apache + MongoDB
├── Dockerfile                  # PHP 8.4 image with mongodb extension
└── .github/workflows/ci.yml   # GitHub Actions CI
```

## License

[MIT](https://opensource.org/licenses/MIT)
