<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix categories
        DB::table('products')->where('category', 'pedas')->update(['category' => 'accessories']);
        DB::table('products')->where('category', 'original')->update(['category' => 'hijab']);
        
        // Fix Mix to FIBRIX in various columns
        $columns = ['carbon_type', 'core', 'faces', 'surface'];
        foreach ($columns as $column) {
            if (Schema::hasColumn('products', $column)) {
                // MySQL specific or generic whereRaw depending on DB
                DB::table('products')
                    ->where($column, 'like', '%Mix%')
                    ->update([$column => DB::raw("REPLACE($column, 'Mix', 'FIBRIX')")]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably reverse this
    }
};
