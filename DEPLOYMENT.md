# Deployment Guide — TokoOnline

## Requirements
- PHP 8.3+
- MySQL 8.0 / MariaDB 10.4+
- Composer 2.x
- Node.js 18+ (for Vite build)
- Redis (optional, for cache/queue)
- Supervisor (for queue workers)
- Nginx (recommended web server)

## Installation Steps

### 1. Clone & Install
```bash
git clone <repo-url> tokoonline
cd tokoonline
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

### 2. Configure .env
Update database credentials and app URL:
```
APP_URL=https://your-domain.com
DB_DATABASE=tokoonline
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### 3. Database
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 4. Build Frontend
```bash
npm install
npm run build
```

### 5. Set Permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .
```

### 6. Nginx Configuration
See `deploy/nginx.conf`

### 7. Supervisor Configuration
See `deploy/supervisor.conf`

### 8. Scheduler
```bash
crontab -e
# Add:
* * * * * cd /path/to/tokoonline && php artisan schedule:run >> /dev/null 2>&1
```

### 9. Verify
- Visit `https://your-domain.com` — Storefront
- Visit `https://your-domain.com/admin` — Admin Panel
- Login email: `admin@tokoonline.test` / password: `password`

## Admin Demo Accounts
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@tokoonline.test | password |
| Customer | customer@tokoonline.test | password |

## Post-Deployment
1. Submit sitemap to Google Search Console: `/sitemap.xml`
2. Set up email provider in .env
3. Configure payment gateways in admin panel
4. Update robots.txt if needed
