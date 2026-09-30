<?php

namespace App\Services\Shipping;

use App\Models\ShippingConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Biteship adapter — REST modern Indonesia.
 * Docs: https://biteship.com (auth Bearer API key)
 *
 * Mendukung: JNE, J&T, SiCepat, AnterAja, Paxel, GoSend, Grab.
 * Origin/destination memakai area_id Biteship (bukan city_id RajaOngkir).
 * Simpan area_id di ShippingConfig.extra_params['origin_area_id'].
 */
class BiteshipAdapter
{
    public function __construct(protected ShippingConfig $config) {}

    protected function apiKey(): string
    {
        // accessor sudah decrypt
        return $this->config->api_key_encrypted ?? '';
    }

    protected function baseUrl(): string
    {
        return rtrim($this->config->base_url ?: 'https://api.biteship.com/v1', '/');
    }

    /**
     * @param string $originAreaId  area_id Biteship gudang
     * @param string $destAreaId    area_id Biteship pembeli
     * @param int $weightGram
     * @param string $couriers      csv: jne,jnt,sicepat,anteraja,paxel,gosend
     */
    public function getRates(string $originAreaId, string $destAreaId, int $weightGram, string $couriers = ''): array
    {
        $payload = [
            'origin_area_id' => $originAreaId,
            'destination_area_id' => $destAreaId,
            'couriers' => $couriers ?: 'jne,jnt,sicepat,anteraja,paxel',
            'items' => [
                ['name' => 'Paket TokoOnline', 'weight' => max(1, $weightGram), 'quantity' => 1, 'value' => 10000],
            ],
        ];

        try {
            $res = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(20)->post($this->baseUrl().'/rates/couriers', $payload);

            if (! $res->successful()) {
                Log::warning('Biteship rates error', ['body' => $res->body()]);
                return [];
            }

            $data = $res->json();
            $pricing = $data['pricing'] ?? [];

            // Normalisasi ke format yang sama dengan RajaOngkir agar view tidak berubah
            $grouped = [];
            foreach ($pricing as $row) {
                $code = strtolower($row['courier_code'] ?? $row['courier_name'] ?? 'courier');
                $grouped[$code]['code'] = $code;
                $grouped[$code]['name'] = strtoupper($row['courier_name'] ?? $code);
                $grouped[$code]['costs'][] = [
                    'service' => $row['courier_service_code'] ?? $row['courier_service_name'] ?? '',
                    'description' => ($row['courier_service_name'] ?? '').' '.($row['description'] ?? ''),
                    'cost' => (int) ($row['price'] ?? 0),
                    'etd' => ($row['duration'] ?? '').' '.($row['duration_type'] ?? ''),
                ];
            }

            return array_values($grouped);
        } catch (\Exception $e) {
            Log::error('Biteship exception: '.$e->getMessage());
            return [];
        }
    }

    public function getAreas(string $search = ''): array
    {
        try {
            $res = Http::withHeaders(['Authorization' => 'Bearer '.$this->apiKey()])
                ->timeout(15)
                ->get($this->baseUrl().'/maps/areas', ['countries' => 'ID', 'input' => $search, 'type' => 'single']);

            return $res->successful() ? ($res->json()['areas'] ?? []) : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function track(string $waybill, string $courier = ''): array
    {
        try {
            $res = Http::withHeaders(['Authorization' => 'Bearer '.$this->apiKey()])
                ->timeout(15)
                ->post($this->baseUrl().'/trackings/'.$waybill.'/couriers/'.$courier);

            return $res->successful() ? ($res->json() ?? []) : [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
