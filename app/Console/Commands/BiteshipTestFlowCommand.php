<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipTestFlowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'biteship:test-flow
        {--delivery-type=now : now or scheduled}
        {--schedule-at= : Schedule datetime (Y-m-d H:i:s) when delivery-type=scheduled}
        {--couriers=gojek,grab,jne,jnt : Couriers for rates check}
        {--courier-company=gojek : Courier company to use when creating order}
        {--courier-type=instant : Courier type/service to use when creating order}
        {--origin-postal= : Origin postal code (default from config biteship.origin.postal_code)}
        {--origin-address=Jl. Raya LUMINA No. 1, Surabaya : Origin address}
        {--origin-lat= : Origin latitude (default from config biteship.origin.latitude)}
        {--origin-lng= : Origin longitude (default from config biteship.origin.longitude)}
        {--destination-postal=40115 : Destination postal code}
        {--destination-address=Jl. Asia Afrika No. 1, Bandung : Destination address}
        {--destination-lat=-6.914744 : Destination latitude}
        {--destination-lng=107.609810 : Destination longitude}
        {--poll=6 : Number of GET order detail polls}
        {--interval=5 : Poll interval in seconds}
    {--dump-payload : Print create order payload before request}
        {--cancel : Try cancel order after create (only valid on confirmed/allocated/picking_up)}
        {--cancel-reason=QA test cancel order : Cancel reason payload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Biteship test flow: rates -> create order -> get detail polling -> optional cancel';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $apiKey = (string) config('biteship.api_key');
        $baseUrl = rtrim((string) config('biteship.base_url', 'https://api.biteship.com/v1'), '/');

        if ($apiKey === '' || str_contains($apiKey, 'your-biteship-api-key')) {
            $this->error('BITESHIP_API_KEY belum di-set. Isi dengan testing key: biteship_test.YOUR_TEST_API_KEY');
            return self::FAILURE;
        }

        if (!str_starts_with($apiKey, 'biteship_test.')) {
            $this->warn('API key bukan testing key (prefix biteship_test.). Lanjutkan dengan hati-hati.');
        }

        $deliveryType = strtolower((string) $this->option('delivery-type'));
        if (!in_array($deliveryType, ['now', 'scheduled'], true)) {
            $this->error('Option --delivery-type hanya boleh now atau scheduled.');
            return self::FAILURE;
        }

        $scheduleAt = null;
        if ($deliveryType === 'scheduled') {
            $scheduleOption = (string) $this->option('schedule-at');
            if ($scheduleOption === '') {
                $this->error('delivery-type=scheduled membutuhkan --schedule-at="Y-m-d H:i:s".');
                return self::FAILURE;
            }

            try {
                $scheduleAt = Carbon::parse($scheduleOption)->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                $this->error('Format --schedule-at tidak valid. Gunakan contoh: 2026-04-19 15:30:00');
                return self::FAILURE;
            }
        }

        $headers = [
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $originPostalCode = (string) ($this->option('origin-postal') !== null && $this->option('origin-postal') !== ''
            ? $this->option('origin-postal')
            : config('biteship.origin.postal_code'));

        $destinationPostalCode = (string) $this->option('destination-postal');

        if (!preg_match('/^\d{5}$/', $originPostalCode)) {
            $this->error("Origin postal code tidak valid: '{$originPostalCode}'. Gunakan format 5 digit.");
            return self::FAILURE;
        }

        if (!preg_match('/^\d{5}$/', $destinationPostalCode)) {
            $this->error("Destination postal code tidak valid: '{$destinationPostalCode}'. Gunakan format 5 digit.");
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('1) Checking rates...');
        $this->line("Using postal route: {$originPostalCode} -> {$destinationPostalCode}");

        $ratesPayload = [
            'origin_postal_code' => $originPostalCode,
            'destination_postal_code' => $destinationPostalCode,
            'couriers' => (string) $this->option('couriers'),
            'items' => [
                [
                    'name' => 'LUMINA LUMINA Test Item',
                    'description' => 'QA flow test',
                    'value' => 1500000,
                    'quantity' => 1,
                    'weight' => 1000,
                ],
            ],
        ];

        $ratesResponse = Http::withoutVerifying()
            ->withHeaders($headers)
            ->post("{$baseUrl}/rates/couriers", $ratesPayload);

        if (!$ratesResponse->successful()) {
            $this->error('Rates check gagal.');
            $this->line('HTTP: ' . $ratesResponse->status());
            $this->line($ratesResponse->body());
            return self::FAILURE;
        }

        $ratesData = $ratesResponse->json();
        $pricing = Arr::get($ratesData, 'pricing', []);
        $this->info('Rates check berhasil. Jumlah opsi: ' . count($pricing));

        if (empty($pricing)) {
            $this->error('Rates kosong, tidak bisa menentukan courier untuk create order.');
            return self::FAILURE;
        }

        $rows = [];
        foreach (array_slice($pricing, 0, 5) as $price) {
            $rows[] = [
                Arr::get($price, 'courier_name', Arr::get($price, 'courier_code', '-')),
                Arr::get($price, 'courier_service_name', '-'),
                (string) Arr::get($price, 'price', '-'),
                (string) Arr::get($price, 'duration', '-'),
            ];
        }

        if (!empty($rows)) {
            $this->table(['Courier', 'Service', 'Price', 'Duration'], $rows);
        }

        $requestedCompany = strtolower(trim((string) $this->option('courier-company')));
        $requestedType = $this->normalizeCourierType((string) $this->option('courier-type'));

        $availablePairs = collect($pricing)
            ->map(function (array $rate) {
                return [
                    'company' => strtolower((string) Arr::get($rate, 'courier_code', '')),
                    'type' => $this->extractCourierType($rate),
                    'service_name' => (string) Arr::get($rate, 'courier_service_name', ''),
                ];
            })
            ->filter(fn (array $pair) => $pair['company'] !== '' && $pair['type'] !== '')
            ->values();

        if ($availablePairs->isEmpty()) {
            $this->error('Gagal menurunkan pasangan courier_company/courier_type dari response rates.');
            $this->line('Pastikan response rates mengandung courier_code dan courier_service_code/name.');
            return self::FAILURE;
        }

        $selectedPair = $availablePairs->first(function (array $pair) use ($requestedCompany, $requestedType) {
            return $pair['company'] === $requestedCompany && $pair['type'] === $requestedType;
        });

        if (!$selectedPair) {
            $selectedPair = $availablePairs->first(function (array $pair) use ($requestedCompany) {
                return $pair['company'] === $requestedCompany;
            });
        }

        if (!$selectedPair) {
            $selectedPair = $availablePairs->first();
        }

        $selectedCompany = (string) $selectedPair['company'];
        $selectedType = (string) $selectedPair['type'];

        if ($selectedCompany !== $requestedCompany || $selectedType !== $requestedType) {
            $this->warn("Requested courier '{$requestedCompany}:{$requestedType}' tidak match rates; pakai '{$selectedCompany}:{$selectedType}' ({$selectedPair['service_name']}).");
        } else {
            $this->info("Using courier from rates: {$selectedCompany}:{$selectedType} ({$selectedPair['service_name']})");
        }

        $this->newLine();
        $this->info('2) Creating order...');

        $originLatitude = $this->option('origin-lat') !== null && $this->option('origin-lat') !== ''
            ? (float) $this->option('origin-lat')
            : (float) config('biteship.origin.latitude');

        $originLongitude = $this->option('origin-lng') !== null && $this->option('origin-lng') !== ''
            ? (float) $this->option('origin-lng')
            : (float) config('biteship.origin.longitude');

        $destinationLatitude = (float) $this->option('destination-lat');
        $destinationLongitude = (float) $this->option('destination-lng');

        $this->line("Origin coordinate: {$originLatitude}, {$originLongitude}");
        $this->line("Destination coordinate: {$destinationLatitude}, {$destinationLongitude}");

        $createPayload = [
            'shipper_contact_name' => 'LUMINA QA',
            'shipper_contact_phone' => '081234567890',
            'shipper_contact_email' => 'qa@skincare.test',

            'origin_contact_name' => 'LUMINA QA',
            'origin_contact_phone' => '081234567890',
            'origin_address' => (string) $this->option('origin-address'),
            'origin_postal_code' => $originPostalCode,
            // Keep legacy keys for compatibility
            'origin_latitude' => $originLatitude,
            'origin_longitude' => $originLongitude,
            // Biteship courier validation may require these coordinate keys
            'origin_coordinate_latitude' => $originLatitude,
            'origin_coordinate_longitude' => $originLongitude,
            'origin_coordinate' => [
                'latitude' => $originLatitude,
                'longitude' => $originLongitude,
            ],

            'destination_contact_name' => 'Customer QA',
            'destination_contact_phone' => '081298765432',
            'destination_contact_email' => 'customer.qa@example.com',
            'destination_address' => (string) $this->option('destination-address'),
            'destination_postal_code' => $destinationPostalCode,
            // Keep legacy keys for compatibility
            'destination_latitude' => $destinationLatitude,
            'destination_longitude' => $destinationLongitude,
            // Some couriers require these explicit destination coordinate keys
            'destination_coordinate_latitude' => $destinationLatitude,
            'destination_coordinate_longitude' => $destinationLongitude,
            'destination_coordinate' => [
                'latitude' => $destinationLatitude,
                'longitude' => $destinationLongitude,
            ],

            'courier_company' => $selectedCompany,
            'courier_type' => $selectedType,
            'delivery_type' => $deliveryType,

            'items' => [
                [
                    'name' => 'LUMINA LUMINA Test Item',
                    'description' => 'QA flow test',
                    'value' => 1500000,
                    'quantity' => 1,
                    'weight' => 1000,
                    'length' => 30,
                    'width' => 25,
                    'height' => 3,
                ],
            ],
            'note' => 'Automated QA flow test from artisan command',
        ];

        if ($scheduleAt !== null) {
            $createPayload['delivery_datetime'] = Carbon::parse($scheduleAt)
                ->setTimezone('Asia/Jakarta')
                ->format('Y-m-d\TH:i:sP');
        }

        Log::info('Biteship order payload', $createPayload);

        if ((bool) $this->option('dump-payload')) {
            $this->line(json_encode($createPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $createResponse = Http::withoutVerifying()
            ->withHeaders($headers)
            ->post("{$baseUrl}/orders", $createPayload);

        if (!$createResponse->successful()) {
            $this->error('Create order gagal.');
            $this->line('HTTP: ' . $createResponse->status());
            $this->line($createResponse->body());
            return self::FAILURE;
        }

        $createData = $createResponse->json();
        $orderId = (string) (
            Arr::get($createData, 'id')
            ?? Arr::get($createData, 'order_id')
            ?? Arr::get($createData, 'data.id')
            ?? Arr::get($createData, 'data.order_id')
        );

        if ($orderId === '') {
            $this->error('Create order sukses tapi order_id tidak ditemukan pada response.');
            $this->line(json_encode($createData, JSON_PRETTY_PRINT));
            return self::FAILURE;
        }

        $status = (string) (
            Arr::get($createData, 'status')
            ?? Arr::get($createData, 'data.status')
            ?? 'unknown'
        );

        $this->info("Order created: {$orderId}");
        $this->line("Initial status: {$status}");

        $this->newLine();
        $this->info('3) Polling order detail...');

        $pollCount = max(1, (int) $this->option('poll'));
        $interval = max(1, (int) $this->option('interval'));
        $history = [];

        for ($i = 1; $i <= $pollCount; $i++) {
            $detailResponse = Http::withoutVerifying()
                ->withHeaders($headers)
                ->get("{$baseUrl}/orders/{$orderId}");

            if (!$detailResponse->successful()) {
                $this->warn("Poll #{$i} gagal (HTTP {$detailResponse->status()})");
                $history[] = ['poll' => $i, 'status' => 'request_failed'];
            } else {
                $detailData = $detailResponse->json();
                $pollStatus = (string) (
                    Arr::get($detailData, 'status')
                    ?? Arr::get($detailData, 'data.status')
                    ?? 'unknown'
                );

                $history[] = ['poll' => $i, 'status' => $pollStatus];
                $this->line("Poll #{$i}: {$pollStatus}");

                if ($i < $pollCount) {
                    sleep($interval);
                }
            }
        }

        $this->table(['Poll', 'Status'], array_map(fn ($row) => [$row['poll'], $row['status']], $history));

        if ((bool) $this->option('cancel')) {
            $this->newLine();
            $this->info('4) Attempting cancel order...');

            $latestStatus = (string) (collect($history)->last()['status'] ?? $status);
            $allowed = ['confirmed', 'allocated', 'picking_up'];

            if (!in_array($latestStatus, $allowed, true)) {
                $this->warn("Skip cancel: status saat ini '{$latestStatus}' tidak termasuk status yang bisa dibatalkan.");
            } else {
                $cancelPayload = ['reason' => (string) $this->option('cancel-reason')];

                $cancelResponse = Http::withoutVerifying()
                    ->withHeaders($headers)
                    ->post("{$baseUrl}/orders/{$orderId}/cancel", $cancelPayload);

                if ($cancelResponse->successful()) {
                    $cancelData = $cancelResponse->json();
                    $cancelStatus = (string) (
                        Arr::get($cancelData, 'status')
                        ?? Arr::get($cancelData, 'data.status')
                        ?? 'cancel_requested'
                    );

                    $this->info("Cancel response sukses. Status: {$cancelStatus}");
                } else {
                    $this->warn('Cancel request gagal.');
                    $this->line('HTTP: ' . $cancelResponse->status());
                    $this->line($cancelResponse->body());
                }
            }
        }

        $this->newLine();
        $this->info('Flow selesai.');
        $this->line('- on_hold > 14 hari: auto-cancel oleh Biteship admin');
        $this->line('- rejected: item gagal dikembalikan ke pengirim');
        $this->line('- disposed: endpoint terminal setelah disposal request');

        return self::SUCCESS;
    }

    private function extractCourierType(array $rate): string
    {
        $direct = Arr::get($rate, 'courier_type')
            ?? Arr::get($rate, 'courier_service_code')
            ?? Arr::get($rate, 'service_code');

        if (is_string($direct) && trim($direct) !== '') {
            return $this->normalizeCourierType($direct);
        }

        $serviceName = (string) Arr::get($rate, 'courier_service_name', '');
        return $this->normalizeCourierType($serviceName);
    }

    private function normalizeCourierType(string $value): string
    {
        $value = strtolower(trim($value));

        if ($value === '') {
            return '';
        }

        if (preg_match('/\(([^)]+)\)/', $value, $matches)) {
            $insideParen = strtolower(trim($matches[1]));
            if ($insideParen !== '') {
                $value = $insideParen;
            }
        }

        $aliases = [
            'reguler' => 'reg',
            'regular' => 'reg',
            'reg' => 'reg',
            'yes' => 'yes',
            'ez' => 'ez',
            'instant' => 'instant',
            'same day' => 'same_day',
            'sameday' => 'same_day',
            'same_day' => 'same_day',
            'trucking' => 'trucking',
            'jne trucking' => 'trucking',
        ];

        if (isset($aliases[$value])) {
            return $aliases[$value];
        }

        $value = preg_replace('/[^a-z0-9]+/', '_', $value) ?? $value;
        return trim($value, '_');
    }
}
