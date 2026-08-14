<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'price_adjustment')) {
                $table->decimal('price_adjustment', 12, 2)->default(0)->after('stock');
            }
            if (!Schema::hasColumn('product_variants', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('price_adjustment');
            }
            if (!Schema::hasColumn('product_variants', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'price_adjustment')) {
                $table->dropColumn('price_adjustment');
            }
            if (Schema::hasColumn('product_variants', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('product_variants', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
