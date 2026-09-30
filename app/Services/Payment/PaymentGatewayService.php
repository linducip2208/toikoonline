<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected array $adapters = [];

    public function getAdapter(PaymentGatewayConfig $gateway): ?PaymentAdapterInterface
    {
        $cacheKey = $gateway->id;

        if (isset($this->adapters[$cacheKey])) {
            return $this->adapters[$cacheKey];
        }

        $adapter = match ($gateway->gateway_format) {
            'midtrans-snap' => new SnapRedirectAdapter($gateway),
            'midtrans-core' => new CoreApiAdapter($gateway),
            'xendit-invoice' => new XenditInvoiceAdapter($gateway),
            'tripay-closed' => new TripayClosedAdapter($gateway),
            'duitku-redirect' => new GenericRedirectAdapter($gateway),
            'oyindonesia-api',
            'ipaymu-api',
            'faspay-api',
            'doku-api',
            'esiapay-api' => new GenericApiAdapter($gateway),
            default => null,
        };

        if ($adapter) {
            $this->adapters[$cacheKey] = $adapter;
        }

        return $adapter;
    }

    public function createPayment(PaymentGatewayConfig $gateway, array $payload): array
    {
        $adapter = $this->getAdapter($gateway);

        if (!$adapter) {
            Log::warning('Payment gateway: unsupported format', [
                'gateway_id' => $gateway->id,
                'format' => $gateway->gateway_format,
            ]);
            return ['success' => false, 'message' => "Format {$gateway->gateway_format} tidak didukung."];
        }

        return $adapter->createTransaction($payload);
    }

    public function getActiveGateways(): array
    {
        return PaymentGatewayConfig::active()->orderBy('sort_order')->get()->all();
    }

    public function getChannelsForGateway(PaymentGatewayConfig $gateway): array
    {
        $adapter = $this->getAdapter($gateway);
        return $adapter?->getChannels() ?? [];
    }

    public function verifyCallback(PaymentGatewayConfig $gateway, array $data): bool
    {
        $adapter = $this->getAdapter($gateway);
        return $adapter?->verifyCallback($data) ?? false;
    }

    public function getTransactionStatus(PaymentGatewayConfig $gateway, string $transactionId): array
    {
        $adapter = $this->getAdapter($gateway);
        return $adapter?->getTransactionStatus($transactionId) ?? ['success' => false, 'message' => 'Format tidak didukung'];
    }

    /**
     * Idempotent payment intent. Safe to call repeatedly for the same order:
     * returns the existing payment_transactions row when idempotency_key
     * (order code) already exists instead of charging twice.
     *
     * Money is integer IDR. $amountMinor must already be in IDR minor units.
     */
    public function createIntent(PaymentGatewayConfig $gateway, int|string $orderId, int $amountMinor, array $extraPayload = []): array
    {
        $order = $orderId instanceof Order ? $orderId : Order::where('id', $orderId)->orWhere('code', $orderId)->first();

        if (!$order) {
            return ['success' => false, 'message' => 'Order not found.'];
        }

        $idempotencyKey = (string) $order->code;

        $existing = PaymentTransaction::where('idempotency_key', $idempotencyKey)->first();
        if ($existing && in_array($existing->status, ['intent', 'pending', 'paid'], true)) {
            return ['success' => true, 'idempotent_replay' => true, 'transaction' => $existing, 'redirect_url' => $existing->redirect_url];
        }

        $txn = PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway_id' => $gateway->id,
            'order_code' => (string) $order->code,
            'amount' => (int) $amountMinor,
            'currency' => 'IDR',
            'status' => 'intent',
            'idempotency_key' => $idempotencyKey,
        ]);

        $payload = array_merge([
            'order_id' => (string) $order->code,
            'order_code' => (string) $order->code,
            'amount' => (int) $amountMinor,
            'gross_amount' => (int) $amountMinor,
            'customer' => [
                'name' => $order->user?->name,
                'email' => $order->user?->email,
            ],
        ], $extraPayload);

        $result = $this->createPayment($gateway, $payload);

        $txn->update([
            'status' => ($result['success'] ?? false) ? 'pending' : 'failed',
            'gateway_reference' => $result['token'] ?? $result['transaction_id'] ?? null,
            'redirect_url' => $result['redirect_url'] ?? null,
            'raw' => $result['raw'] ?? null,
        ]);

        $result['transaction'] = $txn->fresh();

        return $result;
    }

    /**
     * Pull gateway status for one transaction row and sync order + row.
     * Used by payments:reconcile. Never throws.
     */
    public function reconcileTransaction(PaymentTransaction $txn): array
    {
        try {
            $gateway = $txn->gateway;
            if (!$gateway || !$gateway->is_active) {
                return ['success' => false, 'message' => 'Gateway inactive'];
            }

            $ref = $txn->gateway_reference ?: $txn->order_code;
            $status = $this->getTransactionStatus($gateway, (string) $ref);
            if (!($status['success'] ?? false)) {
                return ['success' => false, 'message' => $status['message'] ?? 'Gateway lookup failed'];
            }

            $data = $status['data'] ?? [];
            $rawStatus = strtolower((string) ($data['transaction_status'] ?? $data['status'] ?? ''));
            $paidHints = ['capture', 'settlement', 'success', 'paid', 'completed', 'paid_off'];
            $mapped = in_array($rawStatus, $paidHints, true) ? 'paid'
                : (in_array($rawStatus, ['refund', 'partial_refund', 'refunded'], true) ? 'refunded' : 'pending');

            if ($mapped === 'paid') {
                $txn->update(['status' => 'paid', 'raw' => $data]);
                $order = $txn->order;
                if ($order && $order->payment_status !== 'paid') {
                    $order->update(['payment_status' => 'paid']);
                }
            } elseif ($mapped === 'refunded') {
                $txn->update(['status' => 'refunded', 'raw' => $data]);
            }

            return ['success' => true, 'mapped' => $mapped];
        } catch (\Exception $e) {
            Log::warning('Payment reconcile failed', ['txn' => $txn->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
