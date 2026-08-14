<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\BiteshipService;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderObserver
{
    protected WebPushService $webPush;
    protected BiteshipService $biteship;

    public function __construct(WebPushService $webPush, BiteshipService $biteship)
    {
        $this->webPush = $webPush;
        $this->biteship = $biteship;
    }

    /**
     * Handle the Order "created" event.
     * Send push notification to all admins
     */
    public function created(Order $order): void
    {
        try {
            // Send push to all admins
            $this->webPush->sendToAdmins(
                '🛒 Pesanan Baru!',
                "Pesanan #{$order->order_number} dari {$order->user->name} - {$order->formatted_total}",
                route('admin.orders.show', $order),
                'new_order'
            );

            Log::info("Push notification sent for new order #{$order->order_number}");
        } catch (\Exception $e) {
            Log::error("Failed to send push for new order: " . $e->getMessage());
        }
    }

    /**
     * Handle the Order "updated" event.
     * Send push notification based on status change
     */
    public function updated(Order $order): void
    {
        $statusChanged = $order->wasChanged('status');
        $paymentBecamePaid = $order->wasChanged('payment_status')
            && $order->payment_status === Order::PAYMENT_PAID;

        $statusMovedToProcessingWhilePaid = $statusChanged
            && $order->status === Order::STATUS_PROCESSING
            && $order->payment_status === Order::PAYMENT_PAID;

        if ($paymentBecamePaid || $statusMovedToProcessingWhilePaid) {
            $this->syncBiteshipAfterPayment($order);
        }

        if (!$statusChanged) {
            return;
        }

        $oldStatus = $order->getOriginal('status');
        $newStatus = $order->status;

        try {
            // Notify customer about status change
            $this->notifyCustomerStatusChange($order, $oldStatus, $newStatus);

            // Notify courier if assigned
            if ($order->wasChanged('courier_id') && $order->courier_id) {
                $this->notifyCourierAssigned($order);
            }

        } catch (\Exception $e) {
            Log::error("Failed to send push for order update: " . $e->getMessage());
        }
    }

    protected function syncBiteshipAfterPayment(Order $order): void
    {
        if (empty($order->courier_code) || !empty($order->biteship_order_id)) {
            return;
        }

        try {
            $courierServiceCode = $this->extractCourierServiceCodeFromNotes((string) $order->delivery_notes);

            $result = $this->biteship->createShipmentFromOrder($order, $courierServiceCode);

            if (!($result['success'] ?? false)) {
                $errorMessage = (string) ($result['message'] ?? 'Unknown error');
                $order->fill([
                    'delivery_notes' => $this->appendDeliveryNote((string) $order->delivery_notes, 'biteship_sync_status=failed_to_sync_biteship; reason=' . $errorMessage),
                ])->saveQuietly();

                Log::warning('Create Biteship shipment gagal saat payment sukses', [
                    'order_number' => $order->order_number,
                    'message' => $errorMessage,
                ]);

                return;
            }

            $data = $result['data'] ?? [];
            $draftOrderId = (string) ($order->biteship_draft_order_id ?? '');
            $draftCleanupStatus = 'not_found';

            if ($draftOrderId !== '') {
                $draftCleanup = $this->biteship->closeDraftOrder(
                    $draftOrderId,
                    'Draft ditutup otomatis karena payment sudah sukses dan shipment final sudah dibuat.'
                );

                $draftCleanupStatus = ($draftCleanup['success'] ?? false) ? 'success' : 'failed';
            }

            $payload = array_filter([
                'biteship_order_id' => $data['biteship_order_id'] ?? null,
                'waybill_id' => $data['waybill_id'] ?? null,
                'label_url' => $data['label_url'] ?? null,
                'pickup_time' => $data['pickup_time'] ?? null,
                'delivery_notes' => $this->appendDeliveryNote(
                    (string) $order->delivery_notes,
                    'biteship_sync_status=synced' . (!empty($order->biteship_draft_order_id) ? '; source=draft_order; draft_id=' . $order->biteship_draft_order_id : '')
                ),
            ], fn ($value) => $value !== null && $value !== '');

            if (Schema::hasColumn('orders', 'awb_number')) {
                $payload['awb_number'] = $data['awb_number'] ?? ($data['waybill_id'] ?? null);
            }

            $payload['delivery_notes'] = $this->appendDeliveryNote(
                (string) ($payload['delivery_notes'] ?? $order->delivery_notes),
                'biteship_draft_cleanup=' . $draftCleanupStatus
            );

            if ($draftCleanupStatus === 'success') {
                $payload['biteship_draft_order_id'] = null;
            }

            if (!empty($payload)) {
                $order->fill($payload)->saveQuietly();
            }

            Log::info('Create Biteship shipment sukses saat payment sukses', [
                'order_number' => $order->order_number,
                'biteship_draft_order_id' => $order->biteship_draft_order_id,
                'biteship_draft_cleanup' => $draftCleanupStatus,
                'biteship_order_id' => $data['biteship_order_id'] ?? null,
                'waybill_id' => $data['waybill_id'] ?? null,
            ]);
        } catch (\Throwable $e) {
            $order->fill([
                'delivery_notes' => $this->appendDeliveryNote((string) $order->delivery_notes, 'biteship_sync_status=failed_to_sync_biteship; reason=' . $e->getMessage()),
            ])->saveQuietly();

            Log::error('Create Biteship shipment exception saat payment sukses', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function extractCourierServiceCodeFromNotes(string $notes): ?string
    {
        if (preg_match('/biteship_courier_service_code=([a-z0-9_\-]+)/i', $notes, $matches)) {
            return strtolower(trim((string) ($matches[1] ?? '')));
        }

        return null;
    }

    protected function appendDeliveryNote(string $existing, string $line): string
    {
        $existing = trim($existing);
        $line = trim($line);

        if ($line === '') {
            return $existing;
        }

        if ($existing === '') {
            return $line;
        }

        return $existing . "\n" . $line;
    }

    /**
     * Notify customer about order status change
     */
    protected function notifyCustomerStatusChange(Order $order, string $oldStatus, string $newStatus): void
    {
        $customer = $order->user;
        if (!$customer) return;

        $statusMessages = [
            'confirmed' => [
                'title' => '✅ Pesanan Dikonfirmasi',
                'message' => "Pesanan #{$order->order_number} telah dikonfirmasi dan sedang diproses.",
            ],
            'processing' => [
                'title' => '👨‍🍳 Pesanan Diproses',
                'message' => "Pesanan #{$order->order_number} sedang dalam proses pembuatan.",
            ],
            'ready' => [
                'title' => '📦 Pesanan Siap',
                'message' => "Pesanan #{$order->order_number} sudah siap dan akan segera dikirim.",
            ],
            'shipped' => [
                'title' => '🚚 Pesanan Dikirim',
                'message' => "Pesanan #{$order->order_number} sedang dalam perjalanan ke alamat Anda.",
            ],
            'delivered' => [
                'title' => '🎉 Pesanan Tiba',
                'message' => "Pesanan #{$order->order_number} telah sampai. Terima kasih telah berbelanja!",
            ],
            'cancelled' => [
                'title' => '❌ Pesanan Dibatalkan',
                'message' => "Pesanan #{$order->order_number} telah dibatalkan.",
            ],
        ];

        if (isset($statusMessages[$newStatus])) {
            $msg = $statusMessages[$newStatus];
            $this->webPush->sendToCustomer(
                $customer,
                $msg['title'],
                $msg['message'],
                route('customer.orders.show', $order),
                'status_changed'
            );
        }
    }

    /**
     * Notify courier when assigned to order
     */
    protected function notifyCourierAssigned(Order $order): void
    {
        $courier = $order->courier;
        if (!$courier) return;

        $this->webPush->sendToCourier(
            $courier,
            '🛵 Tugas Pengiriman Baru',
            "Anda ditugaskan mengirim pesanan #{$order->order_number} ke {$order->delivery_address}",
            route('courier.deliveries.show', $order)
        );
    }
}
