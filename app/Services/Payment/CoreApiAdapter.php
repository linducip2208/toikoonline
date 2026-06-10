<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoreApiAdapter implements PaymentAdapterInterface
{
    public function __construct(protected PaymentGatewayConfig $gateway) {}

    public function createTransaction(array $payload): array
    {
        $orderId = $payload['order_id'] ?? $payload['order_code'] ?? uniqid('ORD-');
        $amount = $payload['amount'] ?? $payload['gross_amount'] ?? 0;
        $bank = $payload['bank'] ?? 'bca';
        $channel = $payload['channel'] ?? 'bank_transfer';

        $body = [
            'payment_type' => $channel,
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => $payload['customer'] ?? [],
            'item_details' => $payload['items'] ?? [],
        ];

        if ($channel === 'bank_transfer') {
            $body['bank_transfer'] = ['bank' => $bank];
        } elseif ($channel === 'echannel') {
            $body['echannel'] = ['bill_info1' => 'Pembayaran', 'bill_info2' => 'Online'];
        } elseif ($channel === 'gopay') {
            $body['gopay'] = ['enable_callback' => true];
        } elseif ($channel === 'shopeepay') {
            $body['shopeepay'] = ['enable_callback' => true];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->withBasicAuth($this->gateway->server_key ?? '', '')
              ->post(rtrim($this->gateway->base_url, '/') . '/charge', $body);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'va_number' => $data['va_numbers'][0]['va_number'] ?? $data['permata_va_number'] ?? $data['bill_key'] ?? null,
                    'bank' => $data['va_numbers'][0]['bank'] ?? $data['bank'] ?? null,
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'expiry' => $data['expiry_time'] ?? null,
                    'raw' => $data,
                ];
            }

            Log::error('Midtrans Core error', ['response' => $response->body()]);
            return ['success' => false, 'message' => 'Payment error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('Midtrans Core exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $response = Http::withBasicAuth($this->gateway->server_key ?? '', '')
                ->get(rtrim($this->gateway->base_url, '/') . "/{$transactionId}/status");

            return $response->successful()
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false, 'message' => 'Failed'];
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
        return [
            'bank_transfer' => ['BCA', 'BNI', 'BRI', 'Mandiri', 'Permata'],
            'echannel' => ['Mandiri Bill'],
            'gopay' => ['GoPay'],
            'shopeepay' => ['ShopeePay'],
            'qris' => ['QRIS'],
        ];
    }
}
