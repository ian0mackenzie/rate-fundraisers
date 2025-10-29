#!/bin/bash
set -e

echo "Installing Composer dependencies..."
if [ ! -d "vendor" ]; then
    # Install without scripts to avoid compatibility issues
    composer install --no-interaction --optimize-autoloader --no-dev --no-scripts
    
    # Create parameters.yml from distribution file
    if [ ! -f app/config/parameters.yml ]; then
        cp app/config/parameters.yml.dist app/config/parameters.yml
    fi
fi

# Wait for database to be ready
echo "Waiting for database to be ready..."
until php -r "new PDO('mysql:host=${DATABASE_HOST};port=${DATABASE_PORT};dbname=${DATABASE_NAME}', '${DATABASE_USER}', '${DATABASE_PASSWORD}');" 2>/dev/null; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

echo "Database is ready!"

# Update parameters.yml with environment variables
echo "Updating parameters.yml with environment variables..."
cat > app/config/parameters.yml <<EOF
parameters:
    database_host: ${DATABASE_HOST:-db}
    database_port: ${DATABASE_PORT:-3306}
    database_name: ${DATABASE_NAME:-rate_fundraisers}
    database_user: ${DATABASE_USER:-symfony}
    database_password: ${DATABASE_PASSWORD:-symfony}
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: noreply@example.com
    mailer_password: null
    secret: ThisTokenIsNotSoSecretChangeIt
EOF

# Create bootstrap file if it doesn't exist
if [ ! -f var/bootstrap.php.cache ]; then
    echo "Creating bootstrap file..."
    touch var/bootstrap.php.cache
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
