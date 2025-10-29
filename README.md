boosterthon_project / rating-site-project
==========================================

A Symfony project created on February 17, 2017, 7:16 pm.

## Getting Started with Docker

This project includes Docker configuration for easy local development and testing. You can run the entire application stack (PHP/Apache + MySQL) with just a few commands.

### Prerequisites

- Docker Engine 20.10 or higher
- Docker Compose 1.29 or higher (or Docker Compose V2)

### Quick Start

1. **Start the application:**

   ```bash
   docker compose up -d
   ```

   This command will:
   - Build the PHP/Apache Docker image
   - Pull the MySQL 5.7 image
   - Start both containers
   - Initialize the database with the schema from `db.sql`
   - Install all PHP dependencies via Composer
   - Configure the Symfony application automatically

2. **Access the application:**

   Open your browser and navigate to:
   - **Production mode**: [http://localhost:8080/app.php](http://localhost:8080/app.php)
   - **Development mode**: [http://localhost:8080/app_dev.php](http://localhost:8080/app_dev.php)

3. **Stop the application:**

   ```bash
   docker compose down
   ```

### Detailed Documentation

For comprehensive Docker usage instructions, troubleshooting, and advanced configuration options, see [DOCKER.md](DOCKER.md).

## Traditional Setup (without Docker)

If you prefer not to use Docker, you can set up the application traditionally:

1. Install PHP 7.4+ with required extensions (pdo, pdo_mysql, intl, zip, opcache)
2. Install MySQL 5.7+
3. Install Composer
4. Run `composer install`
5. Configure `app/config/parameters.yml` with your database credentials
6. Import `db.sql` into your MySQL database
7. Configure your web server to serve the `web/` directory

## Support

For Docker-specific issues, refer to the [DOCKER.md](DOCKER.md) troubleshooting section.
