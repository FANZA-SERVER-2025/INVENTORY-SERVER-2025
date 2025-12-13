<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Electronics', 'code' => 'CAT001', 'description' => 'Electronic items and gadgets', 'is_active' => true],
            ['name' => 'Office Supplies', 'code' => 'CAT002', 'description' => 'Office equipment and supplies', 'is_active' => true],
            ['name' => 'Furniture', 'code' => 'CAT003', 'description' => 'Office and home furniture', 'is_active' => true],
            ['name' => 'Stationery', 'code' => 'CAT004', 'description' => 'Writing and paper supplies', 'is_active' => true],
            ['name' => 'Cleaning Supplies', 'code' => 'CAT005', 'description' => 'Cleaning and maintenance items', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }

        // Suppliers
        $suppliers = [
            ['name' => 'PT. Electronic Indonesia', 'code' => 'SUP001', 'email' => 'electronic@supplier.com', 'phone' => '021-12345678', 'address' => 'Jakarta', 'contact_person' => 'John Doe', 'is_active' => true],
            ['name' => 'CV. Office Mart', 'code' => 'SUP002', 'email' => 'officemart@supplier.com', 'phone' => '021-87654321', 'address' => 'Bandung', 'contact_person' => 'Jane Smith', 'is_active' => true],
            ['name' => 'UD. Furniture Jaya', 'code' => 'SUP003', 'email' => 'furniture@supplier.com', 'phone' => '022-11223344', 'address' => 'Surabaya', 'contact_person' => 'Bob Wilson', 'is_active' => true],
            ['name' => 'Toko Stationery', 'code' => 'SUP004', 'email' => 'stationery@supplier.com', 'phone' => '031-44332211', 'address' => 'Yogyakarta', 'contact_person' => 'Alice Brown', 'is_active' => true],
        ];

        foreach ($suppliers as $supplier) {
            \App\Models\Supplier::create($supplier);
        }

        // Vehicles
        $vehicles = [
            ['name' => 'Truck - Delivery 1', 'plate_number' => 'B 1234 ABC', 'type' => 'Truck', 'brand' => 'Mitsubishi', 'year' => 2020, 'is_active' => true],
            ['name' => 'Van - Delivery 2', 'plate_number' => 'B 5678 DEF', 'type' => 'Van', 'brand' => 'Toyota', 'year' => 2021, 'is_active' => true],
            ['name' => 'Pickup - Delivery 3', 'plate_number' => 'B 9012 GHI', 'type' => 'Pickup', 'brand' => 'Isuzu', 'year' => 2019, 'is_active' => true],
        ];

        foreach ($vehicles as $vehicle) {
            \App\Models\Vehicle::create($vehicle);
        }

        // Items
        $items = [
            // Electronics
            ['category_id' => 1, 'supplier_id' => 1, 'name' => 'Laptop HP ProBook', 'code' => 'ITM001', 'description' => 'Business laptop with Intel i5', 'unit' => 'pcs', 'stock' => 15, 'minimum_stock' => 5, 'purchase_price' => 8000000, 'selling_price' => 10000000, 'is_active' => true],
            ['category_id' => 1, 'supplier_id' => 1, 'name' => 'Monitor LED 24 inch', 'code' => 'ITM002', 'description' => 'Full HD monitor', 'unit' => 'pcs', 'stock' => 8, 'minimum_stock' => 10, 'purchase_price' => 1500000, 'selling_price' => 2000000, 'is_active' => true],
            ['category_id' => 1, 'supplier_id' => 1, 'name' => 'Mouse Wireless', 'code' => 'ITM003', 'description' => 'Wireless optical mouse', 'unit' => 'pcs', 'stock' => 50, 'minimum_stock' => 20, 'purchase_price' => 150000, 'selling_price' => 250000, 'is_active' => true],
            
            // Office Supplies
            ['category_id' => 2, 'supplier_id' => 2, 'name' => 'Printer HP LaserJet', 'code' => 'ITM004', 'description' => 'Monochrome laser printer', 'unit' => 'pcs', 'stock' => 5, 'minimum_stock' => 10, 'purchase_price' => 3000000, 'selling_price' => 4000000, 'is_active' => true],
            ['category_id' => 2, 'supplier_id' => 2, 'name' => 'Paper A4 (Ream)', 'code' => 'ITM005', 'description' => '500 sheets per ream', 'unit' => 'ream', 'stock' => 100, 'minimum_stock' => 50, 'purchase_price' => 40000, 'selling_price' => 60000, 'is_active' => true],
            
            // Furniture
            ['category_id' => 3, 'supplier_id' => 3, 'name' => 'Office Chair Ergonomic', 'code' => 'ITM006', 'description' => 'Adjustable office chair', 'unit' => 'pcs', 'stock' => 20, 'minimum_stock' => 10, 'purchase_price' => 1000000, 'selling_price' => 1500000, 'is_active' => true],
            ['category_id' => 3, 'supplier_id' => 3, 'name' => 'Office Desk 120cm', 'code' => 'ITM007', 'description' => 'Standard office desk', 'unit' => 'pcs', 'stock' => 12, 'minimum_stock' => 5, 'purchase_price' => 800000, 'selling_price' => 1200000, 'is_active' => true],
            
            // Stationery
            ['category_id' => 4, 'supplier_id' => 4, 'name' => 'Pen Blue (Box)', 'code' => 'ITM008', 'description' => '50 pens per box', 'unit' => 'box', 'stock' => 5, 'minimum_stock' => 10, 'purchase_price' => 50000, 'selling_price' => 75000, 'is_active' => true],
            ['category_id' => 4, 'supplier_id' => 4, 'name' => 'Notebook A5', 'code' => 'ITM009', 'description' => '100 pages spiral notebook', 'unit' => 'pcs', 'stock' => 150, 'minimum_stock' => 50, 'purchase_price' => 15000, 'selling_price' => 25000, 'is_active' => true],
            
            // Cleaning Supplies
            ['category_id' => 5, 'supplier_id' => 2, 'name' => 'Floor Cleaner 1L', 'code' => 'ITM010', 'description' => 'Multi-purpose floor cleaner', 'unit' => 'bottle', 'stock' => 7, 'minimum_stock' => 10, 'purchase_price' => 30000, 'selling_price' => 50000, 'is_active' => true],
        ];

        foreach ($items as $item) {
            \App\Models\Item::create($item);
        }

        $this->command->info('Sample data created successfully!');
    }
}
