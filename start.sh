#!/bin/bash

# Invoice DDD Service Startup Script

echo "🚀 Starting Invoice DDD Service..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker and try again."
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker &> /dev/null || ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose v2 is not available. Please install Docker with Compose v2 support."
    echo "   You can install it with: sudo apt install docker-compose-plugin"
    exit 1
fi

# Use docker compose v2
COMPOSE_CMD="docker compose"

# Check if .env file exists, if not copy from example
if [ ! -f .env ]; then
    echo "📋 Creating .env file from template..."
    cp env.example .env
fi

# Build and start the containers
echo "🔨 Building Docker containers..."
$COMPOSE_CMD build

echo "🚀 Starting services..."
$COMPOSE_CMD up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 15

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
$COMPOSE_CMD exec app composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate application key if not exists
echo "🔑 Generating application key..."
$COMPOSE_CMD exec app php artisan key:generate --force

# Run database migrations
echo "🗄️ Running database migrations..."
$COMPOSE_CMD exec app php artisan migrate --force

echo "✅ Invoice DDD Service is now running!"
echo ""
echo "🌐 Application URL: http://localhost:8080"
echo "🏥 Health Check: http://localhost:8080/api/health"
echo "🗄️ Database: localhost:3306"
echo ""
echo "📋 Useful commands:"
echo "  - View logs: $COMPOSE_CMD logs -f"
echo "  - Stop services: $COMPOSE_CMD down"
echo "  - Restart services: $COMPOSE_CMD restart"
echo "  - Access app container: $COMPOSE_CMD exec app bash"
echo ""
echo "🎉 Happy coding!"
