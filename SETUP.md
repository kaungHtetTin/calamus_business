# Calamus Education Partner Portal - Setup Guide

## Prerequisites

1. **XAMPP** (PHP 7.4+ and MySQL) installed
2. **Composer** installed
3. **Modern web browser**

## Initial Setup

### Step 1: Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services
3. Verify they are running (green status)

### Step 2: Database Setup

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create a new database named `calamus_db`
3. Import the database schema (if you have a SQL file) or the tables will be created automatically when you first use the application

### Step 3: Install Dependencies

Open terminal/command prompt in the project directory:

```bash
cd C:\xampp\htdocs\business
composer install
```

This installs PHPMailer for email functionality.

### Step 4: Configure Database Connection

The database configuration is in `classes/Database.php`:

**Current settings (localhost):**

- Host: `localhost`
- Username: `root`
- Password: `` (empty)
- Database: `calamus_db`

**To use production database**, uncomment the production settings in `classes/Database.php` (lines 16-19).

### Step 5: Configure Email Settings

Edit `email_config.php`:

```php
define('EMAIL_FROM_ADDRESS', 'your-email@example.com');
define('EMAIL_FROM_NAME', 'Your Company Name');
define('SMTP_HOST', 'smtp.yourprovider.com');
define('SMTP_PORT', 465);
define('SMTP_USERNAME', 'your-smtp-username');
define('SMTP_PASSWORD', 'your-smtp-password');
```

### Step 6: Access the Application

**Partner Portal:**

- URL: http://localhost/business/
- Login page: http://localhost/business/partner_login.php
- Registration: http://localhost/business/partner_register.php

**Admin Dashboard:**

- Login URL: http://localhost/business/admin/login.php
- Credentials:
  - Username: calamuseducation@gmail.com
  - Password: @$calamus5241$@

## Project Structure

```
business/
├── admin/              # Admin dashboard pages
│   ├── partners.php    # Partner list with account status check
│   ├── Account_verify.php  # Verify/reject partners
│   └── view_partner.php    # View partner details with suspend option
├── api/                # API endpoints
├── classes/            # PHP classes (Database, Auth, etc.)
├── css/                # Stylesheets
├── js/                 # JavaScript files
├── layout/             # Header, footer, sidebar templates
├── email_templates/    # Email HTML templates
└── uploads/           # User uploaded files (ID cards, profiles)

```

## Features Implemented

### Admin Features:

1. **Partner Management**: View all partners with filtering
2. **Account Status Check**: Filter partners who have completed all requirements:
   - Email verified
   - Payment method added
   - Personal information complete
3. **Account Verification Page**: Review partner details and verify/reject accounts
4. **Account Suspension**: Suspend accounts with custom email notification
5. **Personal Information Display**: Shows address, NID, and ID card images

### Email System:

- Uses PHPMailer with SMTP
- Templates in `email_templates/` folder
- General notification template: `general_action.html`

## Troubleshooting

### Issues Starting:

1. **Port already in use**: Stop other web servers using port 80
2. **Database connection failed**: Check MySQL is running in XAMPP
3. **Composer not found**: Install Composer from https://getcomposer.org

### Common Errors:

**Error: "Database connection failed"**

- Ensure MySQL is running in XAMPP
- Check database credentials in `classes/Database.php`

**Error: "Email sending failed"**

- Verify SMTP settings in `email_config.php`
- Check email provider credentials

## Security Notes

1. Change default admin credentials in production
2. Update database password in production
3. Keep email credentials secure
4. Review file upload permissions in `uploads/` directory

## Next Steps

1. Create database tables (if not auto-created)
2. Test email functionality with email_tester.php
3. Create first admin user
4. Test partner registration and verification flow

## Support

For issues or questions, refer to:

- `HELP_PAGE_SUMMARY.md`
- `README.md` files in admin directory
