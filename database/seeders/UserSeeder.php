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
                'email' => 'admin@hijab.id',
                'name' => 'Admin Hijab',
                'phone' => '081234567890',
                'address' => 'Jl. Admin No. 1, Surabaya',
                'password' => Hash::make('qwertyu123'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'email' => 'customer@hijab.id',
                'name' => 'Customer Hijab',
                'phone' => '081234567891',
                'address' => 'Jl. Customer No. 1, Surabaya',
                'password' => Hash::make('qwertyu123'),
                'role' => 'customer',
                'is_active' => true,
                'points' => 500,
            ],
            [
                'email' => 'courier@hijab.id',
                'name' => 'Kurir Hijab',
                'phone' => '081234567892',
                'address' => 'Jl. Kurir No. 1, Surabaya',
                'password' => Hash::make('qwertyu123'),
                'role' => 'courier',
                'is_active' => true,
            ],
            [
                'email' => 'developer@hijab.id',
                'name' => 'Developer Hijab',
                'phone' => '081234567893',
                'address' => 'Jl. Developer No. 1, Surabaya',
                'password' => Hash::make('qwertyu123'),
                'role' => 'developer',
                'is_active' => true,
            ],
            [
                'email' => 'blogger@hijab.id',
                'name' => 'Blogger Hijab',
                'phone' => '081234567894',
                'address' => 'Jl. Blogger No. 1, Surabaya',
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
                'email' => 'demo_admin@hijab.id',
                'name' => 'Admin Demo',
                'phone' => '081987654320',
                'address' => 'Jl. Demo Admin No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'email' => 'demo_customer@hijab.id',
                'name' => 'Customer Demo',
                'phone' => '081987654321',
                'address' => 'Jl. Demo Customer No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
                'points' => 300,
            ],
            [
                'email' => 'demo_courier@hijab.id',
                'name' => 'Kurir Demo',
                'phone' => '081987654322',
                'address' => 'Jl. Demo Kurir No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'courier',
                'is_active' => true,
            ],
            [
                'email' => 'demo_developer@hijab.id',
                'name' => 'Developer Demo',
                'phone' => '081987654323',
                'address' => 'Jl. Demo Developer No. 1, Surabaya',
                'password' => Hash::make('password'),
                'role' => 'developer',
                'is_active' => true,
            ],
            [
                'email' => 'demo_blogger@hijab.id',
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
