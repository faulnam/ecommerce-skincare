<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('paylabs_transaction_id')->nullable();
            $table->string('payment_gateway')->nullable()->comment('pakasir or paylabs');
            $table->string('payment_channel')->nullable()->comment('bca, bni, qris, ovo, etc');
            $table->text('payment_data')->nullable()->comment('JSON data from payment gateway');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'paylabs_transaction_id',
                'payment_gateway',
                'payment_channel',
                'payment_data',
            ]);
        });
    }
};
