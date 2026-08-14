<?php

namespace Database\Seeders;

use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class PointTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil SEMUA user dengan role customer
        $customers = User::where('role', 'customer')->get();

        if ($customers->isEmpty()) {
            $this->command->warn('Tidak ada user customer ditemukan. Lewati seed poin.');
            return;
        }

        $now = now();

        foreach ($customers as $customer) {
            // Hapus transaksi poin lama milik user ini (supaya bersih)
            PointTransaction::where('user_id', $customer->id)->delete();

            // 1. Welcome bonus — masih valid (6 bulan dari sekarang)
            PointTransaction::create([
                'user_id' => $customer->id,
                'order_id' => null,
                'points' => 100,
                'type' => 'welcome_bonus',
                'description' => 'Welcome Bonus — Register',
                'balance_before' => 0,
                'balance_after' => 100,
                'expires_at' => $now->copy()->addMonths(6),
                'consumed' => 0,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ]);

            // 2. Earned points dari order lama — SUDAH EXPIRED (untuk test tampilan kedaluwarsa)
            PointTransaction::create([
                'user_id' => $customer->id,
                'order_id' => null,
                'points' => 150,
                'type' => 'earned',
                'description' => 'Cashback Order #1001',
                'balance_before' => 100,
                'balance_after' => 250,
                'expires_at' => $now->copy()->subDays(10), // sudah lewat
                'consumed' => 0,
                'created_at' => $now->copy()->subMonths(7),
                'updated_at' => $now->copy()->subMonths(7),
            ]);

            // 3. Earned points — masih valid (expire 3 bulan lagi)
            PointTransaction::create([
                'user_id' => $customer->id,
                'order_id' => null,
                'points' => 200,
                'type' => 'earned',
                'description' => 'Cashback Order #1002',
                'balance_before' => 250,
                'balance_after' => 450,
                'expires_at' => $now->copy()->addMonths(3),
                'consumed' => 0,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ]);

            // 4. Earned points — baru banget (expire 6 bulan lagi)
            PointTransaction::create([
                'user_id' => $customer->id,
                'order_id' => null,
                'points' => 300,
                'type' => 'earned',
                'description' => 'Cashback Order #1003',
                'balance_before' => 450,
                'balance_after' => 750,
                'expires_at' => $now->copy()->addMonths(6),
                'consumed' => 0,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ]);

            // 5. Redeemed points — menggunakan 50 poin
            PointTransaction::create([
                'user_id' => $customer->id,
                'order_id' => null,
                'points' => -50,
                'type' => 'redeemed',
                'description' => 'Redeemed for order #1004',
                'balance_before' => 750,
                'balance_after' => 700,
                'expires_at' => null,
                'consumed' => 0,
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ]);

            // 6. Tandai consumed pada transaksi welcome_bonus karena FIFO redeem
            PointTransaction::where('user_id', $customer->id)
                ->where('type', 'welcome_bonus')
                ->update(['consumed' => 50]);

            // Update kolom points user agar sinkron (seharusnya 100 + 200 + 300 = 600)
            $customer->points = 600;
            $customer->saveQuietly();

            $this->command->info("Point transactions seeded for {$customer->email}.");
        }

        $this->command->info("Total {$customers->count()} customer user(s) seeded with points.");
    }
}
