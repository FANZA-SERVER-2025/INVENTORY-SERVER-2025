<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    // Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    // Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    
    // Storage files
    Route::get('/storage/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) {
            abort(404);
        }
        return response()->file($fullPath);
    })->where('path', '.*');
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Categories
    Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::resource('categories', CategoryController::class);
    
    // Suppliers
    Route::get('/suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export');
    Route::resource('suppliers', SupplierController::class);
    
    // Items
    Route::get('/items/export', [ItemController::class, 'export'])->name('items.export');
    Route::resource('items', ItemController::class);
    
    // Vehicles
    Route::get('/vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');
    Route::resource('vehicles', VehicleController::class);
    
    // Users
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class);
    
    // Roles & Permissions
    Route::resource('roles', RoleController::class);
    
    // Transactions
    Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    Route::get('/transactions/{transaction}/invoice', [TransactionController::class, 'invoice'])->name('transactions.invoice');
    Route::patch('/transactions/{transaction}/update-payment', [TransactionController::class, 'updatePayment'])->name('transactions.update-payment');
    Route::resource('transactions', TransactionController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});
