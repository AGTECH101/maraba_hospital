#!/bin/bash

# Maraba Hospital - Quick Docker Deployment Script
# This script helps quickly deploy the application to production

set -e

echo "======================================"
echo "Maraba Hospital - Deployment Script"
echo "======================================"
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Error: Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Error: Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

echo "✓ Docker and Docker Compose detected"
echo ""

# Check if .env.production exists
if [ ! -f .env.production ]; then
    echo "Creating .env.production from template..."
    cp .env.production.example .env.production
    echo "⚠ Please edit .env.production with your production settings"
    exit 1
fi

echo "✓ .env.production found"
echo ""

# Ask for deployment environment
echo "Select deployment environment:"
echo "1) Development (docker-compose.yml)"
echo "2) Production (docker-compose.prod.yml)"
read -p "Enter choice [1-2]: " choice

case $choice in
    1)
        COMPOSE_FILE="docker-compose.yml"
        ENV="Development"
        ;;
    2)
        COMPOSE_FILE="docker-compose.prod.yml"
        ENV="Production"
        ;;
    *)
        echo "Invalid choice. Exiting."
        exit 1
        ;;
esac

echo ""
echo "Deploying to $ENV environment using $COMPOSE_FILE"
echo ""

# Build images
echo "Building Docker images..."
docker-compose -f $COMPOSE_FILE build --no-cache

# Start services
echo ""
echo "Starting services..."
docker-compose -f $COMPOSE_FILE up -d

# Wait for database to be ready
echo ""
echo "Waiting for database to be ready..."
sleep 10

# Run migrations
echo "Running database migrations..."
docker-compose -f $COMPOSE_FILE exec -T php php artisan migrate --force

# Create storage symlink if needed
echo "Setting up storage..."
docker-compose -f $COMPOSE_FILE exec -T php php artisan storage:link || true

# Clear caches
echo "Clearing application caches..."
docker-compose -f $COMPOSE_FILE exec -T php php artisan cache:clear
docker-compose -f $COMPOSE_FILE exec -T php php artisan config:clear
docker-compose -f $COMPOSE_FILE exec -T php php artisan route:clear
docker-compose -f $COMPOSE_FILE exec -T php php artisan view:clear

# Show status
echo ""
echo "======================================"
echo "Deployment Complete!"
echo "======================================"
echo ""
docker-compose -f $COMPOSE_FILE ps
echo ""

if [ "$COMPOSE_FILE" = "docker-compose.yml" ]; then
    echo "Development server running on http://localhost:8080"
else
    echo "Production server running. Check your domain configuration."
fi

echo ""
echo "View logs with: docker-compose -f $COMPOSE_FILE logs -f"
