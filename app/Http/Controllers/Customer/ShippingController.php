<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\BiteshipService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected $biteship;

    public function __construct(BiteshipService $biteship)
    {
        $this->biteship = $biteship;
    }

    /**
     * Get shipping rates from Biteship API
     */
    public function getRates(Request $request)
    {
        try {
            $request->validate([
                'destination_postal_code' => 'nullable|string',
                'destination_latitude' => 'nullable|numeric',
                'destination_longitude' => 'nullable|numeric',
            ]);

            $destinationLatitude = (float) ($request->destination_latitude ?? -7.278417);
            $destinationLongitude = (float) ($request->destination_longitude ?? 112.632583);
            $destinationPostalCode = $this->resolveDestinationPostalCode($request, $destinationLatitude, $destinationLongitude);

            if ($destinationPostalCode === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode pos tujuan tidak ditemukan. Coba pilih titik lokasi yang lebih akurat.',
                ], 400);
            }

            // Get cart items (support guest checkout)
            if (auth()->check()) {
                $cartItems = auth()->user()->cart()->with('product')->get();
            } else {
                // Guest cart from session
                $guestCart = session()->get('guest_cart', []);
                $cartItems = collect();
                
                foreach ($guestCart as $item) {
                    $product = \App\Models\Product::find($item['product_id']);
                    if ($product) {
                        $cartItems->push((object)[
                            'product' => $product,
                            'quantity' => $item['quantity'],
                        ]);
                    }
                }
            }

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang belanja kosong',
                ], 400);
            }

            $items = $cartItems->map(function ($cartItem) {
                return [
                    'name' => $cartItem->product->name,
                    'value' => (int) $cartItem->product->discounted_price,
                    'weight' => (int) ($cartItem->product->weight ?? 500),
                    'quantity' => $cartItem->quantity,
                ];
            })->toArray();

            // Kirim alias kode instant agar kompatibel lintas konfigurasi akun Biteship.
            $requestedCouriers = 'jnt,jne,anteraja,paxel,gojek,grab,gosend,grabexpress';

            $result = $this->biteship->getRates([
                'destination_postal_code' => $destinationPostalCode,
                'destination_latitude' => $destinationLatitude,
                'destination_longitude' => $destinationLongitude,
                'couriers' => $requestedCouriers,
                'strict_biteship' => true,
                'items' => $items,
            ]);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal mengambil data ongkir',
                ], 400);
            }

            $pricing = $result['data']['pricing'] ?? [];

            if (empty($pricing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada layanan ekspedisi aktif untuk rute ini di Biteship.',
                    'debug' => config('app.debug') ? [
                        'requested_couriers' => explode(',', $requestedCouriers),
                        'returned_couriers' => [],
                        'destination_postal_code' => $destinationPostalCode,
                    ] : null,
                ], 400);
            }

            // Process each rate langsung dari Biteship tanpa filter aturan lokal.
            $processedRates = [];
            foreach ($pricing as $rate) {
                $serviceType = (string) ($rate['service_type'] ?? 'regular');

                $processedRates[] = [
                    'courier_code' => $rate['courier_code'],
                    'courier_name' => $rate['courier_name'],
                    'courier_service_code' => $rate['courier_service_code'] ?? ($rate['courier_type'] ?? null),
                    'courier_type' => $rate['courier_type'] ?? ($rate['courier_service_code'] ?? null),
                    'courier_service_name' => $rate['courier_service_name'],
                    'service_type' => $serviceType,
                    'price' => (int) ($rate['price'] ?? 0),
                    'duration' => (string) ($rate['duration'] ?? ''),
                    'duration_minutes' => $rate['duration_minutes'] ?? 0,
                    'is_cheapest' => (bool) ($rate['is_cheapest'] ?? false),
                    'is_fastest' => (bool) ($rate['is_fastest'] ?? false),
                ];
            }

            [$processedRates, $hiddenByOperationalHours] = $this->filterRatesByOperationalHours($processedRates);

            if (empty($processedRates)) {
                $message = 'Semua layanan ekspedisi untuk rute ini sedang di luar jam operasional.';
                if (!empty($hiddenByOperationalHours)) {
                    $message .= ' Silakan pilih waktu lain atau layanan berbeda.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'debug' => config('app.debug') ? [
                        'hidden_by_operational_hours' => $hiddenByOperationalHours,
                    ] : null,
                ], 400);
            }

            // Sort by price (cheapest first), lalu durasi tercepat.
            usort($processedRates, function($a, $b) {
                if ($a['price'] === $b['price']) {
                    return ($a['duration_minutes'] ?? 0) <=> ($b['duration_minutes'] ?? 0);
                }
                return $a['price'] <=> $b['price'];
            });

            // Add labels visual sederhana untuk UI.
            if (!empty($processedRates)) {
                $cheapestPrice = min(array_column($processedRates, 'price'));
                $fastestMinutes = min(array_column($processedRates, 'duration_minutes'));
                
                foreach ($processedRates as $index => $rate) {
                    $processedRates[$index]['is_cheapest'] = ($rate['price'] === $cheapestPrice);
                    $processedRates[$index]['is_fastest'] = (($rate['duration_minutes'] ?? 0) === $fastestMinutes);
                    $processedRates[$index]['label'] = '';
                    
                    if ($rate['price'] === $cheapestPrice) {
                        $processedRates[$index]['label'] = '💸 Termurah';
                    }
                    if (($rate['duration_minutes'] ?? 0) === $fastestMinutes) {
                        $processedRates[$index]['label'] = '⚡ Tercepat';
                    }
                }
            }

            $returnedCouriers = array_values(array_unique(array_filter(array_map(
                fn ($rate) => strtolower(trim((string) ($rate['courier_code'] ?? ''))),
                $pricing
            ))));

            return response()->json([
                'success' => true,
                'rates' => $processedRates,
                'debug' => config('app.debug') ? [
                    'requested_couriers' => explode(',', $requestedCouriers),
                    'returned_couriers' => $returnedCouriers,
                    'destination_postal_code' => $destinationPostalCode,
                    'hidden_by_operational_hours' => $hiddenByOperationalHours,
                ] : null,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Shipping rates error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function resolveDestinationPostalCode(Request $request, float $lat, float $lng): ?string
    {
        $postalCode = trim((string) $request->input('destination_postal_code', ''));
        if (preg_match('/^\d{5}$/', $postalCode)) {
            return $postalCode;
        }

        $geo = $this->reverseGeocode($lat, $lng);
        $geoPostalCode = trim((string) ($geo['postal_code'] ?? ''));

        return preg_match('/^\d{5}$/', $geoPostalCode) ? $geoPostalCode : null;
    }

    private function reverseGeocode(float $lat, float $lng): array
    {
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&addressdetails=1";
            $ctx = stream_context_create(['http' => ['header' => 'User-Agent: Hijab/1.0']]);
            $json = @file_get_contents($url, false, $ctx);

            if (!$json) {
                return [];
            }

            $data = json_decode($json, true);
            $addr = $data['address'] ?? [];

            return [
                'postal_code' => $addr['postcode'] ?? null,
                'city' => $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['county'] ?? null,
                'province' => $addr['state'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Filter layanan yang berada di luar jam operasional.
     *
     * @param array<int, array<string, mixed>> $rates
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>}
     */
    private function filterRatesByOperationalHours(array $rates): array
    {
        $rules = (array) config('biteship.service_operational_hours', []);

        if (empty($rules) || empty($rates)) {
            return [$rates, []];
        }

        $now = Carbon::now('Asia/Jakarta');
        $kept = [];
        $hidden = [];

        foreach ($rates as $rate) {
            $courierCode = $this->normalizeCourierCode((string) ($rate['courier_code'] ?? ''));
            $serviceType = $this->normalizeServiceType((array) $rate);
            $ruleKey = $courierCode . ':' . $serviceType;
            $rule = (array) ($rules[$ruleKey] ?? []);

            if (empty($rule)) {
                $kept[] = $rate;
                continue;
            }

            $start = trim((string) ($rule['start'] ?? '00:00'));
            $end = trim((string) ($rule['end'] ?? '23:59'));

            $startTime = $this->parseOperationalTime($start);
            $endTime = $this->parseOperationalTime($end);

            // Jika format jam invalid di config, jangan hide layanan agar tidak memblokir checkout.
            if ($startTime === null || $endTime === null) {
                $kept[] = $rate;
                continue;
            }

            // Support window lintas tengah malam (contoh 20:00-02:00) bila nanti dibutuhkan.
            if ($endTime->lt($startTime)) {
                $isWithinHours = $now->gte($startTime) || $now->lte($endTime);
            } else {
                $isWithinHours = $now->betweenIncluded($startTime, $endTime);
            }

            if ($isWithinHours) {
                $kept[] = $rate;
                continue;
            }

            $hidden[] = [
                'courier_code' => $courierCode,
                'courier_service_name' => (string) ($rate['courier_service_name'] ?? ''),
                'service_type' => $serviceType,
                'window' => $start . '-' . $end,
                'current_time_wib' => $now->format('H:i'),
            ];
        }

        return [$kept, $hidden];
    }

    /**
     * Parse jam operasional konfigurasi (H:i) ke objek Carbon timezone WIB.
     */
    private function parseOperationalTime(string $time): ?Carbon
    {
        try {
            return Carbon::createFromFormat('H:i', $time, 'Asia/Jakarta');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Normalisasi variasi nama courier agar key config konsisten.
     */
    private function normalizeCourierCode(string $courierCode): string
    {
        $code = strtolower(trim($courierCode));

        return match ($code) {
            'go_send', 'go-send' => 'gosend',
            'go_jek', 'go-jek' => 'gojek',
            'grab_express', 'grab-express' => 'grabexpress',
            default => $code,
        };
    }

    /**
     * Normalisasi service type agar konsisten untuk pencocokan rule.
     */
    private function normalizeServiceType(array $rate): string
    {
        $candidates = [
            strtolower(trim((string) ($rate['service_type'] ?? ''))),
            strtolower(trim((string) ($rate['courier_type'] ?? ''))),
            strtolower(trim((string) ($rate['courier_service_code'] ?? ''))),
            strtolower(trim((string) ($rate['courier_service_name'] ?? ''))),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === '') {
                continue;
            }

            if (in_array($candidate, ['same day', 'same_day', 'sameday'], true)) {
                return 'same_day';
            }

            if (str_contains($candidate, 'same day') || str_contains($candidate, 'sameday')) {
                return 'same_day';
            }

            if ($candidate === 'instant' || str_contains($candidate, 'instant')) {
                return 'instant';
            }
        }

        return $candidates[0] !== '' ? $candidates[0] : 'regular';
    }
}
