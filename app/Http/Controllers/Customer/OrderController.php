<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('orderDetails.product')
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderDetails.product', 'deliveryHistories', 'refundRequests');

        // Timeline live: gabung delivery_histories DB (sumber: admin / webhook Biteship)
        $trackingTimeline = $order->deliveryHistories->sortBy('created_at')->map(fn($h) => [
            'label' => $h->status ?? 'Update',
            'date' => $h->created_at?->format('d M Y H:i'),
            'note' => $h->note ?? '',
        ])->values();

        $view = view()->exists('customer.orders.show') ? 'customer.orders.show' : 'customer.order-detail';

        return view($view, compact('order', 'trackingTimeline'));
    }
}
