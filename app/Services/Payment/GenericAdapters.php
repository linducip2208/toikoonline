<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenericRedirectAdapter implements PaymentAdapterInterface
{
    public function __construct(protected PaymentGatewayConfig $gateway) {}

    public function createTransaction(array $payload): array
    {
        try {
            $response = Http::withHeaders(array_merge([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ], $this->buildAuthHeaders()))
            ->post(rtrim($this->gateway->base_url, '/') . '/transaction', $this->buildPayload($payload));

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'redirect_url' => $data['redirect_url'] ?? $data['payment_url'] ?? null,
                    'raw' => $data,
                ];
            }
            return ['success' => false, 'message' => $response->body()];
        } catch (\Exception $e) {
            Log::error('GenericRedirect error', ['error' => $e->getMessage(), 'format' => $this->gateway->gateway_format]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $response = Http::withHeaders($this->buildAuthHeaders())
                ->get(rtrim($this->gateway->base_url, '/') . "/transaction/{$transactionId}");

            return $response->successful()
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false];
        }
    }

    public function verifyCallback(array $requestData): bool
    {
        return true;
    }

    public function getChannels(): array
    {
        return [];
    }

    protected function buildAuthHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . ($this->gateway->api_key_encrypted ?? ''),
            'X-API-Key' => $this->gateway->api_key_encrypted ?? '',
        ];
    }

    protected function buildPayload(array $payload): array
    {
        return [
            'order_id' => $payload['order_id'] ?? $payload['order_code'],
            'amount' => $payload['amount'] ?? $payload['gross_amount'] ?? 0,
            'customer' => $payload['customer'] ?? $payload['customer_details'] ?? [],
            'items' => $payload['items'] ?? $payload['item_details'] ?? [],
            'callback_url' => $payload['callback_url'] ?? '',
            'return_url' => $payload['return_url'] ?? '',
        ];
    }
}

class GenericApiAdapter implements PaymentAdapterInterface
{
    public function __construct(protected PaymentGatewayConfig $gateway) {}

    public function createTransaction(array $payload): array
    {
        try {
            $headers = array_merge([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . ($this->gateway->api_key_encrypted ?? ''),
            ], $this->gateway->extra_headers ?? []);

            $response = Http::withHeaders($headers)
                ->post(rtrim($this->gateway->base_url, '/') . '/transaction/create', [
                    'order_id' => $payload['order_id'] ?? $payload['order_code'],
                    'amount' => $payload['amount'] ?? $payload['gross_amount'] ?? 0,
                    'customer' => $payload['customer'] ?? $payload['customer_details'] ?? [],
                    'items' => $payload['items'] ?? $payload['item_details'] ?? [],
                    'callback_url' => $payload['callback_url'] ?? '',
                    'return_url' => $payload['return_url'] ?? '',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'redirect_url' => $data['redirect_url'] ?? $data['payment_url'] ?? null,
                    'va_number' => $data['va_number'] ?? null,
                    'raw' => $data,
                ];
            }
            return ['success' => false, 'message' => $response->body()];
        } catch (\Exception $e) {
            Log::error('GenericApi error', ['error' => $e->getMessage(), 'format' => $this->gateway->gateway_format]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        return ['success' => false, 'message' => 'Not supported'];
    }

    public function verifyCallback(array $requestData): bool
    {
        return true;
    }

    public function getChannels(): array
    {
        return [];
    }
}
