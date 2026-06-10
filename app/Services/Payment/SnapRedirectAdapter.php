<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SnapRedirectAdapter implements PaymentAdapterInterface
{
    public function __construct(protected PaymentGatewayConfig $gateway) {}

    public function createTransaction(array $payload): array
    {
        $orderId = $payload['order_id'] ?? $payload['order_code'] ?? uniqid('ORD-');
        $amount = $payload['amount'] ?? $payload['gross_amount'] ?? 0;
        $customer = $payload['customer'] ?? $payload['customer_details'] ?? [];
        $items = $payload['items'] ?? $payload['item_details'] ?? [];

        $body = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => $customer,
            'item_details' => $items,
            'enabled_payments' => $payload['channels'] ?? [],
        ];

        if (!empty($payload['callbacks'])) {
            $body['callbacks'] = $payload['callbacks'];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->withBasicAuth($this->gateway->server_key ?? '', '')
              ->post(rtrim($this->gateway->base_url, '/') . '/transactions', $body);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'token' => $data['token'] ?? null,
                    'redirect_url' => $data['redirect_url'] ?? null,
                    'raw' => $data,
                ];
            }

            Log::error('Midtrans Snap error', ['response' => $response->body()]);
            return ['success' => false, 'message' => 'Payment gateway error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('Midtrans Snap exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $response = Http::withBasicAuth($this->gateway->server_key ?? '', '')
                ->get(rtrim($this->gateway->base_url, '/') . "/{$transactionId}/status");

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'message' => 'Failed to get status'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function verifyCallback(array $requestData): bool
    {
        $signatureKey = hash('sha512',
            ($requestData['order_id'] ?? '') .
            ($requestData['status_code'] ?? '') .
            ($requestData['gross_amount'] ?? '') .
            ($this->gateway->server_key ?? '')
        );

        return hash_equals($signatureKey, $requestData['signature_key'] ?? '');
    }

    public function getChannels(): array
    {
        $config = $this->gateway->config ?? [];
        return $config['channels'] ?? [];
    }
}
