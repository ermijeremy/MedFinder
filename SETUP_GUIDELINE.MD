# MedFinder Ethiopia - Setup Guide

## Prerequisites

- **PHP 7.4+** with mysqli extension
- **MySQL 5.7+** or MariaDB 10.3+
- **Apache** web server with mod_rewrite
- **Git** for version control

## Installation Steps

### 1. Clone Repository
```bash
git clone <repository-url>
cd med-ethio
```

### 2. Database Setup
```sql
-- Create database
CREATE DATABASE medfinder_ethiopia;

-- Import schema (create tables manually or use migration script)
-- See database schema in guideline.md
```

### 3. Configuration
- Copy `includes/config.php.example` to `includes/config.php`
- Update database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'medfinder_ethiopia');
```

### 4. File Permissions
```bash
chmod 755 includes/
chmod 644 includes/*.php
chmod 755 css/ js/
```

### 5. Web Server Configuration
- Point document root to project directory
- Enable .htaccess if using Apache
- Ensure PHP can connect to MySQL

### 6. Seed Data
- Insert initial neighborhoods and sample medicines
- Create admin user (hardcoded initially)

## Running Locally

### Using XAMPP
1. Install XAMPP
2. Copy project to `C:\xampp\htdocs\medFinder`
3. Start Apache and MySQL
4. Access: `http://localhost/medFinder`

### Using Built-in PHP Server
```bash
php -S localhost:8000
```
Access: `http://localhost:8000`

## Testing

- Visit home page
- Test search functionality
- Try admin/pharmacy login (use test credentials)

## Troubleshooting

- Check PHP error logs
- Verify database connection
- Ensure file permissions are correct
- Test with phpMyAdmin for DB issues