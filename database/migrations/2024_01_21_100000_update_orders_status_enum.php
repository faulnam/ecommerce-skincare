<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update enum status untuk menambahkan 'processing' dan 'shipped'
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM(
            'pending_payment',
            'paid',
            'processing',
            'shipped',
            'delivered',
            'completed',
            'cancelled',
            'assigned',
            'picked_up',
            'on_delivery'
        ) NOT NULL DEFAULT 'pending_payment'");
    }

    public function down(): void
    {
        // Rollback ke enum lama
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM(
            'pending_payment',
            'paid',
            'assigned',
            'picked_up',
            'on_delivery',
            'delivered',
            'completed',
            'cancelled'
        ) NOT NULL DEFAULT 'pending_payment'");
    }
};
