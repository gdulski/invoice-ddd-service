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

### Health Check
```bash
curl -X GET http://localhost:8080/api/health
```

### Invoice Management

#### 1. Create Invoice
```bash
curl -X POST http://localhost:8080/api/invoices \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Jan Kowalski",
    "customer_email": "jan.kowalski@example.com",
    "lines": [
      {
        "product_name": "Web Development",
        "quantity": 10,
        "unit_price_in_cents": 5000
      },
      {
        "product_name": "Consulting",
        "quantity": 5,
        "unit_price_in_cents": 10000
      }
    ]
  }'
```

#### 2. View Invoice (replace {id} with actual invoice ID)
```bash
curl -X GET http://localhost:8080/api/invoices/{id}
```

#### 3. Send Invoice (replace {id} with actual invoice ID)
```bash
curl -X POST http://localhost:8080/api/invoices/{id}/send
```

### Complete Workflow Example
```bash
# 1. Check if service is running
curl -X GET http://localhost:8080/api/health

# 2. Create an invoice and save the ID
INVOICE_ID=$(curl -s -X POST http://localhost:8080/api/invoices \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Test Customer",
    "customer_email": "test@example.com",
    "lines": [
      {
        "product_name": "Test Product",
        "quantity": 1,
        "unit_price_in_cents": 10000
      }
    ]
  }' | jq -r '.id')

echo "Created invoice with ID: $INVOICE_ID"

# 3. View the invoice
curl -X GET http://localhost:8080/api/invoices/$INVOICE_ID

# 4. Send the invoice
curl -X POST http://localhost:8080/api/invoices/$INVOICE_ID/send
```

## 🛠️ Development Commands

### Using Makefile (Recommended)
```bash
# Start the project
make start

# Stop services
make stop

# Restart services
make restart

# View logs
make logs

# Check status
make status

# Run tests
make test
make test-unit
make test-feature
make test-coverage

# Database migrations
make migrate-status
make migrate
make migrate-rollback
make migrate-fresh
make migrate-seed

# Show all available commands
make help
```

### Manual Docker Commands
```bash
# View logs
docker compose logs -f

# Stop services
docker compose down

# Restart services
docker compose restart

# Access app container
docker compose exec app bash

# Run Laravel commands
docker compose exec app php artisan [command]

# Install Composer dependencies
docker compose exec app composer install

# Run migrations
docker compose exec app php artisan migrate
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

## 🎯 Features Implemented

✅ **Domain-Driven Design Architecture**
- Clean layered architecture (Domain → Application → Infrastructure → Interfaces)
- Rich domain models with business logic
- Value objects for type safety
- Domain events for loose coupling

✅ **Invoice Management System**
- Create invoices with customer data and product lines
- View invoice details
- Send invoices (status change from draft to sending)
- Automatic price calculations

✅ **API Endpoints**
- RESTful API with proper HTTP status codes
- JSON request/response format
- Input validation with detailed error messages
- Health check endpoint

✅ **Testing Suite**
- Unit tests for domain layer
- Feature tests for API endpoints
- Test database isolation (SQLite in memory)
- Comprehensive test coverage

✅ **Docker Containerization**
- Multi-container setup (app, nginx, database)
- Development-ready environment
- Easy deployment and scaling

✅ **Developer Experience**
- Makefile with common commands
- Database migration management
- Comprehensive documentation
- Ready-to-use curl examples

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Domain-Driven Design](https://martinfowler.com/bliki/DomainDrivenDesign.html)
- [Docker Documentation](https://docs.docker.com/)
