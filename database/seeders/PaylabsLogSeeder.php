<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaylabsLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\PaylabsLog::create([
            'order_id' => 'ORD-987654',
            'endpoint' => '/payment/v1/charge',
            'method' => 'POST',
            'request_payload' => ['amount' => 150000, 'payment_type' => 'qris'],
            'response_payload' => ['success' => true, 'transaction_id' => 'paylabs_001'],
            'status_code' => 200,
        ]);
        
        \App\Models\PaylabsLog::create([
            'order_id' => 'ORD-987655',
            'endpoint' => '/webhook/paylabs',
            'method' => 'POST',
            'request_payload' => ['transaction_id' => 'paylabs_002', 'status' => 'settled'],
            'response_payload' => ['message' => 'ok'],
            'status_code' => 200,
        ]);
    }
}
