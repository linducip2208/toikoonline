<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalOrders = Order::where('user_id', $user->id)->count();
        $pendingOrders = Order::where('user_id', $user->id)->where('delivery_status', 'pending')->count();
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();
        $recentOrders = Order::where('user_id', $user->id)->with('orderDetails.product')->latest()->take(5)->get();

        return view('customer.dashboard', compact(
            'user', 'totalOrders', 'pendingOrders', 'wishlistCount', 'recentOrders'
        ));
    }
}
