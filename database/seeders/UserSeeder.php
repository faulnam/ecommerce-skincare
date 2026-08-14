<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@hijab.id'],
            [
                'name' => 'Admin Hijab',
                'phone' => '081234567890',
                'address' => 'Jl. Admin No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Create Customer User
        User::updateOrCreate(
            ['email' => 'customer@hijab.id'],
            [
                'name' => 'Customer Demo',
                'phone' => '081234567891',
                'address' => 'Jl. Customer No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
            ]
        );
    }
}
