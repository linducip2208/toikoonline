<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CouponValidateRequest;
use App\Models\Cart;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function validate(CouponValidateRequest $request, CouponService $coupons): JsonResponse
    {
        $subtotal = (int) round(
            Cart::where('user_id', $request->user()->id)->get()
                ->sum(fn ($i) => (float) $i->price * (int) $i->quantity)
        );

        $result = $coupons->apply($request->code, $request->user()->id, $subtotal);

        if ($result['error']) {
            return response()->json(['success' => false, 'message' => $result['error']], 422);
        }

        return response()->json(['success' => true, 'data' => [
            'code' => $request->code,
            'discount' => (int) round($result['discount']),
            'subtotal' => $subtotal,
        ]]);
    }
}
