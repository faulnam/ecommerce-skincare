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
        $realUsers = [
            [
                'email' => 'admin@skincare.id',
                'name' => 'Admin Skincare',
                'phone' => '081234567890',
                'address' => 'Jl. Lumina Beauty No. 1, Surabaya',
                'password' => Hash::make('qwertyu123'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'email' => 'customer@skincare.id',
                'name' => 'Customer Skincare',
                'phone' => '081234567891',
                'address' => 'Jl. Darmo No. 12, Surabaya',
                'password' => Hash::make('qwertyu123'),
                'role' => 'customer',
                'is_active' => true,
                'points' => 500,
            ],
            [
                'email' => 'courier@skincare.id',
                'name' => 'Kurir Skincare',
                'phone' => '081234567892',
                'address' => 'Jl. Ekspedisi No. 5, Surabaya',
                'password' => Hash::make('qwertyu123'),
                'role' => 'courier',
                'is_active' => true,
            ],
            [
                'email' => 'developer@skincare.id',
                'name' => 'Developer Skincare',
                'phone' => '081234567893',
                'address' => 'Jl. Teknologi No. 8, Surabaya',
                'password' => Hash::make('qwertyu123'),
                'role' => 'developer',
                'is_active' => true,
            ],
            [
                'email' => 'blogger@skincare.id',
                'name' => 'Blogger Skincare',
                'phone' => '081234567894',
                'address' => 'Jl. Penulis No. 3, Surabaya',
                'password' => Hash::make('qwertyu123'),
                'role' => 'blogger',
                'is_active' => true,
            ],
        ];

        foreach ($realUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $demoUsers = [
            [
                'email' => 'demo_admin@skincare.id',
                'name' => 'Admin Demo',
                'phone' => '081987654320',
                'address' => 'Jl. Demo Admin No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'email' => 'demo_customer@skincare.id',
                'name' => 'Customer Demo',
                'phone' => '081987654321',
                'address' => 'Jl. Demo Customer No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
                'points' => 300,
            ],
            [
                'email' => 'demo_courier@skincare.id',
                'name' => 'Kurir Demo',
                'phone' => '081987654322',
                'address' => 'Jl. Demo Kurir No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'courier',
                'is_active' => true,
            ],
            [
                'email' => 'demo_developer@skincare.id',
                'name' => 'Developer Demo',
                'phone' => '081987654323',
                'address' => 'Jl. Demo Developer No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'developer',
                'is_active' => true,
            ],
            [
                'email' => 'demo_blogger@skincare.id',
                'name' => 'Blogger Demo',
                'phone' => '081987654324',
                'address' => 'Jl. Demo Blogger No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'blogger',
                'is_active' => true,
            ],
        ];

        foreach ($demoUsers as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
