#!/bin/bash
# docker/start.sh - Startup script for the container

set -e

echo "🚀 Starting Rate Fundraisers application..."

# Extract database connection details from DATABASE_URL if provided
if [ -n "$DATABASE_URL" ]; then
    echo "📋 Parsing database connection..."
    
    # Parse DATABASE_URL (format: mysql://user:password@host:port/database)
    DB_USER=$(echo $DATABASE_URL | sed -n 's/.*:\/\/\([^:]*\):.*@.*/\1/p')
    DB_PASSWORD=$(echo $DATABASE_URL | sed -n 's/.*:\/\/[^:]*:\([^@]*\)@.*/\1/p')
    DB_HOST=$(echo $DATABASE_URL | sed -n 's/.*@\([^:]*\):.*/\1/p')
    DB_PORT=$(echo $DATABASE_URL | sed -n 's/.*@[^:]*:\([^\/]*\)\/.*/\1/p')
    DB_NAME=$(echo $DATABASE_URL | sed -n 's/.*\/\([^?]*\).*/\1/p')
    
    echo "🔗 Connecting to database at $DB_HOST:$DB_PORT"
    
    # Wait for database to be ready
    echo "⏳ Waiting for database connection..."
    RETRIES=30
    until mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" --silent; do
        RETRIES=$((RETRIES - 1))
        if [ $RETRIES -eq 0 ]; then
            echo "❌ Could not connect to database after 30 attempts"
            exit 1
        fi
        echo "⏳ Database not ready, retrying in 2 seconds... ($RETRIES attempts left)"
        sleep 2
    done
    
    echo "✅ Database connection successful!"
    
    # Check if database exists and has tables
    TABLE_COUNT=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" -D "$DB_NAME" -e "SHOW TABLES;" --batch --skip-column-names 2>/dev/null | wc -l || echo "0")
    
    if [ "$TABLE_COUNT" -eq "0" ]; then
        echo "🗃️  Database is empty, importing schema..."
        if [ -f "/var/www/html/db.sql" ]; then
            mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASSWORD" -D "$DB_NAME" < /var/www/html/db.sql
            echo "✅ Database schema imported successfully!"
        else
            echo "⚠️  No db.sql file found, skipping schema import"
        fi
    else
        echo "✅ Database already contains $TABLE_COUNT tables"
    fi
else
    echo "⚠️  No DATABASE_URL provided, skipping database setup"
fi

# Set up Symfony environment
echo "🔧 Setting up Symfony environment..."
export APP_ENV=${APP_ENV:-prod}
export APP_DEBUG=${APP_DEBUG:-false}

# Clear and warm up Symfony cache
if [ -d "/var/www/html/var/cache" ]; then
    echo "🧹 Clearing Symfony cache..."
    rm -rf /var/www/html/var/cache/*
fi

# Ensure proper permissions
echo "🔐 Setting up file permissions..."
chown -R www-data:www-data /var/www/html/var
chmod -R 777 /var/www/html/var

# Display startup info
echo "🌟 Application starting with:"
echo "   - Environment: $APP_ENV"
echo "   - Debug mode: $APP_DEBUG"
echo "   - Document root: /var/www/html/public"
echo "   - Health check: /health"

# Start Apache in foreground
echo "🚀 Starting Apache web server..."
exec apache2-foreground