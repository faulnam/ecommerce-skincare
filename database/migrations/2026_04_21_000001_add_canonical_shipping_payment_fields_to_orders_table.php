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
            if (!Schema::hasColumn('orders', 'ongkir_asli')) {
                 $table->decimal('ongkir_asli', 12, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'diskon_ongkir')) {
                 $table->decimal('diskon_ongkir', 12, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'ongkir_dibayar')) {
                 $table->decimal('ongkir_dibayar', 12, 2)->default(0);
            }

            if (!Schema::hasColumn('orders', 'total_pembayaran')) {
                 $table->decimal('total_pembayaran', 12, 2)->default(0);
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

            foreach (['ongkir_asli', 'diskon_ongkir', 'ongkir_dibayar', 'total_pembayaran'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
