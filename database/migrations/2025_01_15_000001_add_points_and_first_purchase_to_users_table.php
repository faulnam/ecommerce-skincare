<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('points')->default(0)->after('avatar');
            $table->boolean('first_purchase_completed')->default(false)->after('points');
            $table->boolean('welcome_bonus_claimed')->default(false)->after('first_purchase_completed');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['points', 'first_purchase_completed', 'welcome_bonus_claimed']);
        });
    }
};
