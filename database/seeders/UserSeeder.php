<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User - Full Access
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@inventory.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Jakarta',
            'is_active' => true,
        ]);
        $superadmin->assignRole('superadmin');

        // Create Admin User - Cannot access Roles & Permissions
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@inventory.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'address' => 'Bandung',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');
    }
}
