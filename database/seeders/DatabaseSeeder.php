<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Users (Admin & Customer)
        $this->call(UserSeeder::class);

        // Seed Vouchers
        $this->call(VoucherSeeder::class);

        // Seed Reviews
        $this->call(ReviewSeeder::class);

        // Seed Point Transactions
        $this->call(PointTransactionSeeder::class);

        // Seed Brand Catalogs
        $this->call(BrandCatalogSeeder::class);

        // Seed Banners & Models
        $this->call(BannerSeeder::class);

        // Seed Luxury Products
        $this->call(LuxuryProductSeeder::class);

        //seed insights
        $this->call(InsightSeeder::class);
    }
}
