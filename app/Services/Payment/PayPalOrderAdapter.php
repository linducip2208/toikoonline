<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PayPal Orders API v2 driver (gateway_format 'paypal-order').
 *
 * Credential fields (PaymentGatewayConfig):
 * - api_key_encrypted   : PayPal Client ID
 * - api_secret_encrypted: PayPal Secret
 * - webhook_config      : ['webhook_id' => '...'] for webhook verification
 * - base_url            : sandbox https://api-m.sandbox.paypal.com (default
 *   when is_sandbox), live https://api-m.paypal.com
 * - is_sandbox          : true => sandbox base URL + no live charges
 *
 * Webhook: PayPal signature headers (transmission id/time/signature +
 * webhook id) verified server-side via /v1/notifications/verify-webhook-signature.
 */
class PayPalOrderAdapter implements PaymentGatewayInterface
{
    public function __construct(protected PaymentGatewayConfig $gateway) {}

    public function baseUrl(): string
    {
        if ($this->gateway->base_url) {
            return rtrim($this->gateway->base_url, '/');
        }

        return $this->gateway->is_sandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function webhookId(): string
    {
        $cfg = $this->gateway->webhook_config ?? [];

        return (string) ($cfg['webhook_id'] ?? '');
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
     */
    public static function buildPayload(string $orderId, int $amountMinor, string $returnUrl = '', string $cancelUrl = ''): array
    {
        return [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $orderId,
                'amount' => [
                    'currency_code' => 'IDR',
                    'value' => number_format(max(0, $amountMinor), 0, '.', ''),
                ],
            ]],
            'application_context' => array_filter([
                'return_url' => $returnUrl ?: null,
                'cancel_url' => $cancelUrl ?: null,
            ]),
        ];
    }

    protected function accessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth(
                (string) ($this->gateway->api_key_encrypted ?? ''),
                (string) ($this->gateway->api_secret_encrypted ?? '')
            )->asForm()->post($this->baseUrl() . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

            return $response->successful() ? ($response->json()['access_token'] ?? null) : null;
        } catch (\Exception) {
            return null;
        }
    }

    public function createTransaction(array $payload): array
    {
        $orderId = (string) ($payload['order_id'] ?? $payload['order_code'] ?? uniqid('ORD-'));
        $amount = (int) ($payload['amount'] ?? $payload['gross_amount'] ?? 0);

        try {
            $token = $this->accessToken();
            if (!$token) {
                return ['success' => false, 'message' => 'PayPal auth failed.'];
            }

            $response = Http::withToken($token)
                ->post($this->baseUrl() . '/v2/checkout/orders', static::buildPayload(
                    $orderId, $amount,
                    (string) ($payload['return_url'] ?? ''),
                    (string) ($payload['callback_url'] ?? '')
                ));

            if ($response->successful()) {
                $data = $response->json();
                $approve = collect($data['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

                return [
                    'success' => true,
                    'transaction_id' => $data['id'] ?? null,
                    'token' => $data['id'] ?? null,
                    'redirect_url' => $approve,
                    'raw' => $data,
                ];
            }

            Log::warning('PayPal order error', ['http' => $response->status()]);

            return ['success' => false, 'message' => 'Payment gateway error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('PayPal order exception', ['error' => $e->getMessage()]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $token = $this->accessToken();
            if (!$token) {
                return ['success' => false, 'message' => 'PayPal auth failed.'];
            }
            $response = Http::withToken($token)->get($this->baseUrl() . '/v2/checkout/orders/' . $transactionId);
            if (!$response->successful()) {
                return ['success' => false, 'message' => 'Failed to get status'];
            }
            $status = strtoupper((string) ($response->json()['status'] ?? ''));
            $mapped = in_array($status, ['COMPLETED'], true) ? 'success'
                : (in_array($status, ['VOIDED'], true) ? 'failed' : 'pending');

            return ['success' => true, 'data' => [
                'status' => $mapped,
                'transaction_status' => $mapped,
            ], 'raw' => $response->json()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function verifyCallback(array $requestData): bool
    {
        $headers = $requestData['_headers'] ?? [];
        $transmissionId = $headers['paypal-transmission-id'] ?? $headers['Paypal-Transmission-Id'] ?? '';
        $transmissionTime = $headers['paypal-transmission-time'] ?? '';
        $transmissionSig = $headers['paypal-transmission-sig'] ?? '';
        $certUrl = $headers['paypal-cert-url'] ?? '';
        $authAlgo = $headers['paypal-auth-algo'] ?? '';
        $body = $requestData['_raw_body'] ?? null;

        if (!$transmissionId || !$transmissionSig || !$this->webhookId() || $body === null) {
            return false;
        }

        try {
            $token = $this->accessToken();
            if (!$token) {
                return false;
            }
            $response = Http::withToken($token)->post($this->baseUrl() . '/v1/notifications/verify-webhook-signature', [
                'transmission_id' => $transmissionId,
                'transmission_time' => $transmissionTime,
                'cert_url' => $certUrl,
                'auth_algo' => $authAlgo,
                'transmission_sig' => $transmissionSig,
                'webhook_id' => $this->webhookId(),
                'webhook_event' => is_string($body) ? json_decode($body, true) : $body,
            ]);

            return $response->successful()
                && strtoupper((string) ($response->json()['verification_status'] ?? '')) === 'SUCCESS';
        } catch (\Exception) {
            return false;
        }
    }

    public function getChannels(): array
    {
        return ['paypal'];
    }
}
