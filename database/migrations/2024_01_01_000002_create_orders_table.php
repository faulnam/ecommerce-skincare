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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Kurir yang ditugaskan
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable(); // Waktu penugasan kurir
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('status', [
                'pending_payment',   // Menunggu pembayaran
                'paid',              // Sudah bayar, menunggu assign kurir
                'assigned',          // Sudah ditugaskan ke kurir
                'picked_up',         // Kurir sudah ambil barang
                'on_delivery',       // Sedang dalam pengiriman
                'delivered',         // Sudah sampai, menunggu konfirmasi
                'completed',         // Selesai
                'cancelled'          // Dibatalkan
            ])->default('pending_payment');
            $table->enum('payment_status', [
                'unpaid',
                'pending_verification',
                'paid'
            ])->default('unpaid');
            $table->string('payment_proof')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            $table->text('shipping_address');
            $table->string('shipping_phone');
            $table->string('shipping_name');
            // Koordinat dan jarak
            $table->decimal('shipping_latitude', 10, 8)->nullable();
            $table->decimal('shipping_longitude', 11, 8)->nullable();
            $table->integer('delivery_distance_minutes')->nullable();
            // Jadwal pengiriman (jam 10:00 - 16:00)
            $table->date('delivery_date')->nullable();
            $table->string('delivery_time_slot')->nullable();
            // Tracking waktu pengiriman
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('on_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('delivery_notes')->nullable(); // Catatan dari kurir
            $table->string('waybill_id')->nullable(); // Nomor resi / waybill
            $table->text('notes')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
