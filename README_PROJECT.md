# Aplikasi Inventory Management System

Aplikasi Inventory Management menggunakan Laravel 12 dan MySQL dengan fitur lengkap untuk mengelola inventory, transaksi, dan user management dengan 3 role berbeda.

## 📋 Requirements

1. ✅ **Login System dengan 3 Role (Admin, Operator, Customer)**
2. ✅ **Kelola Kategori** - CRUD dengan DataTable
3. ✅ **Kelola Supplier** - CRUD dengan DataTable
4. ✅ **Kelola Barang** - CRUD dengan DataTable
5. ✅ **Kelola User** - CRUD dengan DataTable
6. ✅ **Kelola Transaksi** - CRUD dengan DataTable
7. ✅ **Kelola Roles & Permission** - CRUD dengan DataTable
8. ✅ **Dashboard Informatif** - Statistik lengkap
9. ✅ **Permintaan Barang** - Approval System
10. ✅ **Keranjang** - Cart Management
11. ✅ **Mengubah Akun** - Profile Edit
12. ✅ **List Transaksi** - DataTable dengan Export
13. ✅ **Search Data** - Global Search
14. ✅ **Responsive Design** - Desktop, Tablet, Mobile
15. ✅ **Export to Excel** - Semua data
16. ✅ **High Security** - Multiple security layers
17. ✅ **Bon Faktur** - PDF Invoice

## 🗄️ Database Structure

### Tables:
- **users** - User accounts dengan phone, address, avatar, is_active
- **roles** - Role management (Admin, Operator, Customer)
- **permissions** - Permission management
- **categories** - Kategori barang
- **suppliers** - Data supplier
- **vehicles** - Data kendaraan untuk pengiriman
- **items** - Data barang dengan stock management
- **item_requests** - Permintaan barang dengan approval system
- **carts** - Shopping cart
- **transactions** - Transaksi barang masuk/keluar
- **transaction_details** - Detail item per transaksi

## 🔐 Default Users

```
Admin:
Email: admin@inventory.com
Password: password

Operator:
Email: operator@inventory.com
Password: password

Customer:
Email: customer@inventory.com
Password: password
```

## 🚀 Installation

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Create Database
```bash
mysql -u root -p
CREATE DATABASE inventory_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit
```

### 5. Run Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```

### 6. Storage Link
```bash
php artisan storage:link
```

### 7. Run Development Server
```bash
php artisan serve
# Atau
npm run dev
```

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   └── RegisterController.php
│   │   ├── CategoryController.php
│   │   ├── SupplierController.php
│   │   ├── ItemController.php
│   │   ├── VehicleController.php
│   │   ├── UserController.php
│   │   ├── RoleController.php
│   │   ├── TransactionController.php
│   │   ├── ItemRequestController.php
│   │   ├── CartController.php
│   │   ├── DashboardController.php
│   │   └── ProfileController.php
│   ├── Middleware/
│   │   └── CheckRole.php
│   └── Requests/
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Supplier.php
│   ├── Item.php
│   ├── Vehicle.php
│   ├── ItemRequest.php
│   ├── Cart.php
│   ├── Transaction.php
│   └── TransactionDetail.php
└── Exports/
    ├── CategoriesExport.php
    ├── SuppliersExport.php
    ├── ItemsExport.php
    └── TransactionsExport.php

resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php
    │   └── guest.blade.php
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── dashboard.blade.php
    ├── categories/
    ├── suppliers/
    ├── items/
    ├── vehicles/
    ├── users/
    ├── roles/
    ├── transactions/
    ├── item-requests/
    ├── cart/
    └── profile/
```

## 🎯 Features by Role

### Admin (Full Access)
- ✅ View & manage all modules
- ✅ User management
- ✅ Role & permission management
- ✅ Approve/decline item requests
- ✅ Export all data
- ✅ View all statistics

### Operator
- ✅ Manage inventory (categories, suppliers, items)
- ✅ Create & manage transactions
- ✅ Approve/decline item requests
- ✅ View dashboard statistics
- ✅ Export data

### Customer
- ✅ View items catalog
- ✅ Request items
- ✅ View cart
- ✅ View own transactions
- ✅ Edit profile

## 🔒 Security Features

1. **CSRF Protection** - Built-in Laravel protection
2. **XSS Prevention** - Input sanitization
3. **SQL Injection Prevention** - Eloquent ORM
4. **Password Hashing** - Bcrypt
5. **Role-Based Access Control** - Spatie Permission
6. **Rate Limiting** - Login & API throttling
7. **Secure Headers** - Security headers middleware
8. **Input Validation** - Form Request validation
9. **Session Security** - Secure session management
10. **Password Policy** - Minimum 8 characters

## 📊 Dashboard Features

- Card statistik untuk:
  - Jumlah Kategori
  - Jumlah Supplier
  - Jumlah Barang
  - Jumlah Kendaraan
  - Jumlah Customer
  - Jumlah Permintaan Barang
  - Jumlah Barang Keluar
  - Jumlah Barang Keluar Bulan Ini
- Barang paling populer
- Notifikasi permintaan barang pending
- Alert barang dengan stok < 10

## 📤 Export Features

Semua modul memiliki fitur export ke Excel:
- Categories
- Suppliers  
- Items
- Vehicles
- Users
- Transactions
- Item Requests

## 🧾 Invoice/Bon Faktur

- Generate PDF invoice untuk setiap transaksi
- Include detail items, quantities, prices
- Company branding
- Transaction number & date
- Customer information

## 📱 Responsive Design

- ✅ Desktop (1920x1080+)
- ✅ Laptop (1366x768+)
- ✅ Tablet (768x1024)
- ✅ Mobile (375x667+)

Menggunakan Bootstrap 5 dengan responsive grid system.

## 🔍 Search Feature

- Global search across all modules
- Search by name, code, email, etc.
- Real-time search results
- Filter by date range
- Filter by status

## 🛠️ Technologies Used

- **Backend**: Laravel 12
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5, jQuery
- **DataTables**: jQuery DataTables
- **Icons**: Font Awesome
- **Charts**: Chart.js
- **Export**: Maatwebsite/Excel
- **PDF**: DomPDF
- **Permissions**: Spatie Laravel Permission

## 📝 API Routes

```php
// Authentication
POST   /login
POST   /register
POST   /logout

// Dashboard
GET    /dashboard

// Categories
GET    /categories
POST   /categories
GET    /categories/{id}
PUT    /categories/{id}
DELETE /categories/{id}
GET    /categories/export

// Suppliers (similar structure)
// Items (similar structure)
// Vehicles (similar structure)
// Users (similar structure)
// Roles (similar structure)
// Transactions (similar structure)
// Item Requests (similar structure)

// Cart
GET    /cart
POST   /cart/add
DELETE /cart/{id}
POST   /cart/checkout

// Profile
GET    /profile
PUT    /profile
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter CategoryTest
```

## 📈 Performance Optimization

- Database indexing
- Query optimization with eager loading
- Caching for static data
- Asset minification
- Image optimization
- Lazy loading for images

## 🐛 Common Issues & Solutions

### MySQL Connection Error
```bash
# Check MySQL service
sudo service mysql status
# Restart if needed
sudo service mysql restart
```

### Permission Denied
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Migration Errors
```bash
# Fresh migration
php artisan migrate:fresh --seed
```

## 📞 Support

Untuk bantuan atau pertanyaan, hubungi tim development.

## 📄 License

This project is proprietary software.

---

**Version**: 1.0.0  
**Last Updated**: November 2025  
**Developed by**: Your Team Name
