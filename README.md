# Invoice DDD Service

A Laravel application built with Domain-Driven Design (DDD) principles, fully containerized with Docker.

## 🚀 Quick Start

### Opcja 1: Używając Makefile (zalecane)
```bash
make start
```

### Opcja 2: Używając skryptu start.sh (używa Docker Compose v2)
```bash
./start.sh
```

### Opcja 3: Ręcznie z docker compose
```bash
docker compose up -d
```

**Uwaga**: Projekt używa Docker Compose v2 (najnowsza wersja z pełnym wsparciem).

## 🏗️ Architecture

This project follows Domain-Driven Design principles with the following structure:

```
src/
├── Domain/                 # Business logic and rules
│   ├── Entities/          # Core business objects
│   ├── ValueObjects/      # Immutable objects with no identity
│   ├── Repositories/      # Data access interfaces
│   ├── Events/           # Domain events
│   └── Services/         # Domain services
├── Application/           # Application layer
│   ├── Commands/         # Command objects
│   ├── Queries/          # Query objects
│   ├── Handlers/         # Command/Query handlers
│   └── DTOs/            # Data Transfer Objects
├── Infrastructure/        # External concerns
│   ├── Persistence/      # Database implementations
│   └── ExternalServices/ # Third-party integrations
└── Presentation/         # Interface layer
    ├── Controllers/      # HTTP controllers
    ├── Requests/        # Form requests
    └── Resources/       # API resources
```

## 🐳 Docker Services

- **app**: PHP 8.2-FPM with Laravel
- **nginx**: Web server (port 8080)
- **db**: MySQL 8.0 (port 3306)

## 📋 Available Endpoints

- `GET /` - Welcome page
- `GET /api/health` - Health check endpoint

## 🛠️ Development Commands

```bash
# View logs
docker-compose logs -f

# Stop services
docker-compose down

# Restart services
docker-compose restart

# Access app container
docker-compose exec app bash

# Run Laravel commands
docker-compose exec app php artisan [command]

# Install Composer dependencies
docker-compose exec app composer install

# Run migrations
docker-compose exec app php artisan migrate
```

## 🔧 Configuration

The application uses environment variables for configuration. Copy `env.example` to `.env` and adjust as needed:

- Database connection settings
- Application key
- Debug mode
- Log levels

## 📦 Dependencies

- PHP 8.2+
- Laravel 10.x
- MySQL 8.0
- Nginx
- Composer

## 🎯 Next Steps

This is a basic setup with a health endpoint. Future development should include:

1. Domain models for Invoice, Customer, etc.
2. Repository implementations
3. Command/Query handlers
4. API endpoints for CRUD operations
5. Validation and error handling
6. Testing suite

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Domain-Driven Design](https://martinfowler.com/bliki/DomainDrivenDesign.html)
- [Docker Documentation](https://docs.docker.com/)
