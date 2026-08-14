<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'product_discount')) {
                $table->decimal('product_discount', 12, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'shipping_discount')) {
                $table->decimal('shipping_discount', 12, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'voucher_id')) {
                $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'voucher_discount')) {
                $table->decimal('voucher_discount', 12, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'points_used')) {
                $table->integer('points_used')->default(0);
            }

            if (!Schema::hasColumn('orders', 'points_discount')) {
                $table->decimal('points_discount', 12, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'delivery_distance_km')) {
                $table->decimal('delivery_distance_km', 10, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $dropColumns = [];

            foreach ([
                'product_discount',
                'shipping_discount',
                'voucher_discount',
                'points_used',
                'points_discount',
                'delivery_distance_km',
            ] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }

            if (Schema::hasColumn('orders', 'voucher_id')) {
                $table->dropForeign(['voucher_id']);
                $table->dropColumn('voucher_id');
            }
        });
    }
};
