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

        $vouchers = [
            [
                'title' => 'Hijab 8% OFF',
                'slug' => 'hijab-8-off',
                'code' => 'HIJAB8',
                'description' => 'Get 8% OFF selected performance hijabs.',
                'type' => 'percent',
                'category' => 'all',
                'discount_value' => 8.00,
                'minimum_purchase' => 1500000.00,
                'maximum_discount' => 300000.00,
                'cashback_coin' => 0,
                'quota' => 50,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Shoes 12% OFF',
                'slug' => 'shoes-12-off',
                'code' => 'SHOES12',
                'description' => 'Enjoy 12% OFF selected court shoes.',
                'type' => 'percent',
                'category' => 'shoes',
                'discount_value' => 12.00,
                'minimum_purchase' => 500000.00,
                'maximum_discount' => 200000.00,
                'cashback_coin' => 0,
                'quota' => 75,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Spin Strip 56% OFF',
                'slug' => 'spin-strip-56-off',
                'code' => 'SPIN56',
                'description' => 'Enjoy up to 56% OFF selected spin strips & overgrips.',
                'type' => 'percent',
                'category' => 'accessories',
                'discount_value' => 56.00,
                'minimum_purchase' => 50000.00,
                'maximum_discount' => 100000.00,
                'cashback_coin' => 0,
                'quota' => 200,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Accessories 15% OFF',
                'slug' => 'accessories-15-off',
                'code' => 'ACC15',
                'description' => 'Special 15% OFF on all accessories.',
                'type' => 'percent',
                'category' => 'accessories',
                'discount_value' => 15.00,
                'minimum_purchase' => 100000.00,
                'maximum_discount' => 30000.00,
                'cashback_coin' => 0,
                'quota' => 100,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],

            [
                'title' => 'New Arrival 10% OFF',
                'slug' => 'new-arrival-10-off',
                'code' => 'NEW10',
                'description' => 'Get 10% OFF on our new arrivals collection.',
                'type' => 'percent',
                'category' => 'new_arrivals',
                'discount_value' => 10.00,
                'minimum_purchase' => 300000.00,
                'maximum_discount' => 150000.00,
                'cashback_coin' => 0,
                'quota' => 150,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Flat Rp 50k OFF',
                'slug' => 'flat-rp-50k-off',
                'code' => 'FLAT50K',
                'description' => 'Flat Rp 50.000 discount on any purchase.',
                'type' => 'fixed',
                'category' => 'all',
                'discount_value' => 50000.00,
                'minimum_purchase' => 400000.00,
                'maximum_discount' => 50000.00,
                'cashback_coin' => 0,
                'quota' => 80,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Super Cashback 25%',
                'slug' => 'super-cashback-25',
                'code' => 'CB25',
                'description' => 'Get 25% cashback coins on your next purchase.',
                'type' => 'cashback',
                'category' => 'all',
                'discount_value' => 0.00,
                'minimum_purchase' => 200000.00,
                'maximum_discount' => null,
                'cashback_coin' => 50,
                'quota' => 120,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Welcome Voucher Rp 20k',
                'slug' => 'welcome-voucher-rp-20k',
                'code' => 'WELCOME20',
                'description' => 'Special discount for new members.',
                'type' => 'fixed',
                'category' => 'all',
                'discount_value' => 20000.00,
                'minimum_purchase' => 100000.00,
                'maximum_discount' => 20000.00,
                'cashback_coin' => 0,
                'quota' => 500,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Hijab Hijab Special 15%',
                'slug' => 'hijab-hijab-special-15',
                'code' => 'HIJAB15',
                'description' => 'Get 15% OFF on premium Hijab Hijabs.',
                'type' => 'percent',
                'category' => 'all',
                'discount_value' => 15.00,
                'minimum_purchase' => 2000000.00,
                'maximum_discount' => 500000.00,
                'cashback_coin' => 0,
                'quota' => 30,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],

            [
                'title' => 'Weekend Flash Sale Rp 100k',
                'slug' => 'weekend-flash-sale-rp-100k',
                'code' => 'WEEKEND100',
                'description' => 'Limited weekend flash sale voucher.',
                'type' => 'fixed',
                'category' => 'all',
                'discount_value' => 100000.00,
                'minimum_purchase' => 800000.00,
                'maximum_discount' => 100000.00,
                'cashback_coin' => 0,
                'quota' => 25,
                'used' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(3),
                'thumbnail' => null,
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::updateOrCreate(
                ['code' => $voucher['code']],
                $voucher
            );
        }

        $this->command->info('Vouchers seeded successfully!');
    }
}