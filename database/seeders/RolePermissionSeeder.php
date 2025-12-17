<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions (using dash format for consistency)
        $permissions = [
            // Dashboard
            'view-dashboard',
            
            // Categories
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            'export-categories',
            
            // Suppliers
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',
            'export-suppliers',
            
            // Items
            'view-items',
            'create-items',
            'edit-items',
            'delete-items',
            'export-items',
            
            // Vehicles
            'view-vehicles',
            'create-vehicles',
            'edit-vehicles',
            'delete-vehicles',
            'export-vehicles',
            
            // Users
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'export-users',
            
            // Roles & Permissions
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            
            // Transactions
            'view-transactions',
            'create-transactions',
            'edit-transactions',
            'delete-transactions',
            'export-transactions',

            // Reports
            'view-reports',
            
            // Profile
            'edit-profile',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin role - has all permissions (can access everything)
        $superadmin = \Spatie\Permission\Models\Role::create(['name' => 'superadmin']);
        $superadmin->givePermissionTo(\Spatie\Permission\Models\Permission::all());

        // Admin role - has all permissions except roles and permissions management
        $admin = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $adminPermissions = \Spatie\Permission\Models\Permission::all()->filter(function($permission) {
            return !in_array($permission->name, [
                'view-roles',
                'create-roles',
                'edit-roles',
                'delete-roles',
            ]);
        });
        $admin->givePermissionTo($adminPermissions);
    }
}
