# 🎯 Developer Cheatsheet - Inventory System

## 📱 Access Points

```
Application URL: http://localhost:8000
Login Page:      http://localhost:8000/login
Dashboard:       http://localhost:8000/dashboard

Demo Accounts:
- admin@inventory.com / password
- operator@inventory.com / password
- customer@inventory.com / password
```

## 🔧 Common Artisan Commands

### Development
```bash
# Start server
php artisan serve

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Clear all
php artisan optimize:clear

# Generate app key
php artisan key:generate

# Create storage link
php artisan storage:link
```

### Database
```bash
# Fresh migration with seeds
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=SampleDataSeeder

# Rollback
php artisan migrate:rollback

# Check database
php artisan db:show
```

### Generate Files
```bash
# Controller
php artisan make:controller NameController
php artisan make:controller NameController --resource

# Model
php artisan make:model Name
php artisan make:model Name -m  # with migration

# Migration
php artisan make:migration create_table_name

# Seeder
php artisan make:seeder NameSeeder

# Middleware
php artisan make:middleware NameMiddleware

# Request
php artisan make:request NameRequest
```

### Testing
```bash
# Tinker (test code)
php artisan tinker

# Examples in tinker:
>>> User::count()
>>> Category::all()
>>> Item::lowStock()->get()
>>> auth()->loginUsingId(1)
```

## 📝 Copy-Paste Patterns

### 1. Create New CRUD Module (Example: Products)

#### Step 1: Controller
```php
// Copy CategoryController.php to ProductController.php
// Find & Replace: categories → products, Category → Product
```

#### Step 2: Export Class
```php
// app/Exports/ProductsExport.php
namespace App\Exports;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsExport implements FromCollection
{
    public function collection()
    {
        return Product::all();
    }
}
```

#### Step 3: Routes (web.php)
```php
Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
Route::resource('products', ProductController::class);
```

#### Step 4: Views
```bash
# Copy entire categories folder
cp -r resources/views/categories resources/views/products

# In each file, Find & Replace:
categories → products
Category → Product
category → product
```

#### Step 5: Menu (layouts/app.blade.php)
```blade
@can('view products')
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" 
       href="{{ route('products.index') }}">
        <i class="fas fa-box"></i> Products
    </a>
</li>
@endcan
```

### 2. Add Permission
```php
// In RolePermissionSeeder.php
$permissions = [
    'view products',
    'create products',
    'edit products',
    'delete products',
    'export products',
];

// Assign to roles
$admin->givePermissionTo('view products');
```

### 3. DataTables AJAX Setup
```javascript
$('#tableName').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('module.index') }}",
    columns: [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        {
            data: 'id',
            name: 'actions',
            orderable: false,
            searchable: false,
            render: function(data) {
                return '<button>Action</button>';
            }
        }
    ]
});
```

### 4. Form Validation
```php
$request->validate([
    'name' => 'required|string|max:255',
    'code' => 'required|string|unique:table,code',
    'email' => 'nullable|email',
    'phone' => 'nullable|string',
    'is_active' => 'boolean',
]);
```

### 5. Image Upload
```php
// In controller store/update
if ($request->hasFile('image')) {
    $path = $request->file('image')->store('images', 'public');
    $data['image'] = $path;
}

// Delete old image
if ($model->image) {
    Storage::disk('public')->delete($model->image);
}
```

### 6. Export Excel
```php
public function export()
{
    return Excel::download(new ItemsExport, 'items_'.date('Y-m-d').'.xlsx');
}
```

### 7. Generate PDF
```php
use Barryvdh\DomPDF\Facade\Pdf;

public function invoice($id)
{
    $data = Model::find($id);
    $pdf = PDF::loadView('module.invoice', compact('data'));
    return $pdf->download('invoice-'.$id.'.pdf');
}
```

## 🎨 Common Blade Components

### Alert Messages
```blade
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
```

### Form Input
```blade
<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" 
           class="form-control @error('name') is-invalid @enderror" 
           id="name" 
           name="name" 
           value="{{ old('name', $item->name ?? '') }}" 
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

### Select Dropdown
```blade
<select class="form-select select2 @error('field') is-invalid @enderror" name="field">
    <option value="">Select...</option>
    @foreach($items as $item)
        <option value="{{ $item->id }}" 
                {{ old('field', $selected ?? '') == $item->id ? 'selected' : '' }}>
            {{ $item->name }}
        </option>
    @endforeach
</select>
```

### Checkbox/Switch
```blade
<div class="form-check form-switch">
    <input class="form-check-input" 
           type="checkbox" 
           id="is_active" 
           name="is_active" 
           value="1"
           {{ old('is_active', $item->is_active ?? 1) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>
```

### File Upload
```blade
<div class="mb-3">
    <label for="image" class="form-label">Image</label>
    <input type="file" 
           class="form-control @error('image') is-invalid @enderror" 
           id="image" 
           name="image"
           accept="image/*">
    @if(isset($item) && $item->image)
        <img src="{{ asset('storage/'.$item->image) }}" 
             alt="Preview" 
             class="img-thumbnail mt-2" 
             width="200">
    @endif
</div>
```

### Action Buttons
```blade
<a href="{{ route('module.show', $id) }}" class="btn btn-sm btn-info" title="View">
    <i class="fas fa-eye"></i>
</a>
<a href="{{ route('module.edit', $id) }}" class="btn btn-sm btn-warning" title="Edit">
    <i class="fas fa-edit"></i>
</a>
<button onclick="deleteItem({{ $id }})" class="btn btn-sm btn-danger" title="Delete">
    <i class="fas fa-trash"></i>
</button>
```

### Status Badge
```blade
@if($item->is_active)
    <span class="badge bg-success">Active</span>
@else
    <span class="badge bg-danger">Inactive</span>
@endif
```

## 🔒 Permission Checks

### In Blade
```blade
@can('permission name')
    <!-- Authorized content -->
@endcan

@cannot('permission name')
    <!-- Unauthorized content -->
@endcannot

@role('admin')
    <!-- Admin only -->
@endrole
```

### In Controller
```php
// In __construct
$this->middleware('permission:view items');

// In method
$this->authorize('update', $item);

// Check directly
if (auth()->user()->can('delete items')) {
    // ...
}

if (auth()->user()->hasRole('admin')) {
    // ...
}
```

### In Routes
```php
Route::middleware(['permission:view items'])->group(function () {
    // Protected routes
});
```

## 🗄️ Database Queries

### Eloquent Examples
```php
// Get all
$items = Item::all();

// With relationships
$items = Item::with(['category', 'supplier'])->get();

// Where conditions
$items = Item::where('is_active', true)->get();
$items = Item::where('stock', '<', 10)->get();

// Pagination
$items = Item::paginate(10);

// Order
$items = Item::orderBy('name', 'asc')->get();

// Search
$items = Item::where('name', 'like', "%{$search}%")->get();

// Count
$count = Item::count();
$lowStock = Item::lowStock()->count();

// Create
$item = Item::create($data);

// Update
$item->update($data);

// Delete (soft)
$item->delete();

// Force delete
$item->forceDelete();

// Restore
$item->restore();
```

### Query Scopes
```php
// In Model
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

public function scopeLowStock($query)
{
    return $query->whereRaw('stock < minimum_stock');
}

// Usage
$activeItems = Item::active()->get();
$lowStockItems = Item::lowStock()->get();
```

## 📊 Statistics Queries

```php
// Count
$total = Model::count();
$active = Model::where('is_active', true)->count();

// Sum
$totalAmount = Transaction::sum('total_amount');
$totalStock = Item::sum('stock');

// Average
$avgPrice = Item::avg('selling_price');

// Group by
$itemsByCategory = Item::select('category_id', DB::raw('count(*) as total'))
    ->groupBy('category_id')
    ->get();

// This month
$thisMonth = Transaction::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->count();

// Today
$today = Transaction::whereDate('created_at', today())->count();
```

## 🎨 Icon Classes (Font Awesome)

```html
<!-- Common icons -->
<i class="fas fa-home"></i>           <!-- Home -->
<i class="fas fa-tachometer-alt"></i> <!-- Dashboard -->
<i class="fas fa-tags"></i>           <!-- Categories -->
<i class="fas fa-truck"></i>          <!-- Suppliers -->
<i class="fas fa-box"></i>            <!-- Items/Products -->
<i class="fas fa-car"></i>            <!-- Vehicles -->
<i class="fas fa-users"></i>          <!-- Users -->
<i class="fas fa-user-shield"></i>    <!-- Roles -->
<i class="fas fa-exchange-alt"></i>   <!-- Transactions -->
<i class="fas fa-clipboard-list"></i> <!-- Requests -->
<i class="fas fa-shopping-cart"></i>  <!-- Cart -->
<i class="fas fa-user-circle"></i>    <!-- Profile -->

<!-- Actions -->
<i class="fas fa-eye"></i>            <!-- View -->
<i class="fas fa-edit"></i>           <!-- Edit -->
<i class="fas fa-trash"></i>          <!-- Delete -->
<i class="fas fa-plus"></i>           <!-- Add -->
<i class="fas fa-save"></i>           <!-- Save -->
<i class="fas fa-times"></i>          <!-- Cancel -->
<i class="fas fa-check"></i>          <!-- Approve -->
<i class="fas fa-ban"></i>            <!-- Decline -->
<i class="fas fa-download"></i>       <!-- Download -->
<i class="fas fa-file-excel"></i>     <!-- Excel -->
<i class="fas fa-file-pdf"></i>       <!-- PDF -->
<i class="fas fa-print"></i>          <!-- Print -->
<i class="fas fa-search"></i>         <!-- Search -->

<!-- Status -->
<i class="fas fa-check-circle"></i>   <!-- Success -->
<i class="fas fa-exclamation-circle"></i> <!-- Warning -->
<i class="fas fa-info-circle"></i>    <!-- Info -->
<i class="fas fa-times-circle"></i>   <!-- Error -->
```

## 🎨 Color Classes

### Bootstrap 5
```html
<!-- Buttons -->
btn-primary btn-secondary btn-success btn-danger btn-warning btn-info

<!-- Badges -->
bg-primary bg-secondary bg-success bg-danger bg-warning bg-info

<!-- Text -->
text-primary text-secondary text-success text-danger text-warning text-info text-muted

<!-- Backgrounds -->
bg-light bg-dark bg-white

<!-- Alerts -->
alert-success alert-danger alert-warning alert-info
```

## 🐛 Debugging

### Laravel Debugbar (Install)
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Debug Methods
```php
// Dump and die
dd($variable);

// Dump
dump($variable);

// Log
\Log::info('Message', ['data' => $data]);
\Log::error('Error message');

// Query log
\DB::enableQueryLog();
// ... queries ...
dd(\DB::getQueryLog());
```

### Check Permissions
```php
// In tinker
>>> $user = User::find(1);
>>> $user->roles;
>>> $user->permissions;
>>> $user->can('view items');
>>> $user->hasRole('admin');
```

## 📱 Responsive Classes

```html
<!-- Display -->
d-none d-sm-block d-md-block d-lg-block d-xl-block

<!-- Columns -->
col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2

<!-- Margins/Padding -->
m-1 m-sm-2 m-md-3
p-1 p-sm-2 p-md-3

<!-- Text alignment -->
text-center text-sm-start text-md-end

<!-- Hide on mobile -->
d-none d-md-block

<!-- Show only on mobile -->
d-block d-md-none
```

## 🔐 Security Best Practices

```php
// Always validate input
$request->validate([...]);

// Use mass assignment protection
protected $fillable = [...];

// Hash passwords
Hash::make($password);

// Verify hashed password
Hash::check($password, $hashedPassword);

// Sanitize output (Blade does this automatically)
{{ $variable }}  // Escaped
{!! $variable !!}  // Not escaped (use with caution)

// CSRF token (automatic in forms within @csrf)
<form method="POST">
    @csrf
    ...
</form>

// Authorize actions
$this->authorize('update', $item);

// Rate limiting
$this->middleware('throttle:60,1');
```

## 🚀 Performance Tips

```php
// Eager loading (prevent N+1)
$items = Item::with('category', 'supplier')->get();

// Select specific columns
$items = Item::select('id', 'name', 'code')->get();

// Chunk large datasets
Item::chunk(100, function($items) {
    foreach ($items as $item) {
        // Process
    }
});

// Cache
$value = Cache::remember('key', $minutes, function() {
    return DB::table('...')->get();
});

// Queue heavy tasks
dispatch(new ProcessJob($data));
```

## 📦 Useful Packages

```bash
# Already installed:
- laravel/framework (Laravel core)
- spatie/laravel-permission (Roles & Permissions)
- maatwebsite/excel (Excel export/import)
- barryvdh/laravel-dompdf (PDF generation)

# Optional:
composer require laravel/telescope  # Debugging
composer require barryvdh/laravel-debugbar  # Debug bar
composer require intervention/image  # Image processing
```

---

**Happy Coding! 🎉**

Simpan file ini untuk referensi cepat selama development.
