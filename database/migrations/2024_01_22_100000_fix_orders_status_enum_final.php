<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing 'paid' status to 'processing'
        DB::table('orders')
            ->where('status', 'paid')
            ->update(['status' => 'processing']);
        
        // Now safe to modify enum
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending_payment', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed', 'cancelled') NOT NULL DEFAULT 'pending_payment'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending_payment', 'paid', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed', 'cancelled') NOT NULL DEFAULT 'pending_payment'");
    }
};
