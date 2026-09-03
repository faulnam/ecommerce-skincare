<?php

namespace Database\Seeders;

use App\Models\Voucher;
use App\Models\User;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?: User::first();

        if (!$admin) {
            $this->command->warn('No admin user found. Skipping voucher seeder.');
            return;
        }

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Voucher::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $vouchers = [
            [
                'title' => 'GLOWING SERUM 20% OFF',
                'slug' => 'glowing-serum-20-off',
                'code' => 'GLOW20',
                'description' => 'Diskon 20% spesial untuk semua produk serum dan treatment pencerah kulit.',
                'type' => 'percent',
                'category' => 'all',
                'discount_value' => 20.00,
                'minimum_purchase' => 150000.00,
                'maximum_discount' => 50000.00,
                'cashback_coin' => 0,
                'quota' => 100,
                'used' => 12,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'title' => 'BARRIER REPAIR RP 25K',
                'slug' => 'barrier-repair-rp-25k',
                'code' => 'BARRIER25',
                'description' => 'Potongan langsung Rp 25.000 untuk pembelian pelembap ceramide dan sunscreen.',
                'type' => 'fixed',
                'category' => 'all',
                'discount_value' => 25000.00,
                'minimum_purchase' => 100000.00,
                'maximum_discount' => 25000.00,
                'cashback_coin' => 0,
                'quota' => 150,
                'used' => 34,
                'start_date' => now(),
                'end_date' => now()->addDays(45),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'title' => 'WELCOME BEAUTY BONUS 15%',
                'slug' => 'welcome-beauty-bonus-15',
                'code' => 'NEWGLOW15',
                'description' => 'Diskon 15% untuk pelanggan baru LUMINA Skincare tanpa minimum belanja.',
                'type' => 'percent',
                'category' => 'all',
                'discount_value' => 15.00,
                'minimum_purchase' => 50000.00,
                'maximum_discount' => 40000.00,
                'cashback_coin' => 0,
                'quota' => 200,
                'used' => 45,
                'start_date' => now(),
                'end_date' => now()->addDays(60),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'title' => 'GRATIS ONGKIR SE-INDONESIA',
                'slug' => 'gratis-ongkir-se-indonesia',
                'code' => 'FREESHIP',
                'description' => 'Potongan ongkir hingga Rp 20.000 ke seluruh wilayah Indonesia.',
                'type' => 'fixed',
                'category' => 'all',
                'discount_value' => 20000.00,
                'minimum_purchase' => 100000.00,
                'maximum_discount' => 20000.00,
                'cashback_coin' => 0,
                'quota' => 250,
                'used' => 88,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
    }
}