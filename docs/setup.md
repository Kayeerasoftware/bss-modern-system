# BSS System Setup Guide

## Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & NPM (for frontend assets)
- Docker (optional, for containerized setup)

## Installation Steps

### 1. Clone the Repository
```bash
git clone <repository-url>
cd bss-system
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bss_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run Migrations
```bash
# Run database migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 6. Build Frontend Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server
```bash
php artisan serve
```

Visit: http://localhost:8000

## Docker Setup (Alternative)

### Using Docker Compose
```bash
cd docker
docker-compose up -d
```

This will start:
- PHP-FPM container
- MySQL container
- Nginx container

Access the application at: http://localhost:8000

## Default Credentials

### Admin Account
- Email: admin@bss.com
- Password: password

### Test Accounts
- Cashier: cashier@bss.com / password
- CEO: ceo@bss.com / password
- TD: td@bss.com / password
- Shareholder: shareholder@bss.com / password
- Client: client@bss.com / password

## Configuration

### SMS Integration (AfricasTalking)
```env
SMS_PROVIDER=africastalking
AFRICASTALKING_USERNAME=your_username
AFRICASTALKING_API_KEY=your_api_key
AFRICASTALKING_FROM=your_sender_id
```

### Payment Gateway (Flutterwave)
```env
PAYMENT_PROVIDER=flutterwave
FLUTTERWAVE_PUBLIC_KEY=your_public_key
FLUTTERWAVE_SECRET_KEY=your_secret_key
FLUTTERWAVE_ENCRYPTION_KEY=your_encryption_key
```

### Email Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

## Queue Workers

For background jobs (notifications, reports):
```bash
php artisan queue:work
```

## Scheduled Tasks

Add to crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## Troubleshooting

### Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Regenerate Autoload
```bash
composer dump-autoload
```

## Production Deployment

### Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
```

### Configure Web Server
See `docs/deployment.md` for Nginx/Apache configuration.

## Support

For issues and questions:
- Documentation: `/docs`
- API Docs: `/docs/api/v1/endpoints.md`
- Database Schema: `/docs/database/schema.md`
