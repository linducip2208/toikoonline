<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditInvoiceAdapter implements PaymentAdapterInterface
{
    public function __construct(protected PaymentGatewayConfig $gateway) {}

    public function createTransaction(array $payload): array
    {
        try {
            $response = Http::withBasicAuth($this->gateway->api_secret_encrypted ?? '', '')
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(rtrim($this->gateway->base_url, '/') . '/v2/invoices', [
                    'external_id' => $payload['order_id'] ?? $payload['order_code'],
                    'amount' => $payload['amount'] ?? $payload['gross_amount'] ?? 0,
                    'payer_email' => $payload['customer']['email'] ?? ($payload['customer_details']['email'] ?? ''),
                    'description' => $payload['description'] ?? 'Pembayaran pesanan',
                    'success_redirect_url' => $payload['success_url'] ?? ($payload['return_url'] ?? ''),
                    'failure_redirect_url' => $payload['failure_url'] ?? '',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'redirect_url' => $data['invoice_url'] ?? null,
                    'invoice_id' => $data['id'] ?? null,
                    'raw' => $data,
                ];
            }

            Log::error('Xendit error', ['response' => $response->body()]);
            return ['success' => false, 'message' => $response->body()];
        } catch (\Exception $e) {
            Log::error('Xendit exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $response = Http::withBasicAuth($this->gateway->api_secret_encrypted ?? '', '')
                ->get(rtrim($this->gateway->base_url, '/') . "/v2/invoices/{$transactionId}");

            return $response->successful()
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false];
        } catch (\Exception $e) {
            return ['success' => false];
        }
    }

    public function verifyCallback(array $requestData): bool
    {
        $callbackToken = $this->gateway->webhook_config['callback_token'] ?? '';
        $incomingToken = $requestData['headers']['x-callback-token'] ?? '';
        return $callbackToken && hash_equals($callbackToken, $incomingToken);
    }

    public function getChannels(): array
    {
        return ['BCA', 'BNI', 'BRI', 'Mandiri', 'Permata', 'QRIS', 'OVO', 'DANA', 'LinkAja', 'Alfamart', 'Indomaret'];
    }
}
