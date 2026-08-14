<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_driver_name')->nullable();
            $table->string('courier_driver_phone')->nullable();
            $table->string('courier_driver_photo')->nullable();
            $table->decimal('courier_driver_rating', 3, 2)->nullable();
            $table->string('courier_driver_vehicle')->nullable();
            $table->string('courier_driver_vehicle_number')->nullable();
            $table->timestamp('pickup_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'courier_driver_name',
                'courier_driver_phone',
                'courier_driver_photo',
                'courier_driver_rating',
                'courier_driver_vehicle',
                'courier_driver_vehicle_number',
                'pickup_time',
            ]);
        });
    }
};
