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
        // For ENUM changes natively in Laravel 11/12 or DB facade
        // Sometimes Schema::table('insights', function (Blueprint $table) { $table->enum(...)->change(); }) 
        // doesn't work perfectly on older MySQL without doctrine/dbal.
        // We will use raw SQL to be 100% safe since Laravel 11/12 native schema builder handles string length but ENUM is tricky.
        
        DB::statement("ALTER TABLE insights MODIFY COLUMN status ENUM('draft', 'published', 'scheduled') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original
        DB::statement("ALTER TABLE insights MODIFY COLUMN status ENUM('draft', 'published') DEFAULT 'published'");
    }
};
