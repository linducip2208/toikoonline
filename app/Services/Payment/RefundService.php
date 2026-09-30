<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gateway refunds. Wired formats: midtrans-snap (server-key Basic Auth
 * cancel/refund endpoint), stripe-pi (refunds API), paypal-order
 * (captured-payment refund lookup + refund). All other formats return
 * unsupported instead of failing silently.
 */
class RefundService
{
    public function supportedFormats(): array
    {
        return ['midtrans-snap', 'stripe-pi', 'paypal-order'];
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

        if ($gateway->gateway_format === 'stripe-pi') {
            return $this->refundStripe($txn, $amountMinor, $reason);
        }

        if ($gateway->gateway_format === 'paypal-order') {
            return $this->refundPayPal($txn, $amountMinor, $reason);
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
                $this->emitRefunded($txn, $amountMinor);

                return ['success' => true, 'data' => $response->json()];
            }

            Log::warning('Midtrans refund failed', ['order' => $txn->order_code, 'http' => $response->status()]);

            return ['success' => false, 'message' => 'Refund rejected by gateway (HTTP ' . $response->status() . ').'];
        } catch (\Exception $e) {
            Log::error('Midtrans refund exception', ['order' => $txn->order_code, 'error' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function refundStripe(PaymentTransaction $txn, ?int $amountMinor, string $reason): array
    {
        try {
            $adapter = new StripePaymentIntentAdapter($txn->gateway);
            $body = array_filter([
                'payment_intent' => $txn->gateway_reference,
                'amount' => $amountMinor,
                'reason' => $reason ?: null,
                'metadata[order_code]' => $txn->order_code,
            ]);
            $response = Http::withBasicAuth($adapter->secretKey(), '')
                ->asForm()->timeout(30)->post($adapter->baseUrl() . '/refunds', $body);

            if ($response->successful()) {
                $txn->update(['status' => 'refunded', 'raw' => $response->json()]);
                if ($txn->order && $txn->order->payment_status === 'paid') {
                    $txn->order->update(['payment_status' => 'refunded']);
                }
                $this->emitRefunded($txn, $amountMinor);

                return ['success' => true, 'data' => $response->json()];
            }

            Log::warning('Stripe refund failed', ['order' => $txn->order_code, 'http' => $response->status()]);

            return ['success' => false, 'message' => 'Refund rejected by gateway (HTTP ' . $response->status() . ').'];
        } catch (\Exception $e) {
            Log::error('Stripe refund exception', ['order' => $txn->order_code, 'error' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function refundPayPal(PaymentTransaction $txn, ?int $amountMinor, string $reason): array
    {
        try {
            $adapter = new PayPalOrderAdapter($txn->gateway);
            $token = Http::withBasicAuth(
                (string) ($txn->gateway->api_key_encrypted ?? ''),
                (string) ($txn->gateway->api_secret_encrypted ?? '')
            )->asForm()->timeout(30)
                ->post($adapter->baseUrl() . '/v1/oauth2/token', ['grant_type' => 'client_credentials'])
                ->json()['access_token'] ?? null;

            if (!$token) {
                return ['success' => false, 'message' => 'PayPal auth failed.'];
            }

            // Order -> captures lookup, then refund the first capture.
            $order = Http::withToken($token)->timeout(30)
                ->get($adapter->baseUrl() . '/v2/checkout/orders/' . $txn->gateway_reference)
                ->json();
            $captureId = $order['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
            if (!$captureId) {
                return ['success' => false, 'message' => 'No PayPal capture found to refund.'];
            }

            $body = $amountMinor
                ? ['amount' => ['currency_code' => 'IDR', 'value' => number_format($amountMinor, 0, '.', '')], 'note_to_payer' => $reason ?: null]
                : ['note_to_payer' => $reason ?: null];
            $response = Http::withToken($token)->timeout(30)
                ->post($adapter->baseUrl() . "/v2/payments/captures/{$captureId}/refund", array_filter($body));

            if ($response->successful()) {
                $txn->update(['status' => 'refunded', 'raw' => $response->json()]);
                if ($txn->order && $txn->order->payment_status === 'paid') {
                    $txn->order->update(['payment_status' => 'refunded']);
                }
                $this->emitRefunded($txn, $amountMinor);

                return ['success' => true, 'data' => $response->json()];
            }

            Log::warning('PayPal refund failed', ['order' => $txn->order_code, 'http' => $response->status()]);

            return ['success' => false, 'message' => 'Refund rejected by gateway (HTTP ' . $response->status() . ').'];
        } catch (\Exception $e) {
            Log::error('PayPal refund exception', ['order' => $txn->order_code, 'error' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function emitRefunded(PaymentTransaction $txn, ?int $amountMinor): void
    {
        if (!class_exists(\App\Events\RefundCreated::class)) {
            return;
        }
        try {
            $txn->loadMissing('order');
            event(new \App\Events\RefundCreated($txn->order ?? $txn, $amountMinor ?? (int) $txn->amount));
        } catch (\Exception $e) {
            Log::warning('RefundCreated dispatch failed (non-fatal)', ['txn' => $txn->id, 'error' => $e->getMessage()]);
        }
    }
}
