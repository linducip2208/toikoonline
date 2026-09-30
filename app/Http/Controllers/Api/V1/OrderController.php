<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with('orderDetails.product')
            ->where('user_id', request()->user()->id)
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders)->response();
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with('orderDetails.product')
            ->where('user_id', request()->user()->id)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => (new OrderResource($order))->resolve()]);
    }
}
