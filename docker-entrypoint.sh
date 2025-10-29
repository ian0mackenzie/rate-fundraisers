#!/bin/bash
set -e

# Wait for database to be ready
echo "Waiting for database to be ready..."
until php -r "new PDO('mysql:host=${DATABASE_HOST};port=${DATABASE_PORT};dbname=${DATABASE_NAME}', '${DATABASE_USER}', '${DATABASE_PASSWORD}');" 2>/dev/null; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

echo "Database is ready!"

# Create parameters.yml from environment variables if it doesn't exist
if [ ! -f app/config/parameters.yml ]; then
    echo "Creating parameters.yml from environment variables..."
    cat > app/config/parameters.yml <<EOF
parameters:
    database_host: ${DATABASE_HOST:-db}
    database_port: ${DATABASE_PORT:-3306}
    database_name: ${DATABASE_NAME:-rate_fundraisers}
    database_user: ${DATABASE_USER:-symfony}
    database_password: ${DATABASE_PASSWORD:-symfony}
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: null
    mailer_password: null
    secret: ThisTokenIsNotSoSecretChangeIt
EOF
fi

# Clear cache
echo "Clearing cache..."
php bin/console cache:clear --no-warmup --env=prod || true
php bin/console cache:warmup --env=prod || true

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data var/ web/ || true
chmod -R 775 var/ || true

echo "Starting Apache..."
exec apache2-foreground
