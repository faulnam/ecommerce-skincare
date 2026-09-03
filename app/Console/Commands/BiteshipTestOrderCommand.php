<?php

namespace App\Console\Commands;

use App\Services\BiteshipService;
use Illuminate\Console\Command;

class BiteshipTestOrderCommand extends Command
{
    protected $signature = 'biteship:test-order {courier=jnt} {--status=confirmed : Order status (confirmed/cancelled)}';
    protected $description = 'Test Biteship order creation dengan berbagai ekspedisi dan status';

    public function handle()
    {
        $courier = strtolower($this->argument('courier'));
        $status = strtolower($this->option('status'));

        if (!in_array($status, ['confirmed', 'cancelled'])) {
            $this->error('Status must be either "confirmed" or "cancelled"');
            return Command::FAILURE;
        }

        $this->info("Testing Biteship order creation untuk ekspedisi: {$courier}");
        $this->info("Target Status: {$status}");
        $this->newLine();

        $biteship = app(BiteshipService::class);

        // Data test order
        $orderData = $this->getTestOrderData($courier);

        $this->info("Data Order:");
        $this->table(
            ['Field', 'Value'],
            [
                ['Courier', $orderData['courier_code']],
                ['Type', $orderData['courier_type']],
                ['Origin', $orderData['origin_address']],
                ['Destination', $orderData['destination_address']],
                ['Has Origin Coordinate', isset($orderData['origin_latitude']) ? 'Yes' : 'No'],
                ['Has Destination Coordinate', isset($orderData['destination_latitude']) ? 'Yes' : 'No'],
                ['Has Origin Postal Code', isset($orderData['origin_postal_code']) ? 'Yes' : 'No'],
                ['Has Destination Postal Code', isset($orderData['destination_postal_code']) ? 'Yes' : 'No'],
            ]
        );
        $this->newLine();

        // Test create order
        $this->info("Creating order...");
        $result = $biteship->createOrder($orderData);

        if ($result['success'] ?? false) {
            $this->info("✅ Order berhasil dibuat!");
            $this->newLine();

            $data = $result['data'] ?? [];
            $courier = $data['courier'] ?? [];
            $orderId = $data['id'] ?? null;

            $this->info("Detail Order:");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Order ID', $orderId ?? '-'],
                    ['Status', $data['status'] ?? '-'],
                    ['Waybill ID', $courier['waybill_id'] ?? '-'],
                    ['Courier Company', $courier['company'] ?? '-'],
                    ['Courier Type', $courier['type'] ?? '-'],
                    ['Price', 'Rp ' . number_format($data['price'] ?? 0, 0, ',', '.')],
                ]
            );
            $this->newLine();

            // Cancel order if requested
            if ($status === 'cancelled' && $orderId) {
                $this->info("Cancelling order...");
                $cancelResult = $biteship->cancelOrder($orderId, 'TEST ORDER - Cancelled via command');

                if ($cancelResult['success'] ?? false) {
                    $this->info("✅ Order berhasil dibatalkan!");
                    $this->newLine();

                    $cancelData = $cancelResult['data'] ?? [];
                    $this->info("Detail Cancel:");
                    $this->table(
                        ['Field', 'Value'],
                        [
                            ['Order ID', $cancelData['id'] ?? $orderId],
                            ['Status', $cancelData['status'] ?? 'cancelled'],
                        ]
                    );
                } else {
                    $this->error("❌ Gagal membatalkan order!");
                    $this->error("Error: " . ($cancelResult['message'] ?? 'Unknown error'));
                }
                $this->newLine();
            }

            $this->info("✅ Silakan cek dashboard Biteship untuk memastikan order muncul:");
            $this->info("   https://dashboard.biteship.com/orders");

            return Command::SUCCESS;
        } else {
            $this->error("❌ Gagal membuat order!");
            $this->error("Error: " . ($result['message'] ?? 'Unknown error'));
            $this->newLine();

            if (isset($result['error'])) {
                $this->error("Detail Error:");
                $this->line(json_encode($result['error'], JSON_PRETTY_PRINT));
            }

            return Command::FAILURE;
        }
    }

    protected function getTestOrderData(string $courier): array
    {
        // Koordinat toko (Surabaya)
    $originLat = (float) config('biteship.origin.latitude', -7.278417);
    $originLng = (float) config('biteship.origin.longitude', 112.632583);
        $originPostalCode = config('biteship.origin.postal_code', '61219');

        // Koordinat tujuan test (Surabaya - area berbeda)
        $destLat = -7.2756;
        $destLng = 112.7942;
        $destPostalCode = '60119';

        // Deteksi kurir instant
        $isInstantCourier = in_array($courier, ['grab', 'grabexpress', 'gojek', 'gosend'], true);

        // Tentukan courier type berdasarkan courier code
        $courierTypeMap = [
            'jnt' => 'ez',
            'jne' => 'reg',
            'anteraja' => 'reg',
            'sicepat' => 'reg',
            'paxel' => 'reg',
            'grab' => 'instant',
            'grabexpress' => 'instant',
            'gojek' => 'instant',
            'gosend' => 'instant',
        ];

        $courierType = $courierTypeMap[$courier] ?? 'reg';

        $baseData = [
            'shipper_contact_name' => config('branding.name', 'LUMINA'),
            'shipper_contact_phone' => config('branding.phone', '081234567890'),
            'shipper_contact_email' => config('mail.from.address', 'test@skincare.test'),
            
            'origin_contact_name' => config('branding.name', 'LUMINA'),
            'origin_contact_phone' => config('branding.phone', '081234567890'),
            'origin_address' => config('branding.address', 'Jl. Test No. 123, Surabaya'),
            'origin_note' => 'Pickup dari toko - TEST ORDER',
            
            'destination_contact_name' => 'Customer Test',
            'destination_contact_phone' => '081234567890',
            'destination_address' => 'Jl. Destination Test No. 456, Surabaya',
            'destination_note' => 'Dekat minimarket - TEST ORDER',
            
            'courier_code' => $courier,
            'courier_type' => $courierType,
            'courier_service_name' => $courierType,
            'delivery_type' => 'now',
            'reference_id' => 'TEST-' . strtoupper($courier) . '-' . time(),
            'order_note' => 'TEST ORDER - Mohon abaikan',
            
            'items' => [
                [
                    'name' => 'Test Product',
                    'description' => 'Test Product Description',
                    'value' => 100000,
                    'quantity' => 1,
                    'weight' => 500,
                    'length' => 30,
                    'width' => 25,
                    'height' => 3,
                ],
            ],
        ];

        // Tambahkan koordinat dan postal code sesuai jenis kurir
        if ($isInstantCourier) {
            // Kurir instant: WAJIB koordinat, OPTIONAL postal code
            $baseData['origin_latitude'] = $originLat;
            $baseData['origin_longitude'] = $originLng;
            $baseData['destination_latitude'] = $destLat;
            $baseData['destination_longitude'] = $destLng;
        } else {
            // Kurir regular: WAJIB postal code, OPTIONAL koordinat
            $baseData['origin_postal_code'] = $originPostalCode;
            $baseData['destination_postal_code'] = $destPostalCode;
            
            // Tetap kirim koordinat untuk akurasi lebih baik
            $baseData['origin_latitude'] = $originLat;
            $baseData['origin_longitude'] = $originLng;
            $baseData['destination_latitude'] = $destLat;
            $baseData['destination_longitude'] = $destLng;
        }

        return $baseData;
    }
}
