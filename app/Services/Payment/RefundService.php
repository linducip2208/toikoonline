<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gateway refunds. Only midtrans-snap is wired (server-key Basic Auth
 * cancel/refund endpoint). All other formats return unsupported instead
 * of failing silently — adapters are untouched (no interface change).
 */
class RefundService
{
    public function supportedFormats(): array
    {
        return ['midtrans-snap'];
    }

    public function isSupported(PaymentGatewayConfig $gateway): bool
    {
        return in_array($gateway->gateway_format, $this->supportedFormats(), true);
    }

    public function refund(PaymentTransaction $txn, ?int $amountMinor = null, string $reason = ''): array
    {
        $gateway = $txn->gateway;

        if (!$gateway) {
            return ['success' => false, 'message' => 'Gateway not found for transaction.'];
        }

        if (!$this->isSupported($gateway)) {
            return [
                'success' => false,
                'message' => "Refund unsupported for format {$gateway->gateway_format}. Supported: " . implode(', ', $this->supportedFormats()),
            ];
        }

        try {
            $base = rtrim($gateway->base_url ?: 'https://api.sandbox.midtrans.com/v2', '/');
            $body = array_filter([
                'refund_key' => 'refund-' . $txn->order_code . '-' . time(),
                'amount' => $amountMinor,
                'reason' => $reason ?: null,
            ]);

            $response = Http::withBasicAuth($gateway->server_key ?? '', '')
                ->timeout(30)
                ->post($base . '/' . $txn->order_code . '/refund', $body);

            if ($response->successful()) {
                $txn->update(['status' => 'refunded', 'raw' => $response->json()]);
                if ($txn->order && $txn->order->payment_status === 'paid') {
                    $txn->order->update(['payment_status' => 'refunded']);
                }

                return ['success' => true, 'data' => $response->json()];
            }

            Log::warning('Midtrans refund failed', ['order' => $txn->order_code, 'http' => $response->status()]);

            return ['success' => false, 'message' => 'Refund rejected by gateway (HTTP ' . $response->status() . ').'];
        } catch (\Exception $e) {
            Log::error('Midtrans refund exception', ['order' => $txn->order_code, 'error' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
