<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected $apiKey;
    protected $baseUrl;
    protected $sandbox;
    protected $useMock;

    public function __construct()
    {
        $this->apiKey = config('biteship.api_key');
        $this->baseUrl = config('biteship.base_url');
        $this->sandbox = config('biteship.sandbox', true);
        $this->useMock = config('biteship.use_mock', false);
    }

    /**
     * Get shipping rates from Biteship API (Real API)
     * 
     * @param array $params
     * @return array
     */
    public function getRatesFromAPI(array $params)
    {
        try {
            // Prepare items for API
            $items = [];
            foreach ($params['items'] as $item) {
                $items[] = [
                    'name' => $item['name'],
                    'value' => $item['value'] ?? 10000,
                    'weight' => $item['weight'] ?? 500,
                    'quantity' => $item['quantity'] ?? 1,
                ];
            }

            $payload = [
                'origin_postal_code' => $params['origin_postal_code'] ?? config('biteship.origin.postal_code', '60119'),
                'destination_postal_code' => $params['destination_postal_code'] ?? config('biteship.origin.postal_code', '61219'),
                'couriers' => $params['couriers'] ?? 'jne,jnt,anteraja,gojek,grab,paxel',
                'items' => $items,
            ];

            // Koordinat sangat membantu Biteship menghitung eligibility layanan instant/same-day.
            $originLatitude = $params['origin_latitude'] ?? config('biteship.origin.latitude');
            $originLongitude = $params['origin_longitude'] ?? config('biteship.origin.longitude');
            $destinationLatitude = $params['destination_latitude'] ?? null;
            $destinationLongitude = $params['destination_longitude'] ?? null;

            if (is_numeric($originLatitude) && is_numeric($originLongitude)) {
                $payload['origin_latitude'] = (float) $originLatitude;
                $payload['origin_longitude'] = (float) $originLongitude;
                $payload['origin_coordinate_latitude'] = (float) $originLatitude;
                $payload['origin_coordinate_longitude'] = (float) $originLongitude;
            }

            if (is_numeric($destinationLatitude) && is_numeric($destinationLongitude)) {
                $payload['destination_latitude'] = (float) $destinationLatitude;
                $payload['destination_longitude'] = (float) $destinationLongitude;
                $payload['destination_coordinate_latitude'] = (float) $destinationLatitude;
                $payload['destination_coordinate_longitude'] = (float) $destinationLongitude;
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/rates/couriers", $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Process and format the rates
                $formattedRates = $this->formatRates($data['pricing'] ?? []);
                
                return [
                    'success' => true,
                    'data' => [
                        'pricing' => $formattedRates,
                    ],
                ];
            }

            Log::error('Biteship getRatesFromAPI failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get rates: ' . ($response->json()['error'] ?? $response->body()),
            ];
        } catch (\Exception $e) {
            Log::error('Biteship getRatesFromAPI exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format rates from Biteship API response
     * 
     * @param array $pricing
     * @return array
     */
    private function formatRates(array $pricing): array
    {
        $formatted = [];
        
        foreach ($pricing as $rate) {
            // Determine service category
            $category = $this->determineCategory(
                $rate['courier_code'] ?? '',
                $rate['courier_service_name'] ?? ''
            );
            
            $rawDuration = trim((string) ($rate['duration'] ?? ''));
            $duration = $rawDuration !== '' ? $rawDuration : 'Estimasi tidak tersedia';
            
            $formatted[] = [
                'courier_code' => $rate['courier_code'] ?? '',
                'courier_name' => $rate['courier_name'] ?? '',
                'courier_service_code' => $rate['courier_service_code'] ?? ($rate['service_code'] ?? ($rate['courier_type'] ?? null)),
                'courier_type' => $rate['courier_type'] ?? ($rate['courier_service_code'] ?? ($rate['service_code'] ?? null)),
                'courier_service_name' => $rate['courier_service_name'] ?? '',
                'service_type' => $category,
                'price' => (int) ($rate['price'] ?? 0),
                'duration' => $duration,
                'duration_minutes' => $this->convertETDToMinutes($duration, $category),
                'company' => $rate['company'] ?? '',
                'description' => $rate['description'] ?? '',
            ];
        }
        
        usort($formatted, function ($a, $b) {
            if ($a['price'] === $b['price']) {
                return $a['duration_minutes'] <=> $b['duration_minutes'];
            }

            return $a['price'] <=> $b['price'];
        });

        if (!empty($formatted)) {
            $cheapest = min(array_column($formatted, 'price'));
            $fastest = min(array_column($formatted, 'duration_minutes'));

            foreach ($formatted as $index => $rate) {
                $formatted[$index]['is_cheapest'] = ($rate['price'] === $cheapest);
                $formatted[$index]['is_fastest'] = ($rate['duration_minutes'] === $fastest);
            }
        }

        return $formatted;
    }

    /**
     * Determine service category based on courier and service name
     * 
     * @param string $courierCode
     * @param string $serviceName
     * @return string
     */
    private function determineCategory(string $courierCode, string $serviceName): string
    {
        // Instant couriers
        if (in_array(strtolower($courierCode), ['gojek', 'gosend', 'grab', 'grabexpress'])) {
            if (stripos($serviceName, 'same day') !== false) {
                return 'sameday';
            }
            return 'instant';
        }
        
        // Same day services
        if (stripos($serviceName, 'same day') !== false || 
            stripos($serviceName, 'sameday') !== false) {
            return 'sameday';
        }
        
        // Default to regular
        return 'regular';
    }
    
    /**
     * Format ETD (Estimated Time of Delivery)
     * 
     * @param string $duration
     * @param string $category
     * @return string
     */
    private function formatETD(string $duration, string $category): string
    {
        if ($category === 'instant') {
            return '1-3 jam';
        }
        
        if ($category === 'sameday') {
            return '6-8 jam (hari yang sama)';
        }
        
        // Parse duration like "2-3" or "1-2 days"
        if (preg_match('/(\d+)-(\d+)/', $duration, $matches)) {
            return $matches[1] . '-' . $matches[2] . ' hari';
        }
        
        if (preg_match('/(\d+)/', $duration, $matches)) {
            $days = (int) $matches[1];
            return $days . ' hari';
        }
        
        return $duration ?: '2-3 hari';
    }
    
    /**
     * Convert ETD to minutes for sorting
     * 
     * @param string $etd
     * @param string $category
     * @return int
     */
    private function convertETDToMinutes(string $etd, string $category): int
    {
        // Parse hours first (contoh: "2-3 hours", "3 jam")
        if (preg_match('/(\d+)\s*-\s*(\d+)\s*(jam|hour|hours|hrs|h)/i', $etd, $matches)) {
            $avgHours = ((int) $matches[1] + (int) $matches[2]) / 2;
            return (int) round($avgHours * 60);
        }

        if (preg_match('/(\d+)\s*(jam|hour|hours|hrs|h)/i', $etd, $matches)) {
            return (int) $matches[1] * 60;
        }

        // Parse days (contoh: "1-2 days", "2 hari")
        if (preg_match('/(\d+)\s*-\s*(\d+)\s*(hari|day|days|d)/i', $etd, $matches)) {
            $avgDays = ((int) $matches[1] + (int) $matches[2]) / 2;
            return (int) round($avgDays * 24 * 60);
        }

        if (preg_match('/(\d+)\s*(hari|day|days|d)/i', $etd, $matches)) {
            return (int) $matches[1] * 24 * 60;
        }

        if ($category === 'instant') {
            return 180; // 3 hours
        }
        
        if ($category === 'sameday') {
            return 480; // 8 hours
        }
        
        // Parse "2-3 hari" format
        if (preg_match('/(\d+)-(\d+)\s*hari/', $etd, $matches)) {
            $avgDays = ((int)$matches[1] + (int)$matches[2]) / 2;
            return (int) ($avgDays * 24 * 60);
        }
        
        if (preg_match('/(\d+)\s*hari/', $etd, $matches)) {
            return (int) $matches[1] * 24 * 60;
        }
        
        return 2880; // Default 2 days
    }

    /**
     * Get shipping rates from Biteship
     * 
     * @param array $params
     * @return array
     */
    public function getRates(array $params)
    {
        $strictBiteship = (bool) ($params['strict_biteship'] ?? false);

        $apiResult = $this->getRatesFromAPI([
            'origin_postal_code' => $params['origin_postal_code'] ?? config('biteship.origin.postal_code', '61219'),
            'destination_postal_code' => $params['destination_postal_code'] ?? config('biteship.origin.postal_code', '61219'),
            'origin_latitude' => $params['origin_latitude'] ?? config('biteship.origin.latitude'),
            'origin_longitude' => $params['origin_longitude'] ?? config('biteship.origin.longitude'),
            'destination_latitude' => $params['destination_latitude'] ?? null,
            'destination_longitude' => $params['destination_longitude'] ?? null,
            'couriers' => $params['couriers'] ?? 'jne,jnt,anteraja,gojek,grab,paxel',
            'items' => $params['items'] ?? [],
        ]);

        if (($apiResult['success'] ?? false) && !empty($apiResult['data']['pricing'] ?? [])) {
            return $apiResult;
        }

        if ($strictBiteship) {
            return [
                'success' => false,
                'message' => $apiResult['message'] ?? 'Layanan ongkir Biteship sedang tidak tersedia. Silakan coba lagi.',
            ];
        }

        Log::warning('Biteship getRates fallback ke local calculator', [
            'message' => $apiResult['message'] ?? 'API rates gagal / kosong',
        ]);

        // fallback lokal jika API gagal
        $totalWeight = collect($params['items'] ?? [])->sum(function ($item) {
            return ($item['weight'] ?? 500) * ($item['quantity'] ?? 1);
        });
        $totalWeight = max($totalWeight, 1000); // minimum 1kg

        // Deteksi zona berdasarkan koordinat tujuan
        $zone = $this->detectZone(
            $params['destination_latitude'],
            $params['destination_longitude']
        );

        // Hitung jarak
    $storeLat = config('biteship.origin.latitude', -7.278417);
    $storeLng = config('biteship.origin.longitude', 112.632583);
        $distance = $this->haversine(
            $storeLat,
            $storeLng,
            $params['destination_latitude'],
            $params['destination_longitude']
        );

        // Hitung ongkir berdasarkan zona dan berat
        $rates = $this->calculateRates($zone, $totalWeight, $distance);

        return [
            'success' => true,
            'data' => [
                'pricing' => $rates
            ],
        ];
    }

    private function detectZone(float $lat, float $lng): string
    {
    $storeLat = config('biteship.origin.latitude', -7.278417);
    $storeLng = config('biteship.origin.longitude', 112.632583);

        // Hitung jarak dari toko ke tujuan (km)
        $distance = $this->haversine($storeLat, $storeLng, $lat, $lng);

        if ($distance <= 30) return 'same_city';       // Dalam kota / sekitar
        if ($distance <= 150) return 'nearby';          // Kota tetangga
        if ($distance <= 500) return 'inter_city';      // Antar kota dalam pulau
        return 'inter_island';                          // Antar pulau
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function calculateRates(string $zone, int $weightGram, float $distance): array
    {
        // Harga dasar per kg per zona (dalam rupiah)
        $baseRates = [
            'same_city'    => ['regular' => 8000,  'express' => 14000, 'sameday' => 20000, 'instant' => 30000],
            'nearby'       => ['regular' => 12000, 'express' => 20000, 'sameday' => 35000, 'instant' => 45000],
            'inter_city'   => ['regular' => 15000, 'express' => 25000, 'sameday' => null,  'instant' => null],
            'inter_island' => ['regular' => 25000, 'express' => 40000, 'sameday' => null,  'instant' => null],
        ];

        $weightKg = max(1, ceil($weightGram / 1000));
        $base = $baseRates[$zone];

        $couriers = [
            [
                'courier_code'         => 'jnt',
                'courier_name'         => 'J&T Express',
                'services' => [
                    ['name' => 'EZ (Reguler)', 'type' => 'regular',  'multiplier' => 1.0],
                    ['name' => 'Express',      'type' => 'express',  'multiplier' => 1.1],
                ],
            ],
            [
                'courier_code'         => 'anteraja',
                'courier_name'         => 'AnterAja',
                'services' => [
                    ['name' => 'Reguler',      'type' => 'regular',  'multiplier' => 0.95],
                    ['name' => 'Same Day',     'type' => 'sameday',  'multiplier' => 1.0],
                ],
            ],
            [
                'courier_code'         => 'paxel',
                'courier_name'         => 'Paxel',
                'services' => [
                    ['name' => 'Regular',      'type' => 'regular',  'multiplier' => 1.05],
                    ['name' => 'Same Day',     'type' => 'sameday',  'multiplier' => 1.1],
                    ['name' => 'Instant',      'type' => 'instant',  'multiplier' => 1.0],
                ],
            ],
            [
                'courier_code'         => 'gosend',
                'courier_name'         => 'GoSend',
                'services' => [
                    ['name' => 'Instant',      'type' => 'instant',  'multiplier' => 1.0],
                    ['name' => 'Same Day',     'type' => 'sameday',  'multiplier' => 0.85],
                ],
            ],
            [
                'courier_code'         => 'grabexpress',
                'courier_name'         => 'GrabExpress',
                'services' => [
                    ['name' => 'Instant',      'type' => 'instant',  'multiplier' => 0.95],
                    ['name' => 'Same Day',     'type' => 'sameday',  'multiplier' => 0.8],
                ],
            ],
        ];

        $rates = [];
        foreach ($couriers as $courier) {
            foreach ($courier['services'] as $service) {
                $basePrice = $base[$service['type']] ?? null;
                if ($basePrice === null) continue; // layanan tidak tersedia di zona ini

                $price = (int) round($basePrice * $weightKg * $service['multiplier']);

                $durationMap = [
                    'regular'  => '2-4 hari',
                    'express'  => '1-2 hari',
                    'sameday'  => 'Hari ini',
                    'instant'  => '2-4 jam',
                ];

                // Estimasi durasi dalam menit (untuk validasi)
                $durationMinutes = [
                    'regular'  => 2880,  // 2 hari
                    'express'  => 1440,  // 1 hari
                    'sameday'  => 720,   // 12 jam
                    'instant'  => 180,   // 3 jam
                ];

                $rates[] = [
                    'courier_code'         => $courier['courier_code'],
                    'courier_name'         => $courier['courier_name'],
                    'courier_service_code' => strtolower(strtok($service['name'], ' ')),
                    'courier_type'         => strtolower(strtok($service['name'], ' ')),
                    'courier_service_name' => $service['name'],
                    'service_type'         => $service['type'],
                    'duration'             => $durationMap[$service['type']],
                    'duration_minutes'     => $durationMinutes[$service['type']],
                    'price'                => $price,
                    'weight_kg'            => $weightKg,
                    'distance_km'          => round($distance, 2),
                    'zone'                 => $zone,
                ];
            }
        }

        return $rates;
    }

    /**
     * Create order in Biteship (Request Pickup)
     * 
     * @param array $orderData
     * @return array
     */
    public function createOrder(array $orderData)
    {
        // Mock hanya bila diaktifkan eksplisit
        if ($this->useMock) {
            return $this->mockCreateOrder($orderData);
        }

        try {
            $courierCode = strtolower((string) ($orderData['courier_code'] ?? ''));
            $courierType = $orderData['courier_type'] ?? $this->inferCourierType(
                $courierCode,
                (string) ($orderData['courier_service_name'] ?? '')
            );

            $originLatitude = (float) ($orderData['origin_latitude'] ?? config('biteship.origin.latitude'));
            $originLongitude = (float) ($orderData['origin_longitude'] ?? config('biteship.origin.longitude'));
            $destinationLatitude = (float) ($orderData['destination_latitude'] ?? 0);
            $destinationLongitude = (float) ($orderData['destination_longitude'] ?? 0);
            $orderNote = trim((string) ($orderData['order_note'] ?? ''));

            // Deteksi apakah ini kurir instant (Grab, GoSend, dll)
            $isInstantCourier = in_array($courierCode, ['grab', 'grabexpress', 'gojek', 'gosend'], true);

            $payload = [
                'shipper_contact_name' => $orderData['shipper_contact_name'] ?? ($orderData['origin_contact_name'] ?? config('branding.name', 'Hijab')),
                'shipper_contact_phone' => $orderData['shipper_contact_phone'] ?? ($orderData['origin_contact_phone'] ?? config('branding.phone', '081234567890')),
                'shipper_contact_email' => $orderData['shipper_contact_email'] ?? config('mail.from.address', 'qa@hijab.test'),

                'origin_contact_name' => $orderData['origin_contact_name'] ?? config('branding.name', 'Hijab'),
                'origin_contact_phone' => $orderData['origin_contact_phone'] ?? config('branding.phone', '081234567890'),
                'origin_address' => $orderData['origin_address'] ?? config('branding.address', 'Toko Hijab'),
                'origin_note' => $orderData['origin_note'] ?? 'Pickup dari toko',
                
                'destination_contact_name' => $orderData['destination_contact_name'],
                'destination_contact_phone' => $orderData['destination_contact_phone'],
                'destination_address' => $orderData['destination_address'],
                'destination_note' => $orderData['destination_note'] ?? '',
                
                'courier_company' => $courierCode,
                'courier_type' => $courierType,
                'delivery_type' => $orderData['delivery_type'] ?? 'now',
                'items' => $orderData['items'],
            ];

            // Untuk kurir instant: WAJIB pakai coordinate, postal_code OPTIONAL
            if ($isInstantCourier) {
                $payload['origin_coordinate'] = [
                    'latitude' => $originLatitude,
                    'longitude' => $originLongitude,
                ];
                $payload['destination_coordinate'] = [
                    'latitude' => $destinationLatitude,
                    'longitude' => $destinationLongitude,
                ];
            } else {
                // Untuk kurir regular: postal_code WAJIB, coordinate OPTIONAL tapi sangat membantu
                $payload['origin_postal_code'] = $orderData['origin_postal_code'] ?? config('biteship.origin.postal_code');
                $payload['destination_postal_code'] = $orderData['destination_postal_code'] ?? '61219';
                
                // Tetap kirim coordinate jika tersedia untuk akurasi lebih baik
                if ($originLatitude && $originLongitude) {
                    $payload['origin_coordinate'] = [
                        'latitude' => $originLatitude,
                        'longitude' => $originLongitude,
                    ];
                }
                if ($destinationLatitude && $destinationLongitude) {
                    $payload['destination_coordinate'] = [
                        'latitude' => $destinationLatitude,
                        'longitude' => $destinationLongitude,
                    ];
                }
            }

            if ($orderNote !== '') {
                $payload['order_note'] = $orderNote;
            }

            if (!empty($orderData['delivery_datetime'])) {
                $payload['delivery_datetime'] = $orderData['delivery_datetime'];
            }

            if (!empty($orderData['reference_id'])) {
                $payload['reference_id'] = $orderData['reference_id'];
            }

            $paymentPayload = $this->buildPaymentPayloadForBiteship($orderData);
            $payloadWithPayment = array_merge($payload, $paymentPayload);

            $rawPaymentMethod = strtolower(trim((string) ($orderData['payment_method'] ?? '')));
            $isCodOrder = filter_var(($orderData['is_cod'] ?? false), FILTER_VALIDATE_BOOLEAN)
                || in_array($rawPaymentMethod, ['cod', 'cash_on_delivery'], true);

            Log::info('Biteship createOrder request', [
                'reference_id' => $payloadWithPayment['reference_id'] ?? null,
                'courier_company' => $payloadWithPayment['courier_company'] ?? null,
                'courier_type' => $payloadWithPayment['courier_type'] ?? null,
                'is_instant_courier' => $isInstantCourier,
                'has_origin_coordinate' => isset($payloadWithPayment['origin_coordinate']),
                'has_destination_coordinate' => isset($payloadWithPayment['destination_coordinate']),
                'has_origin_postal_code' => isset($payloadWithPayment['origin_postal_code']),
                'has_destination_postal_code' => isset($payloadWithPayment['destination_postal_code']),
                'is_cod_order' => $isCodOrder,
                'payment_method' => $paymentPayload['payment_method'] ?? null,
            ]);

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/orders", $payloadWithPayment);

            // Fallback aman: jika field payment tidak dikenali API, coba ulang tanpa field payment
            // HANYA untuk non-COD agar order COD tidak berubah menjadi Non-COD di Biteship.
            if (!$response->successful() && !empty($paymentPayload)) {
                Log::warning('Biteship createOrder failed with payment payload, retry without payment payload', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payment_payload' => $paymentPayload,
                    'is_cod_order' => $isCodOrder,
                ]);

                if (!$isCodOrder) {
                    $response = Http::withoutVerifying()->withHeaders([
                        'Authorization' => $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])->post("{$this->baseUrl}/orders", $payload);
                }
            }

            if ($response->successful()) {
                $responseJson = $response->json();

                Log::info('Biteship createOrder response payment meta', [
                    'reference_id' => $payloadWithPayment['reference_id'] ?? null,
                    'biteship_order_id' => $responseJson['id'] ?? ($responseJson['order_id'] ?? null),
                    'status' => $responseJson['status'] ?? null,
                    'note' => $responseJson['note'] ?? null,
                    'is_cod_order' => $isCodOrder,
                ]);

                return [
                    'success' => true,
                    'data' => $responseJson,
                ];
            }

            Log::error('Biteship createOrder failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payloadWithPayment,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create pickup order: ' . ($response->json()['error'] ?? $response->body()),
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Biteship createOrder exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create draft order in Biteship.
     *
     * Endpoint utama: POST /v1/draft_orders
     */
    public function createDraftOrder(array $orderData): array
    {
        if ($this->useMock) {
            return [
                'success' => true,
                'data' => [
                    'id' => 'DRAFT-' . strtoupper(uniqid()),
                    'status' => 'draft',
                ],
            ];
        }

        try {
            $courierCode = strtolower((string) ($orderData['courier_code'] ?? ''));
            $courierType = $orderData['courier_type'] ?? $this->inferCourierType(
                $courierCode,
                (string) ($orderData['courier_service_name'] ?? '')
            );

            $originLatitude = (float) ($orderData['origin_latitude'] ?? config('biteship.origin.latitude'));
            $originLongitude = (float) ($orderData['origin_longitude'] ?? config('biteship.origin.longitude'));
            $destinationLatitude = (float) ($orderData['destination_latitude'] ?? 0);
            $destinationLongitude = (float) ($orderData['destination_longitude'] ?? 0);
            $orderNote = trim((string) ($orderData['order_note'] ?? ''));

            // Deteksi apakah ini kurir instant (Grab, GoSend, dll)
            $isInstantCourier = in_array($courierCode, ['grab', 'grabexpress', 'gojek', 'gosend'], true);

            $payload = [
                'shipper_contact_name' => $orderData['shipper_contact_name'] ?? ($orderData['origin_contact_name'] ?? config('branding.name', 'Hijab')),
                'shipper_contact_phone' => $orderData['shipper_contact_phone'] ?? ($orderData['origin_contact_phone'] ?? config('branding.phone', '081234567890')),
                'shipper_contact_email' => $orderData['shipper_contact_email'] ?? config('mail.from.address', 'qa@hijab.test'),

                'origin_contact_name' => $orderData['origin_contact_name'] ?? config('branding.name', 'Hijab'),
                'origin_contact_phone' => $orderData['origin_contact_phone'] ?? config('branding.phone', '081234567890'),
                'origin_address' => $orderData['origin_address'] ?? config('branding.address', 'Toko Hijab'),
                'origin_note' => $orderData['origin_note'] ?? 'Pickup dari toko',

                'destination_contact_name' => $orderData['destination_contact_name'],
                'destination_contact_phone' => $orderData['destination_contact_phone'],
                'destination_address' => $orderData['destination_address'],
                'destination_note' => $orderData['destination_note'] ?? '',

                'courier_company' => $courierCode,
                'courier_type' => $courierType,
                'delivery_type' => $orderData['delivery_type'] ?? 'now',
                'items' => $orderData['items'],
                'is_draft' => true,
            ];

            // Untuk kurir instant: WAJIB pakai coordinate, postal_code OPTIONAL
            if ($isInstantCourier) {
                $payload['origin_coordinate'] = [
                    'latitude' => $originLatitude,
                    'longitude' => $originLongitude,
                ];
                $payload['destination_coordinate'] = [
                    'latitude' => $destinationLatitude,
                    'longitude' => $destinationLongitude,
                ];
            } else {
                // Untuk kurir regular: postal_code WAJIB, coordinate OPTIONAL tapi sangat membantu
                $payload['origin_postal_code'] = $orderData['origin_postal_code'] ?? config('biteship.origin.postal_code');
                $payload['destination_postal_code'] = $orderData['destination_postal_code'] ?? '61219';
                
                // Tetap kirim coordinate jika tersedia untuk akurasi lebih baik
                if ($originLatitude && $originLongitude) {
                    $payload['origin_coordinate'] = [
                        'latitude' => $originLatitude,
                        'longitude' => $originLongitude,
                    ];
                }
                if ($destinationLatitude && $destinationLongitude) {
                    $payload['destination_coordinate'] = [
                        'latitude' => $destinationLatitude,
                        'longitude' => $destinationLongitude,
                    ];
                }
            }

            if ($orderNote !== '') {
                $payload['order_note'] = $orderNote;
            }

            if (!empty($orderData['delivery_datetime'])) {
                $payload['delivery_datetime'] = $orderData['delivery_datetime'];
            }

            if (!empty($orderData['reference_id'])) {
                $payload['reference_id'] = $orderData['reference_id'];
            }

            $paymentPayload = $this->buildPaymentPayloadForBiteship($orderData);
            $payloadWithPayment = array_merge($payload, $paymentPayload);

            Log::info('Biteship createDraftOrder request', [
                'reference_id' => $payloadWithPayment['reference_id'] ?? null,
                'courier_company' => $payloadWithPayment['courier_company'] ?? null,
                'courier_type' => $payloadWithPayment['courier_type'] ?? null,
                'is_instant_courier' => $isInstantCourier,
                'has_origin_coordinate' => isset($payloadWithPayment['origin_coordinate']),
                'has_destination_coordinate' => isset($payloadWithPayment['destination_coordinate']),
                'has_origin_postal_code' => isset($payloadWithPayment['origin_postal_code']),
                'has_destination_postal_code' => isset($payloadWithPayment['destination_postal_code']),
                'payment_method' => $payloadWithPayment['payment_method'] ?? null,
            ]);

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/draft_orders", $payloadWithPayment);

            if ($response->successful()) {
                $responseJson = $response->json();

                Log::info('Biteship createDraftOrder response', [
                    'reference_id' => $payloadWithPayment['reference_id'] ?? null,
                    'biteship_draft_order_id' => $this->extractBiteshipOrderId($responseJson),
                    'status' => $responseJson['status'] ?? null,
                ]);

                return [
                    'success' => true,
                    'data' => $responseJson,
                ];
            }

            Log::error('Biteship createDraftOrder failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payloadWithPayment,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create draft order: ' . ($response->json()['error'] ?? $response->body()),
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Biteship createDraftOrder exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create draft order in Biteship from local order (pending payment stage).
     */
    public function createDraftOrderFromOrder(Order $order, ?string $courierTypeOverride = null): array
    {
        if (!empty($order->biteship_draft_order_id)) {
            return [
                'success' => true,
                'message' => 'Draft order Biteship sudah tersedia.',
                'data' => [
                    'biteship_draft_order_id' => $order->biteship_draft_order_id,
                ],
            ];
        }

        $order->loadMissing('items.product');

        $items = $order->items->map(function ($item) {
            $weight = (int) (($item->product->weight ?? 500) * max(1, (int) $item->quantity));

            return [
                'name' => $item->product_name,
                'description' => $item->product_name,
                'value' => (int) $item->product_price,
                'quantity' => (int) $item->quantity,
                'weight' => max(1, $weight),
                'length' => 30,
                'width' => 25,
                'height' => 3,
            ];
        })->values()->toArray();

        if (empty($items)) {
            return [
                'success' => false,
                'message' => 'Order item kosong, tidak bisa create Biteship draft order.',
            ];
        }

        $customerOrderNote = trim((string) ($order->notes ?? ''));

        $draftPayload = [
            'shipper_contact_name' => config('branding.name', 'Hijab'),
            'shipper_contact_phone' => config('branding.phone', '081234567890'),
            'origin_contact_name' => config('branding.name', 'Hijab'),
            'origin_contact_phone' => config('branding.phone', '081234567890'),
            'origin_address' => config('branding.address', 'Toko Hijab'),
            'origin_note' => 'Pickup dari toko',
            'origin_latitude' => (float) config('biteship.origin.latitude'),
            'origin_longitude' => (float) config('biteship.origin.longitude'),
            'origin_postal_code' => config('biteship.origin.postal_code'),

            'destination_contact_name' => $order->shipping_name,
            'destination_contact_phone' => $order->shipping_phone,
            'destination_address' => $order->shipping_address,
            'destination_note' => $order->notes ?? '',
            'destination_latitude' => (float) $order->shipping_latitude,
            'destination_longitude' => (float) $order->shipping_longitude,
            'destination_postal_code' => $order->shipping_postal_code ?: config('biteship.origin.postal_code', '61219'),

            'courier_code' => strtolower((string) $order->courier_code),
            'courier_service_name' => (string) ($order->courier_service_name ?? ''),
            'courier_type' => $courierTypeOverride ? strtolower(trim($courierTypeOverride)) : null,
            'delivery_type' => 'now',
            'reference_id' => $order->order_number,
            'is_cod' => false,
            'payment_method' => 'online_payment',
            'total_amount' => (int) round((float) $order->total),
            'cash_on_delivery_amount' => 0,
            'items' => $items,
        ];

        if ($customerOrderNote !== '') {
            $draftPayload['order_note'] = $customerOrderNote;
        }

        $createResult = $this->createDraftOrder($draftPayload);

        if (!($createResult['success'] ?? false)) {
            return $createResult;
        }

        $raw = $createResult['data'] ?? [];

        return [
            'success' => true,
            'message' => 'Draft order berhasil dibuat di Biteship.',
            'data' => [
                'biteship_draft_order_id' => $this->extractBiteshipOrderId($raw),
                'status' => $raw['status'] ?? null,
                'raw' => $raw,
            ],
        ];
    }

    /**
     * Mock create order untuk sandbox mode
     */
    private function mockCreateOrder(array $orderData)
    {
        // Simulasi delay scanning kurir
        sleep(2);

        // Data dummy kurir berdasarkan ekspedisi
        $courierData = $this->getDummyCourier($orderData['courier_code']);
        
        // Generate nomor resi sesuai format ekspedisi
        $waybillId = $this->generateWaybillNumber($orderData['courier_code']);

        // Generate label URL dari Biteship sandbox
        $labelUrl = "https://sandbox.biteship.com/label/{$waybillId}";

        return [
            'success' => true,
            'data' => [
                'id' => strtoupper(substr(uniqid(), -12)),
                'courier' => [
                    'waybill_id' => $waybillId,
                    'company' => $orderData['courier_code'],
                    'name' => $courierData['name'],
                    'phone' => $courierData['phone'],
                    'photo' => $courierData['photo'],
                    'rating' => $courierData['rating'],
                    'total_trips' => $courierData['total_trips'],
                    'vehicle_type' => $courierData['vehicle_type'],
                    'vehicle_number' => $courierData['vehicle_number'],
                ],
                'label_url' => $labelUrl,
                'status' => 'confirmed',
                'pickup_time' => now()->addMinutes(30)->format('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * Mock cancel order
     */
    private function mockCancelOrder(string $orderId, ?string $reason = null): array
    {
        sleep(1);

        return [
            'success' => true,
            'message' => 'Cancel order Biteship berhasil (MOCK).',
            'data' => [
                'id' => $orderId,
                'status' => 'cancelled',
                'cancelled_at' => now()->format('Y-m-d H:i:s'),
                'cancel_reason' => $reason ?? 'Dibatalkan oleh customer',
            ],
            'status' => 'cancelled',
        ];
    }

    /**
     * Generate nomor resi sesuai format ekspedisi
     */
    private function generateWaybillNumber(string $courierCode): string
    {
        switch ($courierCode) {
            case 'jnt':
                // Format J&T: JT + 12 digit angka
                // Contoh: JT012345678901
                return 'JT' . str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                
            case 'anteraja':
                // Format AnterAja: 10000 + 10 digit angka
                // Contoh: 100001234567890
                return '10000' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
                
            case 'paxel':
                // Format Paxel: PXL + 8 digit angka + 2 huruf
                // Contoh: PXL12345678AB
                $letters = chr(rand(65, 90)) . chr(rand(65, 90));
                return 'PXL' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT) . $letters;
                
            case 'gosend':
                // Format GoSend: GOSEND- + timestamp + 4 digit random
                // Contoh: GOSEND-17763116031234
                return 'GOSEND-' . time() . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                
            case 'grabexpress':
                // Format GrabExpress: GRAB + 12 digit angka
                // Contoh: GRAB123456789012
                return 'GRAB' . str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                
            default:
                return strtoupper($courierCode) . '-' . time();
        }
    }

    /**
     * Get dummy courier data
     */
    private function getDummyCourier(string $courierCode)
    {
        $couriers = [
            'jnt' => [
                [
                    'name' => 'Budi Santoso',
                    'phone' => '081234567890',
                    'photo' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=EF4444&color=fff&size=200',
                    'rating' => 4.8,
                    'total_trips' => 1250,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 1234 AB',
                ],
                [
                    'name' => 'Ahmad Rizki',
                    'phone' => '081234567891',
                    'photo' => 'https://ui-avatars.com/api/?name=Ahmad+Rizki&background=EF4444&color=fff&size=200',
                    'rating' => 4.9,
                    'total_trips' => 980,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 5678 CD',
                ],
            ],
            'anteraja' => [
                [
                    'name' => 'Dedi Kurniawan',
                    'phone' => '081234567892',
                    'photo' => 'https://ui-avatars.com/api/?name=Dedi+Kurniawan&background=3B82F6&color=fff&size=200',
                    'rating' => 4.7,
                    'total_trips' => 850,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 9012 EF',
                ],
                [
                    'name' => 'Eko Prasetyo',
                    'phone' => '081234567893',
                    'photo' => 'https://ui-avatars.com/api/?name=Eko+Prasetyo&background=3B82F6&color=fff&size=200',
                    'rating' => 4.9,
                    'total_trips' => 1100,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 3456 GH',
                ],
            ],
            'paxel' => [
                [
                    'name' => 'Fajar Ramadhan',
                    'phone' => '081234567894',
                    'photo' => 'https://ui-avatars.com/api/?name=Fajar+Ramadhan&background=10B981&color=fff&size=200',
                    'rating' => 4.8,
                    'total_trips' => 720,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 7890 IJ',
                ],
                [
                    'name' => 'Gilang Pratama',
                    'phone' => '081234567895',
                    'photo' => 'https://ui-avatars.com/api/?name=Gilang+Pratama&background=10B981&color=fff&size=200',
                    'rating' => 4.9,
                    'total_trips' => 950,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 2345 KL',
                ],
            ],
            'gosend' => [
                [
                    'name' => 'Hendra Wijaya',
                    'phone' => '081234567896',
                    'photo' => 'https://ui-avatars.com/api/?name=Hendra+Wijaya&background=00AA13&color=fff&size=200',
                    'rating' => 4.9,
                    'total_trips' => 1580,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 6789 MN',
                ],
                [
                    'name' => 'Irfan Hakim',
                    'phone' => '081234567897',
                    'photo' => 'https://ui-avatars.com/api/?name=Irfan+Hakim&background=00AA13&color=fff&size=200',
                    'rating' => 4.8,
                    'total_trips' => 1320,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 4321 OP',
                ],
            ],
            'grabexpress' => [
                [
                    'name' => 'Joko Susilo',
                    'phone' => '081234567898',
                    'photo' => 'https://ui-avatars.com/api/?name=Joko+Susilo&background=00B14F&color=fff&size=200',
                    'rating' => 4.9,
                    'total_trips' => 1450,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 8765 QR',
                ],
                [
                    'name' => 'Kurniawan Adi',
                    'phone' => '081234567899',
                    'photo' => 'https://ui-avatars.com/api/?name=Kurniawan+Adi&background=00B14F&color=fff&size=200',
                    'rating' => 4.8,
                    'total_trips' => 1290,
                    'vehicle_type' => 'Motor',
                    'vehicle_number' => 'L 5432 ST',
                ],
            ],
        ];

        $courierList = $couriers[$courierCode] ?? $couriers['jnt'];
        return $courierList[array_rand($courierList)];
    }

    /**
     * Print label/resi dari Biteship
     * Endpoint: GET /v1/orders/{id}/label
     */
    public function printLabel(string $biteshipOrderId): array
    {
        if ($this->sandbox) {
            return [
                'success' => false,
                'sandbox' => true,
                'message' => 'Label resi hanya tersedia di mode production. Set BITESHIP_SANDBOX=false di .env untuk menggunakan label resmi ekspedisi.',
            ];
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/orders/{$biteshipOrderId}/label");

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');

                if (str_contains($contentType, 'application/pdf') || str_contains($contentType, 'image/')) {
                    return [
                        'success' => true,
                        'content' => $response->body(),
                        'content_type' => $contentType,
                    ];
                }

                $data = $response->json();
                return [
                    'success' => true,
                    'url' => $data['url'] ?? $data['label_url'] ?? null,
                    'data' => $data,
                ];
            }

            Log::error('Biteship printLabel failed', [
                'order_id' => $biteshipOrderId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil label dari Biteship: ' . ($response->json()['error'] ?? $response->body()),
            ];
        } catch (\Exception $e) {
            Log::error('Biteship printLabel exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get order details from Biteship
     * 
     * @param string $orderId
     * @return array
     */
    public function getOrder(string $orderId)
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/orders/{$orderId}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Biteship getOrder failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get order',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Biteship getOrder exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel order in Biteship.
     *
     * Endpoint: POST /v1/orders/{id}/cancel
     * Status yang umumnya bisa dibatalkan: confirmed, allocated, picking_up.
     */
    public function cancelOrder(string $orderId, ?string $reason = null): array
    {
        if (trim($orderId) === '') {
            return [
                'success' => false,
                'message' => 'Biteship order ID kosong.',
            ];
        }

        // Mock mode
        if ($this->useMock) {
            return $this->mockCancelOrder($orderId, $reason);
        }

        $payload = [
            'reason' => trim((string) ($reason ?: 'Dibatalkan oleh customer')),
        ];

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/orders/{$orderId}/cancel", $payload);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'message' => 'Cancel order Biteship berhasil.',
                    'data' => $data,
                    'status' => $data['status'] ?? ($data['data']['status'] ?? null),
                ];
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['error']
                ?? $errorBody['message']
                ?? $response->body();

            Log::error('Biteship cancelOrder failed', [
                'order_id' => $orderId,
                'payload' => $payload,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membatalkan order di Biteship: ' . $errorMessage,
                'error' => $errorBody,
                'http_status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Biteship cancelOrder exception', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Request refund for shipping cost from Biteship.
     * 
     * Note: Biteship tidak memiliki endpoint refund khusus.
     * Refund ongkir biasanya otomatis diproses jika order dibatalkan sebelum pickup.
     * Method ini untuk tracking dan logging purposes.
     * 
     * @param string $orderId Biteship order ID
     * @param float $amount Jumlah ongkir yang akan di-refund
     * @param string $reason Alasan refund
     * @return array
     */
    public function refundShippingCost(string $orderId, float $amount, string $reason = ''): array
    {
        if (trim($orderId) === '') {
            return [
                'success' => false,
                'message' => 'Biteship order ID kosong.',
            ];
        }

        // Check order status first
        $orderDetail = $this->getOrder($orderId);
        
        if (!($orderDetail['success'] ?? false)) {
            return [
                'success' => false,
                'message' => 'Gagal mengambil detail order dari Biteship: ' . ($orderDetail['message'] ?? 'Unknown error'),
            ];
        }

        $data = $orderDetail['data'] ?? [];
        $status = strtolower((string) ($data['status'] ?? ''));
        $courier = $data['courier'] ?? [];
        $waybillId = $courier['waybill_id'] ?? null;

        Log::info('Biteship refund shipping cost check', [
            'order_id' => $orderId,
            'status' => $status,
            'waybill_id' => $waybillId,
            'amount' => $amount,
            'reason' => $reason,
        ]);

        // Statuses yang eligible untuk refund otomatis (belum pickup)
        $eligibleForAutoRefund = [
            'confirmed', 'allocated', 'pending', 'created',
        ];

        // Statuses yang sudah terlalu jauh (tidak bisa refund otomatis)
        $notEligibleStatuses = [
            'picked', 'picking_up', 'dropping_off', 'delivered', 'completed',
        ];

        if (in_array($status, $notEligibleStatuses, true)) {
            return [
                'success' => false,
                'message' => 'Order sudah dalam proses pengiriman, tidak dapat refund ongkir otomatis. Status: ' . $status,
                'requires_manual_refund' => true,
                'data' => [
                    'order_id' => $orderId,
                    'status' => $status,
                    'amount' => $amount,
                ],
            ];
        }

        if (in_array($status, $eligibleForAutoRefund, true)) {
            // Cancel order untuk trigger auto refund
            $cancelResult = $this->cancelOrder($orderId, $reason ?: 'Order cancelled - refund shipping cost');

            if ($cancelResult['success'] ?? false) {
                return [
                    'success' => true,
                    'message' => 'Order berhasil dibatalkan. Refund ongkir akan diproses otomatis oleh Biteship.',
                    'auto_refund' => true,
                    'data' => [
                        'order_id' => $orderId,
                        'cancelled_status' => $cancelResult['status'] ?? 'cancelled',
                        'amount' => $amount,
                        'refund_method' => 'auto',
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal membatalkan order untuk refund: ' . ($cancelResult['message'] ?? 'Unknown error'),
                'requires_manual_refund' => true,
            ];
        }

        // Status lainnya (cancelled, returned, dll)
        return [
            'success' => true,
            'message' => 'Order dengan status "' . $status . '" akan diproses refund manual oleh admin.',
            'requires_manual_refund' => true,
            'data' => [
                'order_id' => $orderId,
                'status' => $status,
                'amount' => $amount,
            ],
        ];
    }

    /**
     * Close/remove draft order in Biteship.
     *
     * Beberapa akun Biteship punya endpoint draft yang berbeda,
     * jadi method ini mencoba beberapa endpoint fallback.
     */
    public function closeDraftOrder(string $draftOrderId, ?string $reason = null): array
    {
        $draftOrderId = trim($draftOrderId);

        if ($draftOrderId === '') {
            return [
                'success' => false,
                'message' => 'Biteship draft order ID kosong.',
            ];
        }

        if ($this->useMock) {
            return [
                'success' => true,
                'message' => 'Draft order ditutup (mock).',
                'action' => 'mock_close_draft',
            ];
        }

        $payload = [
            'reason' => trim((string) ($reason ?: 'Draft ditutup otomatis karena payment sudah sukses.')),
        ];

        $attempts = [
            [
                'method' => 'post',
                'endpoint' => "/draft_orders/{$draftOrderId}/cancel",
                'payload' => $payload,
                'action' => 'draft_cancel',
            ],
            [
                'method' => 'post',
                'endpoint' => "/draft_orders/{$draftOrderId}/archive",
                'payload' => $payload,
                'action' => 'draft_archive',
            ],
            [
                'method' => 'delete',
                'endpoint' => "/draft_orders/{$draftOrderId}",
                'payload' => null,
                'action' => 'draft_delete',
            ],
            // Fallback terakhir: beberapa akun tetap menerima cancel lewat endpoint orders.
            [
                'method' => 'post',
                'endpoint' => "/orders/{$draftOrderId}/cancel",
                'payload' => $payload,
                'action' => 'order_cancel_fallback',
            ],
        ];

        $errors = [];

        foreach ($attempts as $attempt) {
            try {
                $request = Http::withoutVerifying()->withHeaders([
                    'Authorization' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ]);

                $method = strtolower((string) ($attempt['method'] ?? 'post'));
                $endpoint = (string) ($attempt['endpoint'] ?? '');
                $targetUrl = "{$this->baseUrl}{$endpoint}";

                if ($method === 'delete') {
                    $response = $request->delete($targetUrl);
                } else {
                    $response = $request->post($targetUrl, $attempt['payload'] ?? []);
                }

                if ($response->successful()) {
                    $data = $response->json();

                    Log::info('Biteship closeDraftOrder success', [
                        'draft_order_id' => $draftOrderId,
                        'action' => $attempt['action'] ?? null,
                        'endpoint' => $endpoint,
                        'status' => data_get($data, 'status'),
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Draft order Biteship berhasil ditutup.',
                        'action' => $attempt['action'] ?? null,
                        'data' => $data,
                    ];
                }

                $errors[] = [
                    'action' => $attempt['action'] ?? null,
                    'endpoint' => $endpoint,
                    'http_status' => $response->status(),
                    'body' => $response->body(),
                ];
            } catch (\Throwable $e) {
                $errors[] = [
                    'action' => $attempt['action'] ?? null,
                    'endpoint' => $attempt['endpoint'] ?? null,
                    'exception' => $e->getMessage(),
                ];
            }
        }

        Log::warning('Biteship closeDraftOrder failed on all endpoints', [
            'draft_order_id' => $draftOrderId,
            'attempts' => $errors,
        ]);

        return [
            'success' => false,
            'message' => 'Gagal menutup draft order Biteship di semua endpoint percobaan.',
            'errors' => $errors,
        ];
    }

    /**
     * Track order from Biteship
     * 
     * @param string $waybillId
     * @return array
     */
    public function trackOrder(string $waybillId)
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/trackings/{$waybillId}/couriers");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to track order',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create Biteship shipment from local order.
     */
    public function createShipmentFromOrder(Order $order, ?string $courierTypeOverride = null): array
    {
        if (!empty($order->biteship_order_id)) {
            return [
                'success' => true,
                'message' => 'Order sudah tersinkron Biteship.',
                'data' => [
                    'biteship_order_id' => $order->biteship_order_id,
                    'waybill_id' => $order->waybill_id,
                ],
            ];
        }

        $order->loadMissing('items.product');

        $items = $order->items->map(function ($item) {
            $weight = (int) (($item->product->weight ?? 500) * max(1, (int) $item->quantity));

            return [
                'name' => $item->product_name,
                'description' => $item->product_name,
                'value' => (int) $item->product_price,
                'quantity' => (int) $item->quantity,
                'weight' => max(1, $weight),
                'length' => 30,
                'width' => 25,
                'height' => 3,
            ];
        })->values()->toArray();

        if (empty($items)) {
            return [
                'success' => false,
                'message' => 'Order item kosong, tidak bisa create Biteship order.',
            ];
        }

        $customerOrderNote = trim((string) ($order->notes ?? ''));

        $shipmentPayload = [
            'shipper_contact_name' => config('branding.name', 'Hijab'),
            'shipper_contact_phone' => config('branding.phone', '081234567890'),
            'origin_contact_name' => config('branding.name', 'Hijab'),
            'origin_contact_phone' => config('branding.phone', '081234567890'),
            'origin_address' => config('branding.address', 'Toko Hijab'),
            'origin_note' => 'Pickup dari toko',
            'origin_latitude' => (float) config('biteship.origin.latitude'),
            'origin_longitude' => (float) config('biteship.origin.longitude'),
            'origin_postal_code' => config('biteship.origin.postal_code'),

            'destination_contact_name' => $order->shipping_name,
            'destination_contact_phone' => $order->shipping_phone,
            'destination_address' => $order->shipping_address,
            'destination_note' => $order->notes ?? '',
            'destination_latitude' => (float) $order->shipping_latitude,
            'destination_longitude' => (float) $order->shipping_longitude,
            'destination_postal_code' => $order->shipping_postal_code ?: config('biteship.origin.postal_code', '61219'),

            'courier_code' => strtolower((string) $order->courier_code),
            'courier_service_name' => (string) ($order->courier_service_name ?? ''),
            'courier_type' => $courierTypeOverride ? strtolower(trim($courierTypeOverride)) : null,
            'delivery_type' => 'now',
            'reference_id' => $order->order_number,
            'is_cod' => false,
            'payment_method' => 'online_payment',
            'total_amount' => (int) round((float) $order->total),
            'cash_on_delivery_amount' => 0,
            'items' => $items,
        ];

        if ($customerOrderNote !== '') {
            $shipmentPayload['order_note'] = $customerOrderNote;
        }

        $createResult = $this->createOrder($shipmentPayload);

        if (!($createResult['success'] ?? false)) {
            return $createResult;
        }

        $raw = $createResult['data'] ?? [];
        $courier = $raw['courier'] ?? [];

        return [
            'success' => true,
            'message' => 'Shipment berhasil dibuat di Biteship.',
            'data' => [
                'biteship_order_id' => $this->extractBiteshipOrderId($raw),
                'tracking_id' => $courier['tracking_id'] ?? null,
                'waybill_id' => $courier['waybill_id'] ?? null,
                'awb_number' => $courier['waybill_id'] ?? null,
                'courier_company' => $courier['company'] ?? null,
                'courier_type' => $courier['type'] ?? null,
                'status' => $raw['status'] ?? null,
                'tracking_link' => $courier['link'] ?? null,
                'label_url' => $raw['label_url'] ?? null,
                'pickup_time' => $raw['pickup_time'] ?? null,
                'raw' => $raw,
            ],
        ];
    }

    /**
     * Ambil ID order/draft dari berbagai kemungkinan key response Biteship.
     */
    private function extractBiteshipOrderId(array $payload): ?string
    {
        $id = data_get($payload, 'id')
            ?? data_get($payload, 'order_id')
            ?? data_get($payload, 'draft_order_id')
            ?? data_get($payload, 'data.id')
            ?? data_get($payload, 'data.order_id')
            ?? data_get($payload, 'data.draft_order_id');

        if ($id === null) {
            return null;
        }

        $id = trim((string) $id);

        return $id === '' ? null : $id;
    }

    /**
     * Build payment payload for Biteship order creation.
     *
     * Tujuan: memastikan dashboard Biteship dapat membedakan COD vs non-COD.
     */
    private function buildPaymentPayloadForBiteship(array $orderData): array
    {
        $isCod = filter_var(($orderData['is_cod'] ?? false), FILTER_VALIDATE_BOOLEAN);

        $paymentMethod = strtolower(trim((string) ($orderData['payment_method'] ?? '')));
        if (in_array($paymentMethod, ['cod', 'cash_on_delivery'], true)) {
            $isCod = true;
            $paymentMethod = 'cash_on_delivery';
        }

        if ($paymentMethod === '') {
            $paymentMethod = $isCod ? 'cash_on_delivery' : 'online_payment';
        }

        $payload = [
            'payment_method' => $paymentMethod,
            'is_cod' => $isCod,
        ];

        if ($paymentMethod === 'cash_on_delivery') {
            $codAmount = (int) round((float) (
                $orderData['cash_on_delivery_amount']
                ?? $orderData['total_amount']
                ?? 0
            ));

            if ($codAmount > 0) {
                $payload['cash_on_delivery'] = [
                    'amount' => $codAmount,
                ];
                $payload['cash_on_delivery_fee'] = 0;
            }
        }

        return $payload;
    }

    private function inferCourierType(string $courierCode, string $courierServiceName): string
    {
        $service = strtolower(trim($courierServiceName));
        $courierCode = strtolower(trim($courierCode));

        if ($service !== '') {
            if (preg_match('/^([a-z0-9_\-]+)/', $service, $matches)) {
                $token = strtolower($matches[1]);
                $map = [
                    'ez' => 'ez',
                    'reg' => 'reg',
                    'regular' => 'reg',
                    'reguler' => 'reg',
                    'yes' => 'yes',
                    'jtr' => 'jtr',
                    'oke' => 'oke',
                    'express' => 'express',
                    'instant' => 'instant',
                    'sameday' => 'same_day',
                    'same_day' => 'same_day',
                    'same-day' => 'same_day',
                ];

                if (isset($map[$token])) {
                    // J&T cenderung menggunakan ez untuk regular
                    if ($courierCode === 'jnt' && $map[$token] === 'reg') {
                        return 'ez';
                    }

                    return $map[$token];
                }
            }

            if ($courierCode === 'jnt' && (str_contains($service, 'reg') || str_contains($service, 'regular') || str_contains($service, 'reguler'))) {
                return 'ez';
            }

            if (str_contains($service, 'instant')) {
                return 'instant';
            }

            if (str_contains($service, 'same day') || str_contains($service, 'sameday')) {
                return 'same_day';
            }
        }

        return in_array($courierCode, ['gojek', 'gosend', 'grab', 'grabexpress'], true)
            ? 'instant'
            : 'reg';
    }
}
