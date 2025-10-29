# Docker Setup for Rate Fundraisers

This project includes Docker configuration for easy local development and testing.

## Prerequisites

- Docker Engine 20.10 or higher
- Docker Compose 1.29 or higher

## Quick Start

1. **Build and start the containers:**

   ```bash
   docker-compose up -d
   ```

   This will:
   - Build the PHP/Apache container
   - Start the MySQL database
   - Initialize the database with the schema from `db.sql`
   - Configure the Symfony application

2. **Access the application:**

   Open your browser and navigate to:
   - **Production mode**: http://localhost:8080/app.php
   - **Development mode**: http://localhost:8080/app_dev.php

3. **Stop the containers:**

   ```bash
   docker-compose down
   ```

   To also remove the database volume:
   ```bash
   docker-compose down -v
   ```

## Configuration

### Environment Variables

The Docker setup uses the following default configuration:

- **Database Host**: `db`
- **Database Port**: `3306`
- **Database Name**: `rate_fundraisers`
- **Database User**: `symfony`
- **Database Password**: `symfony`

You can customize these by editing the `docker-compose.yml` file or by creating a `.env` file.

### Ports

- **Web Application**: http://localhost:8080
- **MySQL Database**: localhost:3306

If these ports are already in use on your system, you can change them in `docker-compose.yml`:

```yaml
services:
  web:
    ports:
      - "8080:80"  # Change 8080 to your preferred port
  db:
    ports:
      - "3306:3306"  # Change 3306 to your preferred port
```

## Development Workflow

### Viewing Logs

To view application logs in real-time:

```bash
docker-compose logs -f web
```

To view database logs:

```bash
docker-compose logs -f db
```

### Running Symfony Commands

Execute Symfony console commands inside the web container:

```bash
docker-compose exec web php bin/console [command]
```

Examples:
```bash
# Clear cache
docker-compose exec web php bin/console cache:clear

# List routes
docker-compose exec web php bin/console debug:router

# Create database schema (if needed)
docker-compose exec web php bin/console doctrine:schema:update --force
```

### Accessing the Database

Connect to the MySQL database from your host machine:

```bash
mysql -h 127.0.0.1 -P 3306 -u symfony -p
# Password: symfony
```

Or use the MySQL client inside the container:

```bash
docker-compose exec db mysql -u symfony -p rate_fundraisers
# Password: symfony
```

### Installing Dependencies

If you need to install new Composer packages:

```bash
docker-compose exec web composer require vendor/package-name
```

### Rebuilding the Containers

If you make changes to the Dockerfile or need to rebuild:

```bash
docker-compose up -d --build
```

## Troubleshooting

### Permission Issues

If you encounter permission issues with cache or log files:

```bash
docker-compose exec web chown -R www-data:www-data var/
docker-compose exec web chmod -R 775 var/
```

### Database Connection Issues

If the application can't connect to the database:

1. Check if the database container is running:
   ```bash
   docker-compose ps
   ```

2. Verify database health:
   ```bash
   docker-compose exec db mysqladmin ping -h localhost -u root -proot
   ```

3. Restart the containers:
   ```bash
   docker-compose restart
   ```

### Clearing Everything

To start fresh (this will delete all data):

```bash
docker-compose down -v
docker-compose up -d --build
```

## Production Considerations

This Docker setup is optimized for **development and testing**. For production deployment:

1. Use a production-grade web server configuration
2. Set proper environment variables for sensitive data
3. Use Docker secrets or environment-specific configuration
4. Configure proper SSL/TLS certificates
5. Set up proper logging and monitoring
6. Use multi-stage builds to reduce image size
7. Consider using container orchestration (Kubernetes, Docker Swarm)

## Additional Information

- The application runs on PHP 7.4 with Apache
- MySQL 5.7 is used for the database
- All Symfony dependencies are automatically installed via Composer
- The database is automatically initialized with the schema from `db.sql`
