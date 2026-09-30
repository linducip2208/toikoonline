<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Cart;
use App\Models\UserCoupon;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::where('status', true)
            ->where('end_date', '>=', now()->timestamp)
            ->orderBy('discount', 'desc')
            ->paginate(12);

        return view('storefront.coupons', compact('coupons'));
    }

    public function claim(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);
        $coupon = Coupon::where('code', $request->code)->where('status', true)->firstOrFail();

        if (! Auth::check()) {
            return redirect()->route('login')->with('info', 'Masuk dulu untuk klaim voucher '.$coupon->code);
        }

        UserCoupon::firstOrCreate(
            ['user_id' => Auth::id(), 'coupon_id' => $coupon->id],
            ['coupon_code' => $coupon->code]
        );

        return back()->with('success', 'Voucher '.$coupon->code.' berhasil diklaim! Pakai saat checkout.');
    }

    /** Validasi kupon live di halaman checkout (dipakai Alpine). */
    public function validate(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);
        $subtotal = Cart::where('user_id', Auth::id())->get()
            ->sum(fn($i) => $i->price * $i->quantity);

        $result = app(CouponService::class)->apply($request->code, Auth::id(), (float) $subtotal);

        if ($result['error']) {
            return response()->json(['success' => false, 'message' => $result['error']]);
        }

        return response()->json([
            'success' => true,
            'discount' => (int) $result['discount'],
            'code' => $result['coupon']->code,
        ]);
    }
}
