<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $visitors = [];

        // 1. Keep your original local testing row
        $visitors[] = [
            'ip_address' => '127.0.0.1',
            'date'       => '2026-06-17',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0',
            'created_at' => '2026-06-17 02:39:09',
            'updated_at' => '2026-06-17 04:28:08',
        ];

        // 2. Dynamically generate 50 realistic fake records
        for ($i = 0; $i < 50; $i++) {
            // Generate a random timestamp sometime in the last 30 days
            $randomDate = fake()->dateTimeBetween('-30 days', 'now');
            $carbonDate = Carbon::instance($randomDate);

            $visitors[] = [
                'ip_address' => fake()->ipv4(),
                'date'       => $carbonDate->format('Y-m-d'),
                'user_agent' => fake()->userAgent(),
                'created_at' => $carbonDate->format('Y-m-d H:i:s'),
                // Simulate the user leaving the site 1 to 45 minutes later
                'updated_at' => $carbonDate->copy()->addMinutes(rand(1, 45))->format('Y-m-d H:i:s'),
            ];
        }

        // Insert all 51 rows at once.
        // insertOrIgnore ensures that if a random IP/Date combo accidentally duplicates, it won't crash the seeder.
        DB::table('visitors')->insertOrIgnore($visitors);
    }
}
