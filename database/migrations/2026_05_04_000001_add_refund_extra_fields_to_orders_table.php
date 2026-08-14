<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'refund_transaction_id')) {
                $table->string('refund_transaction_id')->nullable()->after('refund_status');
            }
            if (!Schema::hasColumn('orders', 'refund_note')) {
                $table->text('refund_note')->nullable()->after('refund_transaction_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'refund_transaction_id')) {
                $table->dropColumn('refund_transaction_id');
            }
            if (Schema::hasColumn('orders', 'refund_note')) {
                $table->dropColumn('refund_note');
            }
        });
    }
};
