<?php

namespace App\Services\Shipping;

use App\Models\ShippingConfig;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class ShippingService
{
    protected ?ShippingConfig $config = null;
    protected string $apiKey = '';
    protected string $baseUrl = '';

    public function setConfig(ShippingConfig $config): static
    {
        $this->config = $config;
        $this->apiKey = $config->api_key_encrypted ? Crypt::decryptString($config->api_key_encrypted) : '';
        $this->baseUrl = $config->base_url ?? '';
        return $this;
    }

    public function getProvinces(): array
    {
        $response = $this->makeRequest('GET', '/province');
        return $response['rajaongkir']['results'] ?? $response['data'] ?? $response['provinces'] ?? [];
    }

    public function getCities(int $provinceId): array
    {
        $response = $this->makeRequest('GET', "/city?province={$provinceId}");
        return $response['rajaongkir']['results'] ?? $response['data'] ?? $response['cities'] ?? [];
    }

    public function getCost(int $originCityId, int $destinationCityId, int $weight, string $courier = ''): array
    {
        $payload = [
            'origin' => $originCityId,
            'destination' => $destinationCityId,
            'weight' => $weight > 0 ? $weight : 1000,
            'courier' => $courier ?: 'jne:pos:tiki',
        ];

        $response = $this->makeRequest('POST', '/cost', $payload);
        $results = $response['rajaongkir']['results'] ?? $response['data'] ?? $response['costs'] ?? [];

        return array_map(function ($service) {
            return [
                'courier' => $service['code'] ?? $service['courier'] ?? '',
                'service_name' => $service['name'] ?? '',
                'costs' => array_map(fn($c) => [
                    'service' => $c['service'] ?? '',
                    'description' => $c['description'] ?? '',
                    'cost' => $c['cost'][0]['value'] ?? $c['cost'] ?? 0,
                    'etd' => $c['cost'][0]['etd'] ?? $c['etd'] ?? '',
                ], $service['costs'] ?? []),
            ];
        }, $results);
    }

    public function getTracking(string $waybill, string $courier = ''): array
    {
        $response = $this->makeRequest('POST', '/waybill', [
            'waybill' => $waybill,
            'courier' => $courier,
        ]);
        return $response['rajaongkir']['result'] ?? $response['data'] ?? [];
    }

    protected function makeRequest(string $method, string $path, array $data = []): array
    {
        $url = rtrim($this->baseUrl, '/') . $path;
        $headers = [
            'Accept' => 'application/json',
            'key' => $this->apiKey,
        ];

        $response = Http::withHeaders($headers)
            ->timeout(30)
            ->send($method, $url, $data ? ['form_params' => $data] : []);

        if (!$response->successful()) {
            throw new \Exception("Shipping API error: {$response->body()}");
        }

        return $response->json() ?? [];
    }
}
