# MedFinder Ethiopia - Setup Guide

## Prerequisites
- **XAMPP** (PHP 8.0+ recommended)
- **MySQL / MariaDB**

## Installation Steps

### 1. Database Setup
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a new database named `medfinder_ethiopia`.
3. Import the SQL file from `sql/database.sql` (if available) or use the schema provided in the `guideline.md`.

### 2. Configuration
The database connection is managed in `includes/db.php`. Ensure your credentials in `includes/config.php` are correct:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'medfinder_ethiopia');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 3. Usage & Testing
1. Copy the project folder to `C:\xampp\htdocs\MedFinder`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/MedFinder` in your browser.

#### **Test Credentials:**
- **Admin Panel:** `http://localhost/MedFinder/admin/login.php`
  - Email: `admin@medfinder.et`
  - Password: `admin123`
- **Pharmacy Dashboard:** `http://localhost/MedFinder/pharmacy/login.php`
  - *Must register a pharmacy first or use existing test data.*

## Features
- **Live Search**: Integrated on the homepage.
- **Admin Management**: Approve pharmacies and manage medicine catalogs.
- **Inventory Sync**: Pharmacies can update stock levels in real-time.
