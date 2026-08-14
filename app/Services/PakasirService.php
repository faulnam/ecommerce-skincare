<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PakasirService
{
    protected string $baseUrl;
    protected string $slug;
    protected string $apiKey;
    protected bool $sandbox;

    public function __construct()
    {
        $this->baseUrl = config('services.pakasir.base_url', 'https://app.pakasir.com');
        $this->slug = (string) config('services.pakasir.slug', '');
        $this->apiKey = (string) config('services.pakasir.api_key', '');
        $this->sandbox = config('services.pakasir.sandbox', true);
    }

    /**
     * Create a new transaction
     * 
     * @param string $orderId
     * @param int $amount
     * @param string $method (qris, bni_va, bri_va, cimb_niaga_va, etc)
     * @return array|null
     */
    public function createTransaction(string $orderId, int $amount, string $method = 'qris'): ?array
    {
        try {
            Log::info('Pakasir creating transaction', [
                'url' => "{$this->baseUrl}/api/transactioncreate/{$method}",
                'project' => $this->slug,
                'order_id' => $orderId,
                'amount' => $amount,
                'method' => $method,
            ]);

            $response = Http::withoutVerifying()->asJson()->post("{$this->baseUrl}/api/transactioncreate/{$method}", [
                'project' => $this->slug,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $this->apiKey,
            ]);

            Log::info('Pakasir response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Pakasir transaction created', [
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'method' => $method,
                    'response' => $data
                ]);
                return $data['payment'] ?? null;
            }

            Log::error('Pakasir create transaction failed', [
                'order_id' => $orderId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Pakasir create transaction error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get payment URL for redirect-based payment
     * 
     * @param string $orderId
     * @param int $amount
     * @param string|null $redirectUrl
     * @param bool $qrisOnly
     * @return string
     */
    public function getPaymentUrl(string $orderId, int $amount, ?string $redirectUrl = null, bool $qrisOnly = false): string
    {
        $url = "{$this->baseUrl}/pay/{$this->slug}/{$amount}?order_id={$orderId}";

        if ($redirectUrl) {
            $url .= "&redirect=" . urlencode($redirectUrl);
        }

        if ($qrisOnly) {
            $url .= "&qris_only=1";
        }

        return $url;
    }

    /**
     * Get transaction detail/status
     * 
     * @param string $orderId
     * @param int $amount
     * @return array|null
     */
    public function getTransactionDetail(string $orderId, int $amount): ?array
    {
        try {
            $response = Http::withoutVerifying()->get("{$this->baseUrl}/api/transactiondetail", [
                'project' => $this->slug,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json()['transaction'] ?? null;
            }

            Log::error('Pakasir get transaction detail failed', [
                'order_id' => $orderId,
                'response' => $response->body()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Pakasir get transaction detail error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Cancel a transaction
     * 
     * @param string $orderId
     * @param int $amount
     * @return bool
     */
    public function cancelTransaction(string $orderId, int $amount): bool
    {
        try {
            $response = Http::withoutVerifying()->asJson()->post("{$this->baseUrl}/api/transactioncancel", [
                'project' => $this->slug,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                Log::info('Pakasir transaction cancelled', [
                    'order_id' => $orderId,
                    'amount' => $amount
                ]);
                return true;
            }

            Log::error('Pakasir cancel transaction failed', [
                'order_id' => $orderId,
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Pakasir cancel transaction error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Simulate payment (sandbox mode only)
     * 
     * @param string $orderId
     * @param int $amount
     * @return bool
     */
    public function simulatePayment(string $orderId, int $amount): bool
    {
        if (!$this->sandbox) {
            Log::warning('Payment simulation attempted in production mode');
            return false;
        }

        try {
            $response = Http::withoutVerifying()->asJson()->post("{$this->baseUrl}/api/paymentsimulation", [
                'project' => $this->slug,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                Log::info('Pakasir payment simulated', [
                    'order_id' => $orderId,
                    'amount' => $amount
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Pakasir payment simulation error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get available payment methods
     * 
     * @return array
     */
    public function getPaymentMethods(): array
    {
        return [
            'qris' => [
                'name' => 'QRIS',
                'description' => 'Scan QR code dengan aplikasi e-wallet',
                'icon' => 'fas fa-qrcode',
            ],
            'bni_va' => [
                'name' => 'BNI Virtual Account',
                'description' => 'Transfer ke nomor Virtual Account BNI',
                'icon' => 'fas fa-university',
            ],
            'bri_va' => [
                'name' => 'BRI Virtual Account',
                'description' => 'Transfer ke nomor Virtual Account BRI',
                'icon' => 'fas fa-university',
            ],
            'cimb_niaga_va' => [
                'name' => 'CIMB Niaga Virtual Account',
                'description' => 'Transfer ke nomor Virtual Account CIMB Niaga',
                'icon' => 'fas fa-university',
            ],
            'permata_va' => [
                'name' => 'Permata Virtual Account',
                'description' => 'Transfer ke nomor Virtual Account Permata',
                'icon' => 'fas fa-university',
            ],
            'maybank_va' => [
                'name' => 'Maybank Virtual Account',
                'description' => 'Transfer ke nomor Virtual Account Maybank',
                'icon' => 'fas fa-university',
            ],
        ];
    }

    /**
     * Verify webhook signature/data
     * 
     * @param array $data
     * @return bool
     */
    public function verifyWebhook(array $data): bool
    {
        // Verify project matches
        if (($data['project'] ?? '') !== $this->slug) {
            Log::warning('Pakasir webhook project mismatch', $data);
            return false;
        }

        // Verify status is completed
        if (($data['status'] ?? '') !== 'completed') {
            Log::info('Pakasir webhook status not completed', $data);
            return false;
        }

        return true;
    }
}
