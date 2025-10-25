# Invoice DDD Service

A Laravel-based Invoice Management System built following Domain-Driven Design (DDD) principles.

## 🏗️ Architecture

This application follows Domain-Driven Design with a clean layered architecture:

```
Interfaces → Application → Domain ← Infrastructure
```

### Layer Responsibilities

- **Domain Layer**: Pure business logic, entities, value objects, domain services
- **Application Layer**: Use cases, orchestration, application services  
- **Infrastructure Layer**: Technical implementations (database, APIs, file system)
- **Interfaces Layer**: Entry points (HTTP, CLI, Queue workers)

## 📋 Invoice Structure

The invoice contains the following fields:

- **Invoice ID**: Auto-generated during creation
- **Invoice Status**: Enum with possible states: `draft`, `sending`, and `sent-to-client`
- **Customer Name**: String, max 255 characters
- **Customer Email**: Valid email address, max 255 characters
- **Invoice Product Lines**, each with:
  - **Product Name**: String, max 255 characters
  - **Quantity**: Integer, must be positive
  - **Unit Price**: Integer (in cents), must be positive
  - **Total Unit Price**: Calculated as Quantity × Unit Price
- **Total Price**: Sum of all Total Unit Prices

## 🚀 Getting Started

### Prerequisites

- Docker and Docker Compose
- Git

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd invoice-ddd-service
```

2. Start the application:
```bash
./start.sh
```

The script will:
- Build Docker containers
- Install dependencies
- Generate application key
- Run database migrations
- Start all services

### Access Points

- **Application**: http://localhost:8080
- **Health Check**: http://localhost:8080/api/health
- **Database**: localhost:3306

## 📡 API Endpoints

### 1. Create Invoice

**POST** `/api/invoices`

Creates a new invoice with the provided customer information and product lines.

#### Request Body

```json
{
  "customer_name": "John Doe",
  "customer_email": "john.doe@example.com",
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
}
```

#### Response

```json
{
  "id": "inv_65a1b2c3d4e5f6",
  "status": "draft",
  "customer_name": "John Doe",
  "customer_email": "john.doe@example.com",
  "lines": [
    {
      "product_name": "Web Development",
      "quantity": 10,
      "unit_price_in_cents": 5000,
      "total_unit_price_in_cents": 50000
    },
    {
      "product_name": "Consulting",
      "quantity": 5,
      "unit_price_in_cents": 10000,
      "total_unit_price_in_cents": 50000
    }
  ],
  "total_price_in_cents": 100000,
  "created_at": "2024-01-15 10:30:00"
}
```

### 2. View Invoice

**GET** `/api/invoices/{id}`

Retrieves invoice data by ID.

#### Response

Same format as Create Invoice response.

### 3. Send Invoice

**POST** `/api/invoices/{id}/send`

Handles the sending of an invoice. Changes status from `draft` to `sending` and triggers notification events.

#### Response

Same format as Create Invoice response, with updated status.

## 🔧 Development

### Useful Commands

```bash
# View logs
docker compose logs -f

# Stop services
docker compose down

# Restart services
docker compose restart

# Access app container
docker compose exec app bash

# Run migrations
docker compose exec app php artisan migrate

# Run tests
docker compose exec app php artisan test
```

### Project Structure

```
src/
├── Domain/                 # Business logic layer
│   ├── Entities/          # Domain entities
│   ├── ValueObjects/      # Immutable value objects
│   ├── Repositories/      # Repository interfaces
│   ├── Events/           # Domain events
│   └── Services/         # Domain services
├── Application/          # Use cases layer
│   ├── DTOs/            # Data transfer objects
│   └── Handlers/        # Command/Query handlers
├── Infrastructure/       # Technical implementation
│   ├── Persistence/     # Database implementations
│   └── ExternalServices/ # External integrations
└── Presentation/        # Interface layer
    ├── Controllers/     # HTTP controllers
    ├── Requests/       # Form validation
    └── Resources/      # API response formatting
```

## 🧪 Testing

The application includes comprehensive tests following DDD principles:

- **Unit Tests**: Domain layer with 100% coverage
- **Integration Tests**: Application layer handlers
- **Feature Tests**: End-to-end API testing

Run tests:
```bash
docker compose exec app php artisan test
```

## 📝 Domain Events

The system emits domain events for significant business occurrences:

- **InvoiceCreated**: Triggered when a new invoice is created
- **InvoiceSent**: Triggered when an invoice is sent to a client

Events are handled by the `InvoiceNotificationService` which logs the events and can be extended to integrate with external notification systems.

## 🔒 Validation Rules

### Invoice Creation
- Customer name: Required, max 255 characters
- Customer email: Required, valid email format, max 255 characters
- Lines: Optional (can be empty or omitted)
- Product name: Required when lines are provided, max 255 characters
- Quantity: Required when lines are provided, must be positive integer (> 0)
- Unit price: Required when lines are provided, must be positive integer (> 0) in cents

### Invoice Sending
- Invoice must exist
- Invoice must be in `draft` status
- Invoice must contain at least one product line with both quantity and unit price as positive integers greater than zero

## 🎯 Business Rules

1. **Invoice Status Flow**: `draft` → `sending` → `sent-to-client`
2. **Invoice Creation**: An invoice can only be created in draft status
3. **Empty Lines**: An invoice can be created with empty product lines
4. **Sending Constraints**: An invoice can only be sent if it is in draft status and contains product lines
5. **Product Lines**: To be sent, an invoice must contain product lines with both quantity and unit price as positive integers greater than zero
6. **Status Transition**: An invoice can only be marked as sent-to-client if its current status is sending
7. **Price Calculations**: All prices are stored in cents to avoid floating-point precision issues
8. **Immutable Value Objects**: All domain value objects are immutable and validate their state

## 🚀 Deployment

The application is containerized and ready for deployment:

1. Build the Docker image
2. Configure environment variables
3. Run database migrations
4. Start the application

## 📚 DDD Principles Applied

- **Rich Domain Models**: Entities contain behavior, not just data
- **Value Objects**: Immutable objects for concepts like Money, Quantity
- **Domain Events**: Event-driven architecture for loose coupling
- **Repository Pattern**: Abstract data access behind interfaces
- **Dependency Inversion**: Domain depends on abstractions, not implementations
- **Ubiquitous Language**: Business terminology used consistently

## 🤝 Contributing

1. Follow DDD principles
2. Write tests for new features
3. Maintain clean architecture boundaries
4. Use meaningful names and comments
5. Follow PSR-12 coding standards

## 📄 License

This project is licensed under the MIT License.

