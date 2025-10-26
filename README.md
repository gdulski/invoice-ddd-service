# Invoice DDD Service

A Laravel application built with Domain-Driven Design (DDD) principles, fully containerized with Docker.

## 🚀 Quick Start

### Option 1: Using Makefile (Recommended)
```bash
make start
```

### Option 2: Using start.sh script (uses Docker Compose v2)
```bash
./start.sh
```

### Option 3: Manually with docker compose
```bash
docker compose up -d
```

**Note**: The project uses Docker Compose v2 (latest version with full support).

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
- Laravel 12.x
- MySQL 8.0
- Nginx
- Composer

## 🎯 Domain-Driven Design Implementation

This project is built using **Domain-Driven Design (DDD)** principles to ensure clean architecture, maintainability, and scalability. Here's how DDD is implemented:

### Why DDD?

This invoice management system uses DDD to separate business logic from technical concerns. The domain layer (core business logic) is completely independent of Laravel, the database, and other infrastructure details. This allows the business rules to be tested, understood, and maintained independently of technical implementation.

### How DDD is Realized:

**1. Rich Domain Models with Behavior**
- The `Invoice` entity contains business logic, not just data
- Business rules are encapsulated in entities (e.g., `canBeSent()`, `send()`, `markAsSentToClient()`)
- Entities protect their invariants - they never allow invalid states

**2. Value Objects for Type Safety**
- Domain concepts are represented as immutable value objects (`Money`, `Quantity`, `InvoiceStatus`, `CustomerEmail`, etc.)
- Prevents primitive obsession and invalid data structures
- Ensures compile-time type safety and domain validation

**3. Event-Driven Architecture**
- Domain events (`InvoiceCreated`, `InvoiceSent`, `NotificationDelivered`) represent business occurrences
- Events enable loose coupling between components
- Status transitions are driven by domain events, not direct method calls

**4. Layered Architecture with Dependency Inversion**
- **Domain Layer**: Pure business logic, no framework dependencies
- **Application Layer**: Orchestrates domain objects to fulfill use cases
- **Infrastructure Layer**: Implements technical details (database, notifications)
- **Interface Layer**: HTTP controllers, validation, response formatting
- Dependencies point inward - Domain has zero external dependencies

**5. Repository Pattern**
- Domain layer depends on `InvoiceRepositoryInterface`, not concrete implementation
- Persistence details are hidden from business logic
- Domain can be tested without database using in-memory implementations

**6. Application Services Pattern**
- Commands and Queries encapsulate use cases
- Handlers coordinate domain objects and infrastructure
- Thin orchestration layer with no business logic

### Practical Example: Invoice Status Flow

The invoice goes through states that represent business concepts:
- `DRAFT` → Invoice created but not sent
- `SENDING` → Send command triggered, notification in progress
- `SENT_TO_CLIENT` → Notification confirmed delivered

These transitions are driven by domain events and business rules, not arbitrary database updates. The domain ensures only valid state transitions occur.

## 📚 Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Domain-Driven Design](https://martinfowler.com/bliki/DomainDrivenDesign.html)
- [Docker Documentation](https://docs.docker.com/)
