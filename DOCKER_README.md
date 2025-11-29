# Docker Setup for Rate Fundraisers

This guide will help you run the Symfony 6.4 application locally using Docker and MySQL.

## Prerequisites

- Docker Desktop (or Docker Engine + Docker Compose)
- Git

## Quick Start

1. **Clone the repository** (if you haven't already):
   ```bash
   git clone https://github.com/ian0mackenzie/rate-fundraisers.git
   cd rate-fundraisers
   git checkout copilot/update-symfony-version
   ```

2. **Start the Docker environment**:
   ```bash
   docker compose up -d
   ```

   This will:
   - Start a MySQL 8.0 database container
   - Start a PHP 8.3 container running the Symfony application
   - Initialize the database with the schema from `db.sql`
   - Start a Mailpit container for email testing

3. **Access the application**:
   - **Application**: http://localhost:8000
   - **Mailpit** (email testing): http://localhost:8025

## Available Services

- **php**: PHP 8.3 with Symfony 6.4 application
  - Port: 8000
  - Working directory: `/var/www/html`
  
- **database**: MySQL 8.0
  - Port: 3306
  - Database: `symfony`
  - Username: `symfony`
  - Password: `symfony`
  - Root password: `root`

- **mailer**: Mailpit (email testing)
  - SMTP: localhost:1025
  - Web UI: http://localhost:8025

## Useful Commands

### View logs
```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f php
docker compose logs -f database
```

### Execute Symfony console commands
```bash
docker compose exec php php bin/console cache:clear
docker compose exec php php bin/console doctrine:schema:validate
docker compose exec php php bin/console debug:router
```

### Access the PHP container
```bash
docker compose exec php bash
```

### Access the MySQL database
```bash
docker compose exec database mysql -usymfony -psymfony symfony
```

### Stop the environment
```bash
docker compose down
```

### Stop and remove all data
```bash
docker compose down -v
```

### Rebuild containers (after Dockerfile changes)
```bash
docker compose up -d --build
```

## Troubleshooting

### Database connection issues
If you see database connection errors:
1. Wait a few seconds for MySQL to fully start
2. Check database logs: `docker compose logs database`
3. Verify database is running: `docker compose ps`

### Permission issues
If you encounter permission issues with var/ directories:
```bash
docker compose exec php chmod -R 777 var/
```

### Clear cache
```bash
docker compose exec php php bin/console cache:clear
```

## Database Management

The database is automatically initialized with the schema from `db.sql` on first startup.

### Reset database
```bash
docker compose down -v
docker compose up -d
```

### Backup database
```bash
docker compose exec database mysqldump -usymfony -psymfony symfony > backup.sql
```

### Import SQL file
```bash
docker compose exec -T database mysql -usymfony -psymfony symfony < your-file.sql
```

## Development Workflow

1. Make code changes in your local editor
2. Changes are automatically reflected in the container (via volume mount)
3. Clear cache if needed: `docker compose exec php php bin/console cache:clear`
4. View logs: `docker compose logs -f php`

## Environment Variables

You can customize the environment by creating a `.env.local` file:

```env
# Database configuration
MYSQL_VERSION=8.0
MYSQL_DATABASE=symfony
MYSQL_USER=symfony
MYSQL_PASSWORD=symfony
MYSQL_ROOT_PASSWORD=root

# Symfony configuration
APP_ENV=dev
APP_SECRET=your-secret-key
```

## Stopping Development

```bash
# Stop containers (keeps data)
docker compose stop

# Stop and remove containers (keeps data)
docker compose down

# Stop, remove containers and volumes (removes all data)
docker compose down -v
```
