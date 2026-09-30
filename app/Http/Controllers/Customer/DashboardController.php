<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ClubPoint;
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
        // Loyalty: total poin dari club_points (fallback 0 jika tabel kosong)
        try {
            $loyaltyPoints = (int) ClubPoint::where('user_id', $user->id)->sum('points');
        } catch (\Exception) {
            $loyaltyPoints = 0;
        }

        return view('customer.dashboard', compact(
            'user', 'totalOrders', 'pendingOrders', 'wishlistCount', 'recentOrders', 'loyaltyPoints'
        ));
    }
}
