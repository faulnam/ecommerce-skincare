<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BiteshipLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\BiteshipLog::create([
            'order_id' => 'ORD-123456',
            'endpoint' => '/v1/orders',
            'method' => 'POST',
            'request_payload' => ['shipper' => 'LUMINA', 'recipient' => 'John Doe'],
            'response_payload' => ['success' => true, 'id' => 'biteship_123', 'status' => 'placed'],
            'status_code' => 200,
        ]);
        
        \App\Models\BiteshipLog::create([
            'order_id' => 'ORD-123457',
            'endpoint' => '/v1/trackings',
            'method' => 'GET',
            'request_payload' => ['waybill_id' => 'biteship_124'],
            'response_payload' => ['success' => false, 'error' => 'Not found'],
            'status_code' => 404,
            'error_message' => 'Tracking ID not found in Biteship',
        ]);
    }
}
