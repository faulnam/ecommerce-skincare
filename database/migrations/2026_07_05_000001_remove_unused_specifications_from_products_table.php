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
        Schema::table('products', function (Blueprint $table) {
            $columnsToDrop = [];
            
            $existingColumns = Schema::getColumnListing('products');
            
            foreach (['level', 'technology', 'suitable_for', 'collection', 'benefits', 'frame'] as $col) {
                if (in_array($col, $existingColumns)) {
                    $columnsToDrop[] = $col;
                }
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'level')) $table->string('level')->nullable();
            if (!Schema::hasColumn('products', 'technology')) $table->string('technology')->nullable();
            if (!Schema::hasColumn('products', 'suitable_for')) $table->string('suitable_for')->nullable();
            if (!Schema::hasColumn('products', 'collection')) $table->string('collection')->nullable();
            if (!Schema::hasColumn('products', 'benefits')) $table->string('benefits')->nullable();
            if (!Schema::hasColumn('products', 'frame')) $table->string('frame')->nullable();
        });
    }
};
