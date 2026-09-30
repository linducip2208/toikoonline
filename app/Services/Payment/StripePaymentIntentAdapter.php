<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Stripe PaymentIntent driver (gateway_format 'stripe-pi').
 *
 * Credential fields (PaymentGatewayConfig):
 * - api_key_encrypted : Stripe secret key (sk_test_... / sk_live_...)
 * - webhook_config    : ['webhook_secret' => 'whsec_...']
 * - base_url          : default https://api.stripe.com/v1
 * - is_sandbox        : true => test mode expected (key must start sk_test_)
 *
 * Webhook: Stripe-Signature header (t=...,v1=...), HMAC-SHA256 of
 * "timestamp.rawBody" with webhook secret, tolerance 300s.
 * Pass raw body via $requestData['_raw_body'] (see WebhookController).
 */
class StripePaymentIntentAdapter implements PaymentGatewayInterface
{
    public function __construct(protected PaymentGatewayConfig $gateway) {}

    public function baseUrl(): string
    {
        return rtrim($this->gateway->base_url ?: 'https://api.stripe.com/v1', '/');
    }

    public function secretKey(): string
    {
        return (string) ($this->gateway->api_key_encrypted ?? '');
    }

    public function webhookSecret(): string
    {
        $cfg = $this->gateway->webhook_config ?? [];

        return (string) ($cfg['webhook_secret'] ?? '');
    }

    public function feeFor(int $amountMinor): int
    {
        return PaymentFees::feeForConfig(($this->gateway->config ?? [])['payment_fee'] ?? 0, $amountMinor);
    }

    public function instructions(): ?string
    {
        return PaymentFees::instructionsForConfig($this->gateway->config ?? []);
    }

    /**
     * Pure payload builder (unit-testable, no I/O).
     * Stripe expects USD-style decimal for most currencies, but IDR is a
     * zero-decimal currency: amount stays integer IDR.
     */
    public static function buildPayload(string $orderId, int $amountMinor, array $extra = []): array
    {
        return array_merge([
            'amount' => max(0, $amountMinor),
            'currency' => 'idr',
            'payment_method_types[]' => 'card',
            'metadata[order_id]' => $orderId,
        ], $extra);
    }

    public function createTransaction(array $payload): array
    {
        $orderId = (string) ($payload['order_id'] ?? $payload['order_code'] ?? uniqid('ORD-'));
        $amount = (int) ($payload['amount'] ?? $payload['gross_amount'] ?? 0);

        try {
            $response = Http::withBasicAuth($this->secretKey(), '')
                ->asForm()
                ->post($this->baseUrl() . '/payment_intents', static::buildPayload($orderId, $amount));

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'transaction_id' => $data['id'] ?? null,
                    'token' => $data['id'] ?? null,
                    'client_secret' => $data['client_secret'] ?? null,
                    'redirect_url' => $payload['return_url'] ?? null,
                    'raw' => $data,
                ];
            }

            Log::warning('Stripe PaymentIntent error', ['http' => $response->status()]);

            return ['success' => false, 'message' => 'Payment gateway error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('Stripe PaymentIntent exception', ['error' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey(), '')
                ->get($this->baseUrl() . '/payment_intents/' . $transactionId);

            if (!$response->successful()) {
                return ['success' => false, 'message' => 'Failed to get status'];
            }
            $data = $response->json();
            $map = [
                'succeeded' => 'success',
                'processing' => 'pending',
                'requires_payment_method' => 'pending',
                'requires_action' => 'pending',
                'requires_confirmation' => 'pending',
                'requires_capture' => 'pending',
                'canceled' => 'failed',
            ];

            return ['success' => true, 'data' => [
                'status' => $map[$data['status'] ?? ''] ?? 'pending',
                'transaction_status' => $map[$data['status'] ?? ''] ?? 'pending',
            ], 'raw' => $data];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Pure signature check (unit-testable). $requestData['_raw_body'] must be
     * the exact raw request body; $requestData['_headers']['stripe-signature']
     * (or 'Stripe-Signature') carries t=...,v1=....
     */
    public static function signatureValid(string $rawBody, string $header, string $secret, int $tolerance = 300): bool
    {
        $parts = [];
        foreach (explode(',', $header) as $chunk) {
            $kv = explode('=', trim($chunk), 2);
            if (count($kv) === 2) {
                $parts[$kv[0]][] = $kv[1];
            }
        }
        if (empty($parts['t'][0]) || empty($parts['v1'])) {
            return false;
        }
        if (abs(time() - (int) $parts['t'][0]) > $tolerance) {
            return false;
        }
        $expected = hash_hmac('sha256', $parts['t'][0] . '.' . $rawBody, $secret);
        foreach ($parts['v1'] as $sig) {
            if (hash_equals($expected, $sig)) {
                return true;
            }
        }

        return false;
    }

    public function verifyCallback(array $requestData): bool
    {
        $headers = $requestData['_headers'] ?? [];
        $header = $headers['stripe-signature'] ?? $headers['Stripe-Signature'] ?? '';
        $rawBody = (string) ($requestData['_raw_body'] ?? '');
        if ($header === '' || $rawBody === '' || $this->webhookSecret() === '') {
            return false;
        }

        return static::signatureValid($rawBody, $header, $this->webhookSecret());
    }

    public function getChannels(): array
    {
        return ['card'];
    }
}
