<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Auth;

/**
 * Aturan kupon terpusat — dipakai checkout (validate endpoint + store)
 * agar diskon client & server selalu sama.
 */
class CouponService
{
    /**
     * @return array{coupon:?Coupon, discount:float, error:?string}
     */
    public function apply(?string $code, int $userId, float $subtotal): array
    {
        if (! $code) {
            return ['coupon' => null, 'discount' => 0, 'error' => null];
        }

        $coupon = Coupon::where('code', $code)->where('status', true)->first();
        if (! $coupon) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Kode kupon tidak ditemukan.'];
        }

        $now = now()->timestamp;
        if ($coupon->start_date && $now < (int) $coupon->start_date) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Kupon belum berlaku.'];
        }
        if ($coupon->end_date && $now > (int) $coupon->end_date) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Kupon sudah kedaluwarsa.'];
        }
        if ((float) $coupon->min_buy > 0 && $subtotal < (float) $coupon->min_buy) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Min. belanja Rp '.number_format($coupon->min_buy, 0, ',', '.').'.'];
        }

        // Kupon yang perlu diklaim dulu: wajib ada di user_coupons
        $needsClaim = $coupon->type === 'claimable';
        if ($needsClaim && ! UserCoupon::where('user_id', $userId)->where('coupon_id', $coupon->id)->exists()) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Klaim kupon dulu di halaman Kupon.'];
        }

        // Batasi 1x pakai per user
        if ($coupon->usages()->where('user_id', $userId)->exists()) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Kupon sudah pernah dipakai.'];
        }

        $discount = $coupon->discount_type === 'percent'
            ? $subtotal * ((float) $coupon->discount / 100)
            : (float) $coupon->discount;

        if ((float) $coupon->max_discount > 0) {
            $discount = min($discount, (float) $coupon->max_discount);
        }
        $discount = min($discount, $subtotal);

        return ['coupon' => $coupon, 'discount' => round($discount), 'error' => null];
    }
}
