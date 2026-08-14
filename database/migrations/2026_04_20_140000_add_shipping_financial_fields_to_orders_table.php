<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		// Intentionally left as no-op.
		// Canonical shipping financial columns are handled by:
		// 2026_04_21_000001_add_canonical_shipping_payment_fields_to_orders_table.php
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		// No-op
	}
};
