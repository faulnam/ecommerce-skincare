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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['fixed', 'percent', 'cashback'])->default('fixed');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('minimum_purchase', 10, 2)->default(0);
            $table->decimal('maximum_discount', 10, 2)->nullable();
            $table->integer('cashback_coin')->default(0);
            $table->integer('quota')->default(100);
            $table->integer('used')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('thumbnail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
