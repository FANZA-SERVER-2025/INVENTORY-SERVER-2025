# 🚀 Quick Start Guide - Inventory Management System

## Prerequisites

- PHP 8.2+
- Composer
- MySQL 8.0+ / MariaDB 10.3+
- Node.js & NPM (optional, untuk compile assets)

## Installation Steps

### 1. Clone / Navigate to Project
```bash
cd /Users/hallo/project/inventory
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies (optional)
npm install
```

### 3. Setup Environment
```bash
# Copy .env file (already exists)
# .env sudah dikonfigurasi dengan MySQL

# Check .env configuration
cat .env
```

### 4. Setup Database

#### Option A: Using MySQL (Production)
```bash
# Create database
mysql -u root -p
```

```sql
CREATE DATABASE inventory_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### Option B: Using SQLite (Development)
Update `.env`:
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=inventory_app
# DB_USERNAME=root
# DB_PASSWORD=
```

### 5. Run Migrations & Seeders
```bash
# Fresh migration with seeders
php artisan migrate:fresh --seed

# Or step by step:
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=SampleDataSeeder  # Optional: sample data
```

### 6. Create Storage Link
```bash
php artisan storage:link
```

### 7. Generate Application Key (if needed)
```bash
php artisan key:generate
```

### 8. Run Development Server
```bash
# Start Laravel server
php artisan serve

# Access application at: http://localhost:8000
```

### 9. Compile Assets (Optional)
```bash
# Development
npm run dev

# Production
npm run build
```

## 🔐 Default Login Credentials

### Admin Account
- **Email:** admin@inventory.com
- **Password:** password
- **Permissions:** Full access to all features

### Operator Account
- **Email:** operator@inventory.com
- **Password:** password
- **Permissions:** Manage inventory, transactions, approve requests

### Customer Account
- **Email:** customer@inventory.com
- **Password:** password
- **Permissions:** View items, create requests, use cart

## 📂 Application URLs

- **Login:** http://localhost:8000/login
- **Register:** http://localhost:8000/register
- **Dashboard:** http://localhost:8000/dashboard
- **Categories:** http://localhost:8000/categories
- **Items:** http://localhost:8000/items
- **Transactions:** http://localhost:8000/transactions

## ✅ Verify Installation

### 1. Check Database Tables
```bash
php artisan db:show

# Or login to MySQL and check:
mysql -u root -p inventory_app
SHOW TABLES;
```

You should see these tables:
- users
- roles
- permissions
- categories
- suppliers
- items
- vehicles
- item_requests
- carts
- transactions
- transaction_details

### 2. Check Seeded Data
```bash
# Check users
php artisan tinker
>>> User::count();  // Should be 3
>>> Role::all();    // Should show admin, operator, customer

# Check sample data (if seeded)
>>> Category::count();
>>> Supplier::count();
>>> Item::count();
```

### 3. Test Login
1. Go to http://localhost:8000/login
2. Login with admin@inventory.com / password
3. Should redirect to dashboard
4. Verify statistics cards display correct numbers

## 🛠️ Troubleshooting

### Migration Error: "Base table or view already exists"
```bash
php artisan migrate:fresh --seed
```

### Permission Denied Error
```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

### MySQL Connection Error
- Check MySQL service is running
- Verify .env credentials
- Test connection: `mysql -u root -p`

### Session Store Error
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### CSRF Token Mismatch
```bash
php artisan config:clear
php artisan cache:clear
# Clear browser cache and cookies
```

## 📦 Optional: Install Sample Data

If you want more test data:

```bash
php artisan db:seed --class=SampleDataSeeder
```

This will create:
- 5 Categories
- 4 Suppliers
- 3 Vehicles
- 10 Items (with low stock examples)

## 🔄 Reset Database (Development Only)

To start fresh:

```bash
# This will drop all tables and recreate them
php artisan migrate:fresh --seed
```

## 🧪 Testing the Application

### Test Admin Features
1. Login as admin
2. Check dashboard statistics
3. Create a new category
4. Create a new supplier
5. Create a new item
6. Create a transaction (in/out)
7. Export data to Excel
8. Manage users and roles

### Test Operator Features
1. Login as operator
2. View and manage items
3. Create transactions
4. Approve/decline item requests
5. Export data

### Test Customer Features
1. Login as customer
2. View items catalog
3. Add items to cart
4. Create item request
5. View transactions

## 📱 Mobile Testing

The application is responsive. Test on:
- Desktop: Chrome, Firefox, Safari
- Tablet: iPad, Android Tablet
- Mobile: iPhone, Android Phone

## 🔍 Debug Mode

In development, debug mode is ON (.env):
```env
APP_DEBUG=true
```

For production, set to:
```env
APP_DEBUG=false
```

## 📝 Next Steps

After installation:

1. **Customize Settings**
   - Update company information in `.env`
   - Configure email settings for notifications
   - Setup backup system

2. **Complete Remaining Features**
   - Follow IMPLEMENTATION_GUIDE.md
   - Complete all CRUD operations
   - Add security features
   - Implement invoice generation

3. **Add Your Data**
   - Create your actual categories
   - Add your suppliers
   - Input your inventory items
   - Configure users and permissions

## 🆘 Need Help?

- Check IMPLEMENTATION_GUIDE.md for detailed implementation
- Check README_PROJECT.md for project overview
- Review Laravel documentation: https://laravel.com/docs

## ⚡ Performance Tips

```bash
# Cache configuration for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# To clear cache
php artisan optimize:clear
```

---

**Ready to go! 🎉**

Login with admin credentials and start using your Inventory Management System!
