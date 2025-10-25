.PHONY: start stop restart logs clean status help test test-unit test-feature test-coverage migrate migrate-status migrate-rollback migrate-fresh migrate-seed

help:
	@echo "Dostępne komendy:"
	@echo "  make start   - Uruchom projekt"
	@echo "  make stop    - Zatrzymaj projekt"
	@echo "  make restart - Zrestartuj projekt"
	@echo "  make logs    - Pokaż logi"
	@echo "  make status  - Sprawdź status"
	@echo "  make clean   - Wyczyść wszystko"
	@echo ""
	@echo "🧪 Testy:"
	@echo "  make test         - Uruchom wszystkie testy"
	@echo "  make test-unit    - Uruchom tylko testy jednostkowe"
	@echo "  make test-feature - Uruchom tylko testy funkcjonalne"
	@echo "  make test-coverage - Uruchom testy z pokryciem kodu"
	@echo ""
	@echo "🗄️ Migracje:"
	@echo "  make migrate-status - Pokaż status migracji"
	@echo "  make migrate        - Uruchom migracje"
	@echo "  make migrate-rollback - Cofnij ostatnią migrację"
	@echo "  make migrate-fresh  - Usuń wszystkie tabele i uruchom migracje od nowa"
	@echo "  make migrate-seed   - Uruchom migracje i seedery"

start:
	@echo "🚀 Starting Invoice DDD Service..."
	@if ! docker info > /dev/null 2>&1; then \
		echo "❌ Docker is not running. Please start Docker and try again."; \
		exit 1; \
	fi
	@if [ ! -f .env ]; then \
		echo "📋 Creating .env file from template..."; \
		cp env.example .env; \
	fi
	@echo "🔨 Building Docker containers..."
	docker compose build
	@echo "🚀 Starting services..."
	docker compose up -d
	@echo "⏳ Waiting for services to be ready..."
	sleep 15
	@echo "📦 Installing Composer dependencies..."
	docker compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader
	@echo "🔑 Generating application key..."
	docker compose exec app php artisan key:generate --force
	@echo "🗄️ Running database migrations..."
	docker compose exec app php artisan migrate --force
	@echo "✅ Invoice DDD Service is now running!"
	@echo ""
	@echo "🌐 Application URL: http://localhost:8080"
	@echo "🏥 Health Check: http://localhost:8080/api/health"
	@echo "🗄️ Database: localhost:3306"
	@echo ""
	@echo "📋 Useful commands:"
	@echo "  - View logs: make logs"
	@echo "  - Stop services: make stop"
	@echo "  - Restart services: make restart"
	@echo "  - Access app container: docker compose exec app bash"
	@echo ""
	@echo "🎉 Happy coding!"

stop:
	@echo "🛑 Stopping services..."
	docker compose down

restart:
	@echo "🔄 Restarting services..."
	docker compose restart

logs:
	@echo "📄 Viewing logs..."
	docker compose logs -f

status:
	@echo "📊 Checking container status..."
	docker compose ps

clean:
	@echo "🧹 Cleaning up Docker resources..."
	docker compose down --rmi all --volumes
	@echo "✅ Cleaned up."

test:
	@echo "🧪 Running all tests..."
	docker compose exec app php artisan test

test-unit:
	@echo "🔬 Running unit tests..."
	docker compose exec app php artisan test --testsuite=Unit

test-feature:
	@echo "🎭 Running feature tests..."
	docker compose exec app php artisan test --testsuite=Feature

test-coverage:
	@echo "📊 Running tests with coverage..."
	docker compose exec app php artisan test --coverage

# Migration commands
migrate-status:
	@echo "📊 Checking migration status..."
	docker compose exec app php artisan migrate:status

migrate:
	@echo "🗄️ Running migrations..."
	docker compose exec app php artisan migrate --force

migrate-rollback:
	@echo "↩️ Rolling back last migration..."
	docker compose exec app php artisan migrate:rollback --force

migrate-fresh:
	@echo "🔄 Fresh migration (drop all tables and re-run migrations)..."
	docker compose exec app php artisan migrate:fresh --force

migrate-seed:
	@echo "🌱 Running migrations and seeders..."
	docker compose exec app php artisan migrate:fresh --seed --force
