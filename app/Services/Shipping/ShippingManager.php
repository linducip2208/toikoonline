<?php

namespace App\Services\Shipping;

use App\Models\BusinessSetting;
use App\Models\PickupPoint;
use App\Models\ShippingConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Normalized multi-provider shipping quotes with per-provider try/catch
 * fallback, plus a zero-API-key local flat-rate provider and pickup points.
 *
 * Provider rows are read from shipping_configs (is_active, sort_order).
 * Supported provider_format values:
 *  - rajaongkir / rajaongkir-api : via ShippingService::getCost
 *  - biteship / biteship-api      : via BiteshipAdapter::getRates
 *  - local-flat                    : flat rate from extra_params['flat_rate']
 * Unknown formats are skipped (logged), never fatal.
 */
class ShippingManager
{
    /**
     * @return array<int, array{provider_id:int, provider:string, courier:string, service:string, description:string, cost:int, etd:string}>
     */
    public function quote(string|int $origin, string|int $destination, int $weightGram, string $couriers = ''): array
    {
        $configs = ShippingConfig::where('is_active', true)->orderBy('sort_order')->get();
        $rates = [];

        foreach ($configs as $config) {
            try {
                foreach ($this->ratesForProvider($config, (string) $origin, (string) $destination, $weightGram, $couriers) as $row) {
                    $rates[] = $row;
                }
            } catch (\Exception $e) {
                Log::warning('ShippingManager provider failed, skipped', [
                    'provider' => $config->provider_format,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        usort($rates, fn ($a, $b) => $a['cost'] <=> $b['cost']);

        return $rates;
    }

    /**
     * @return array<int, array>
     */
    protected function ratesForProvider(ShippingConfig $config, string $origin, string $destination, int $weightGram, string $couriers): array
    {
        $format = strtolower((string) $config->provider_format);

        if ($format === 'local-flat') {
            $extra = $config->extra_params ?? [];
            $flat = (int) ($extra['flat_rate'] ?? $extra['rate'] ?? 0);
            $freeAbove = (int) ($extra['free_above'] ?? 0);

            return [[
                'provider_id' => $config->id,
                'provider' => $config->name,
                'courier' => 'local',
                'service' => 'flat',
                'description' => $freeAbove > 0 ? "Flat rate, free above Rp{$freeAbove}" : 'Flat rate',
                'cost' => $flat,
                'etd' => (string) ($extra['etd'] ?? '1-2'),
            ]];
        }

        if (in_array($format, ['biteship', 'biteship-api'], true)) {
            $rows = (new BiteshipAdapter($config))->getRates($origin, $destination, $weightGram, $couriers);
            return $this->flattenGrouped($config, $rows);
        }

        $rows = (new ShippingService())->setConfig($config)->getCost($origin, $destination, $weightGram, $couriers);
        return $this->flattenGrouped($config, $rows);
    }

    protected function flattenGrouped(ShippingConfig $config, array $grouped): array
    {
        $out = [];
        foreach ($grouped as $group) {
            $courier = (string) ($group['code'] ?? $group['courier'] ?? '');
            foreach ($group['costs'] ?? [] as $c) {
                $out[] = [
                    'provider_id' => $config->id,
                    'provider' => $config->name,
                    'courier' => $courier,
                    'service' => (string) ($c['service'] ?? ''),
                    'description' => (string) ($c['description'] ?? ''),
                    'cost' => (int) ($c['cost'] ?? 0),
                    'etd' => (string) ($c['etd'] ?? ''),
                ];
            }
        }

        return $out;
    }

    public function defaultOrigin(): string
    {
        return (string) (BusinessSetting::getValue(
            'warehouse_area_id',
            BusinessSetting::getValue('warehouse_city_id', env('SHOP_ORIGIN_AREA_ID', env('SHOP_ORIGIN_CITY_ID', '')))
        ) ?? '');
    }

    /**
     * @return array<int, array{id:int, name:string, address:string, phone:string}>
     */
    public function pickupPoints(): array
    {
        if (!class_exists(PickupPoint::class)) {
            return [];
        }

        try {
            return PickupPoint::query()->get()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'address' => $p->address,
                'phone' => $p->phone,
            ])->all();
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Label data for printing. Prefers Agent 2's Shipment model when present,
     * otherwise falls back to order fields. Never fatal.
     */
    public function labelData(int|string $orderId): array
    {
        $order = \App\Models\Order::where('id', $orderId)->orWhere('code', $orderId)->first();
        if (!$order) {
            return [];
        }

        if (class_exists(\App\Models\Shipment::class)) {
            try {
                $shipment = \App\Models\Shipment::where('order_id', $order->id)->latest()->first();
                if ($shipment) {
                    return [
                        'order_code' => $order->code,
                        'courier' => $shipment->courier ?? $order->courier,
                        'service' => $shipment->service ?? null,
                        'waybill' => $shipment->waybill ?? $shipment->tracking_number ?? $order->tracking_number,
                        'receiver' => $shipment->receiver_name ?? $order->shipping_address,
                        'weight' => $shipment->weight ?? null,
                    ];
                }
            } catch (\Exception) {
                // fall through to order fields
            }
        }

        return [
            'order_code' => $order->code,
            'courier' => $order->courier,
            'service' => $order->shipping_method,
            'waybill' => $order->tracking_number,
            'receiver' => $order->shipping_address,
            'weight' => null,
        ];
    }

    public function cachedQuote(string|int $origin, string|int $destination, int $weightGram, string $couriers = ''): array
    {
        $key = 'ship_quote:' . md5(json_encode([$origin, $destination, $weightGram, $couriers]));

        return Cache::remember($key, now()->addHour(), fn () => $this->quote($origin, $destination, $weightGram, $couriers));
    }
}
