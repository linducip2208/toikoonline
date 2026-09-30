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

        // Data nyata untuk widget (dulu dummy Alpine)
        $statusOf = fn($o) => $o->payment_status !== 'paid' ? 'Menunggu Pembayaran'
            : match ($o->delivery_status) {
                'delivered' => 'Selesai', 'cancelled' => 'Dibatalkan',
                'on_delivery', 'picked_up' => 'Dikirim', default => 'Diproses',
            };
        $recentOrderRows = $recentOrders->map(fn($o) => [
            'id' => $o->id,
            'code' => $o->code,
            'date' => $o->created_at->format('d M Y'),
            'status' => $statusOf($o),
            'total' => (int) $o->grand_total,
        ])->values();
        $wishlistRows = Wishlist::where('user_id', $user->id)->with('product')->latest()->take(4)->get()
            ->filter(fn($w) => $w->product)->map(fn($w) => [
                'name' => $w->product->name,
                'price' => (int) ($w->product->unit_price),
                'slug' => $w->product->slug,
                'image' => $w->product->thumbnail_img ? asset($w->product->thumbnail_img) : null,
            ])->values();

        return view('customer.dashboard', compact(
            'user', 'totalOrders', 'pendingOrders', 'wishlistCount', 'recentOrders',
            'loyaltyPoints', 'recentOrderRows', 'wishlistRows'
        ));
    }
}
