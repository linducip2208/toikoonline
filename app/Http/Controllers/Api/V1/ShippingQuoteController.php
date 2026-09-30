<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ShippingQuoteRequest;
use App\Services\Shipping\ShippingManager;
use Illuminate\Http\JsonResponse;

class ShippingQuoteController extends Controller
{
    public function quote(ShippingQuoteRequest $request, ShippingManager $manager, \App\Services\Shipping\ShippingMethodService $tables): JsonResponse
    {
        $origin = $request->input('origin') ?: $manager->defaultOrigin();

        if (!$origin) {
            return response()->json(['success' => false, 'message' => 'Store origin not configured.'], 422);
        }

        $live = $manager->cachedQuote($origin, (string) $request->destination, (int) $request->weight, (string) $request->input('couriers', ''));
        $data = $tables->mergeWithLive($live, [
            'city' => (string) $request->destination,
            'postcode' => (string) $request->input('postcode', ''),
            'state' => (string) $request->input('state', ''),
            'country' => (string) $request->input('country', 'ID'),
        ], (int) $request->weight, (int) $request->input('subtotal', 0));

        return response()->json([
            'success' => true,
            'data' => $data,
            'pickup_points' => $manager->pickupPoints(),
        ]);
    }
}
