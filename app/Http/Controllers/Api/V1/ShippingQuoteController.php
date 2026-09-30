<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ShippingQuoteRequest;
use App\Services\Shipping\ShippingManager;
use Illuminate\Http\JsonResponse;

class ShippingQuoteController extends Controller
{
    public function quote(ShippingQuoteRequest $request, ShippingManager $manager): JsonResponse
    {
        $origin = $request->input('origin') ?: $manager->defaultOrigin();

        if (!$origin) {
            return response()->json(['success' => false, 'message' => 'Store origin not configured.'], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $manager->cachedQuote($origin, (string) $request->destination, (int) $request->weight, (string) $request->input('couriers', '')),
            'pickup_points' => $manager->pickupPoints(),
        ]);
    }
}
