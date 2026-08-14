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
            // Only add columns if they don't exist
            if (!Schema::hasColumn('orders', 'refund_at')) {
                $table->timestamp('refund_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'refund_amount')) {
                $table->decimal('refund_amount', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('orders', 'refund_status')) {
                $table->string('refund_status')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'refund_at')) {
                $table->dropColumn('refund_at');
            }
            if (Schema::hasColumn('orders', 'refund_amount')) {
                $table->dropColumn('refund_amount');
            }
            if (Schema::hasColumn('orders', 'refund_status')) {
                $table->dropColumn('refund_status');
            }
        });
    }
};
