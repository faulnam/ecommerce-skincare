<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BiteshipWebhookController extends Controller
{
    /**
     * Handle webhook callback dari Biteship.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Biteship webhook received', $payload);

        // Installation handshake dari Biteship bisa mengirim body kosong.
        // Harus tetap balas 200 OK agar endpoint lolos instalasi.
        if (empty($payload)) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Webhook endpoint is reachable.',
            ], 200);
        }

        $biteshipOrderId = (string) (
            data_get($payload, 'id')
            ?? data_get($payload, 'order_id')
            ?? data_get($payload, 'data.id')
            ?? data_get($payload, 'data.order_id')
            ?? ''
        );

        if ($biteshipOrderId === '') {
            Log::warning('Biteship webhook skipped: missing order id', ['payload' => $payload]);

            return response()->json([
                'status' => 'ok',
                'message' => 'Webhook diterima (order id belum tersedia pada payload).',
            ], 200);
        }

        $order = Order::whereBiteshipOrderId($biteshipOrderId)
            ->orWhere('biteship_draft_order_id', $biteshipOrderId)
            ->first();

        if (!$order) {
            Log::warning('Biteship webhook: local order not found', ['biteship_order_id' => $biteshipOrderId]);

            return response()->json([
                'status' => 'ok',
                'message' => 'Webhook diterima (order lokal belum ditemukan).',
            ], 200);
        }

        $trackingStatus = strtolower((string) (
            data_get($payload, 'courier_tracking_status')
            ?? data_get($payload, 'status')
            ?? data_get($payload, 'courier.status')
            ?? data_get($payload, 'data.courier_tracking_status')
            ?? data_get($payload, 'data.status')
            ?? ''
        ));

        $waybillId = data_get($payload, 'waybill_id')
            ?? data_get($payload, 'courier.waybill_id')
            ?? data_get($payload, 'data.waybill_id');

        $labelUrl = data_get($payload, 'label_url')
            ?? data_get($payload, 'data.label_url');

        $driverName = data_get($payload, 'driver_name')
            ?? data_get($payload, 'courier.name')
            ?? data_get($payload, 'data.driver_name');

        $driverPhone = data_get($payload, 'driver_phone')
            ?? data_get($payload, 'courier.phone')
            ?? data_get($payload, 'data.driver_phone');

        $updates = [];

        if ($trackingStatus !== '') {
            $updates['biteship_tracking_status'] = $trackingStatus;

            $shipmentStage = Order::normalizeBiteshipStage($trackingStatus);
            if (!empty($shipmentStage)) {
                $updates['biteship_status_stage'] = $shipmentStage;
            }

            $mappedOrderStatus = Order::mapBiteshipTrackingToOrderStatus($trackingStatus);
            if (!empty($mappedOrderStatus)) {
                $updates['status'] = $mappedOrderStatus;
            }
        }

        if (!empty($waybillId)) {
            $updates['waybill_id'] = $waybillId;
        }

        if (!empty($labelUrl)) {
            $updates['label_url'] = $labelUrl;
        }

        if (!empty($driverName)) {
            $updates['courier_driver_name'] = $driverName;
        }

        if (!empty($driverPhone)) {
            $updates['courier_driver_phone'] = $driverPhone;
        }

        if ($trackingStatus === 'picked') {
            $updates['picked_up_at'] = now();
        }

        if ($trackingStatus === 'dropping_off') {
            $updates['on_delivery_at'] = now();
        }

        if ($trackingStatus === 'delivered') {
            $updates['delivered_at'] = now();
        }

        if (in_array($trackingStatus, ['completed', 'done'], true)) {
            $updates['completed_at'] = now();
        }

        try {
            if (!empty($updates)) {
                $order->update($updates);
            }

            $matchedBy = $order->biteship_order_id === $biteshipOrderId
                ? 'biteship_order_id'
                : 'biteship_draft_order_id';

            Log::info('Biteship webhook processed', [
                'order_number' => $order->order_number,
                'biteship_order_id' => $biteshipOrderId,
                'matched_by' => $matchedBy,
                'tracking_status' => $trackingStatus,
                'updates' => $updates,
            ]);
        } catch (\Throwable $e) {
            Log::error('Biteship webhook processing failed', [
                'biteship_order_id' => $biteshipOrderId,
                'error' => $e->getMessage(),
            ]);

            // Tetap balas 200 agar webhook installation/delivery dari provider tidak gagal total.
            return response()->json([
                'status' => 'ok',
                'message' => 'Webhook diterima, proses internal gagal sementara.',
            ], 200);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Webhook Biteship diterima.',
        ], 200);
    }
}
