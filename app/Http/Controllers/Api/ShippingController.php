<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingConfig;
use App\Services\Shipping\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'origin' => 'required|integer',
            'destination' => 'required|integer',
            'weight' => 'required|integer|min:1',
            'courier' => 'nullable|string',
            'provider_id' => 'nullable|integer|exists:shipping_configs,id',
        ]);

        $config = $this->resolveConfig($request);
        $this->service->setConfig($config);

        $costs = $this->service->getCost(
            (int) $request->origin,
            (int) $request->destination,
            (int) $request->weight,
            $request->courier ?? ''
        );

        return response()->json([
            'success' => true,
            'data' => $costs,
        ]);
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
