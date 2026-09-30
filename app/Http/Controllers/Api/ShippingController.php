<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingConfig;
use App\Services\Shipping\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShippingController extends Controller
{
    protected ShippingService $service;

    public function __construct(ShippingService $service)
    {
        $this->service = $service;
    }

    public function cost(Request $request): JsonResponse
    {
        $request->validate([
            'origin' => 'nullable',
            'destination' => 'required',
            'weight' => 'required|integer|min:1|max:30000',
            'courier' => 'nullable|string|max:100',
            'provider_id' => 'nullable|integer|exists:shipping_configs,id',
        ]);

        try {
            // Asal gudang: dari request, atau default toko (BusinessSetting → .env)
            $origin = $request->origin ?: \App\Models\BusinessSetting::getValue(
                'warehouse_area_id',
                \App\Models\BusinessSetting::getValue('warehouse_city_id', env('SHOP_ORIGIN_AREA_ID', env('SHOP_ORIGIN_CITY_ID', '')))
            );
            if (! $origin) {
                return response()->json(['success' => false, 'message' => 'Kota asal toko belum diatur. Isi warehouse_area_id di BusinessSetting / SHOP_ORIGIN_AREA_ID di .env.', 'data' => []], 422);
            }

            $config = $this->resolveConfig($request);
            $this->service->setConfig($config);

            // Gratis ongkir: min belanja Rp150rb subsidi Rp10rb (aturan toko, bisa pindah ke BusinessSetting)
            $subtotal = (int) $request->input('subtotal', 0);
            $freeOngkirCover = ($subtotal >= 150000) ? 10000 : 0;

            $cacheKey = 'ship_cost:'.md5(json_encode([$config->id, $origin, $request->destination, $request->weight, $request->courier]));
            $costs = Cache::remember($cacheKey, now()->addHour(), function () use ($request, $origin) {
                return $this->service->getCost(
                    $request->origin ?? $origin,
                    $request->destination,
                    (int) $request->weight,
                    $request->courier ?? ''
                );
            });

            // Terapkan subsidi gratis ongkir ke tampilan (tidak minus)
            if ($freeOngkirCover > 0) {
                foreach ($costs as &$courier) {
                    foreach ($courier['costs'] as &$c) {
                        $c['cost_before_discount'] = $c['cost'];
                        $c['cost'] = max(0, $c['cost'] - $freeOngkirCover);
                        $c['free_ongkir_applied'] = $freeOngkirCover;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $costs,
                'free_ongkir_cover' => $freeOngkirCover,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => []], 422);
        }
    }

    public function track(Request $request, string $waybill): JsonResponse
    {
        $request->validate([
            'courier' => 'nullable|string',
            'provider_id' => 'nullable|integer|exists:shipping_configs,id',
        ]);

        $config = $this->resolveConfig($request);
        $this->service->setConfig($config);

        $tracking = $this->service->getTracking($waybill, $request->courier ?? '');

        return response()->json([
            'success' => true,
            'data' => $tracking,
        ]);
    }

    public function areas(Request $request): JsonResponse
    {
        $request->validate(['q' => 'nullable|string|max:100']);
        try {
            $config = ShippingConfig::where('is_active', true)->orderBy('sort_order')->firstOrFail();
            $this->service->setConfig($config);
            if ($this->service->isBiteship()) {
                $adapter = new \App\Services\Shipping\BiteshipAdapter($config);
                return response()->json(['success' => true, 'data' => $adapter->getAreas($request->q ?? '')]);
            }
            return response()->json(['success' => true, 'data' => []]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    protected function resolveConfig(Request $request): ShippingConfig
    {
        if ($request->filled('provider_id')) {
            $config = ShippingConfig::find($request->provider_id);
            if ($config && $config->is_active) {
                return $config;
            }
        }

        $config = ShippingConfig::where('is_active', true)->orderBy('sort_order')->first();

        if (!$config) {
            throw new \Exception('No active shipping provider configured.');
        }

        return $config;
    }
}
