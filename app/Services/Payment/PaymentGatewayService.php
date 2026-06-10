<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewayConfig;
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
}
