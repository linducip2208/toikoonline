<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayClosedAdapter implements PaymentAdapterInterface
{
    public function __construct(protected PaymentGatewayConfig $gateway) {}

    public function createTransaction(array $payload): array
    {
        $merchantCode = $this->gateway->config['merchant_code'] ?? '';
        $apiKey = $this->gateway->api_key_encrypted ?? '';
        $privateKey = $this->gateway->api_secret_encrypted ?? '';
        $merchantRef = $payload['order_id'] ?? $payload['order_code'] ?? uniqid('TRX-');
        $amount = (int) ($payload['amount'] ?? $payload['gross_amount'] ?? 0);

        $body = [
            'method' => $payload['channel'] ?? 'BRIVA',
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $payload['customer']['first_name'] ?? ($payload['customer_details']['first_name'] ?? 'Customer'),
            'customer_email' => $payload['customer']['email'] ?? ($payload['customer_details']['email'] ?? ''),
            'customer_phone' => $payload['customer']['phone'] ?? ($payload['customer_details']['phone'] ?? ''),
            'order_items' => $payload['items'] ?? $payload['item_details'] ?? [],
            'return_url' => $payload['return_url'] ?? ($payload['success_url'] ?? ''),
            'signature' => hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post(rtrim($this->gateway->base_url, '/') . '/transaction/create', $body);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['success'] ?? false) === true) {
                    return [
                        'success' => true,
                        'redirect_url' => $data['data']['checkout_url'] ?? null,
                        'reference' => $data['data']['reference'] ?? null,
                        'raw' => $data['data'],
                    ];
                }
                return ['success' => false, 'message' => $data['message'] ?? 'Unknown error'];
            }

            Log::error('Tripay error', ['response' => $response->body()]);
            return ['success' => false, 'message' => $response->body()];
        } catch (\Exception $e) {
            Log::error('Tripay exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . ($this->gateway->api_key_encrypted ?? ''),
            ])->get(rtrim($this->gateway->base_url, '/') . "/transaction/detail?reference={$transactionId}");

            return $response->successful()
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false];
        }
    }

    public function verifyCallback(array $requestData): bool
    {
        $privateKey = $this->gateway->api_secret_encrypted ?? '';
        $callbackSignature = $requestData['headers']['x-callback-signature'] ?? '';
        $jsonBody = json_encode($requestData['body'] ?? []);
        $signature = hash_hmac('sha256', $jsonBody, $privateKey);
        return hash_equals($signature, $callbackSignature);
    }

    public function getChannels(): array
    {
        return [
            'BCAVA', 'BNIVA', 'BRIVA', 'MANDIRIVA', 'BSIVA', 'PERMATAVA',
            'QRIS', 'QRISC', 'QRIS2',
            'GOPAY', 'OVO', 'DANA', 'SHOPEEPAY', 'LINKAJA',
            'ALFAMART', 'INDOMARET',
        ];
    }
}
