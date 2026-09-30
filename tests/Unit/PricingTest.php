<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PricingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $this->user = User::factory()->create(['user_type' => 'customer']);
    }

    protected function makeCoupon(array $overrides = []): Coupon
    {
        return Coupon::create(array_merge([
            'user_id' => $this->user->id,
            'type' => 'discount',
            'code' => 'TEST-'.strtoupper(uniqid()),
            'details' => '{}',
            'discount' => 10,
            'discount_type' => 'percent',
            'start_date' => now()->subDay()->timestamp,
            'end_date' => now()->addDay()->timestamp,
            'min_buy' => 0,
            'max_discount' => 0,
            'status' => true,
        ], $overrides));
    }

    public function test_percent_coupon_applies_percentage(): void
    {
        $coupon = $this->makeCoupon(['code' => 'P10', 'discount' => 10, 'discount_type' => 'percent']);

        $result = app(CouponService::class)->apply('P10', $this->user->id, 200000);

        $this->assertNull($result['error']);
        $this->assertEquals(20000, $result['discount']);
        $this->assertEquals($coupon->id, $result['coupon']->id);
    }

    public function test_percent_coupon_respects_max_discount(): void
    {
        $this->makeCoupon(['code' => 'P50MAX', 'discount' => 50, 'discount_type' => 'percent', 'max_discount' => 15000]);

        $result = app(CouponService::class)->apply('P50MAX', $this->user->id, 200000);

        $this->assertNull($result['error']);
        $this->assertEquals(15000, $result['discount']);
    }

    public function test_amount_coupon_capped_at_subtotal(): void
    {
        $this->makeCoupon(['code' => 'FLAT', 'discount' => 50000, 'discount_type' => 'amount']);

        $result = app(CouponService::class)->apply('FLAT', $this->user->id, 30000);

        $this->assertNull($result['error']);
        $this->assertEquals(30000, $result['discount']);
    }

    public function test_min_buy_enforced(): void
    {
        $this->makeCoupon(['code' => 'MINB', 'discount' => 10, 'discount_type' => 'percent', 'min_buy' => 100000]);

        $result = app(CouponService::class)->apply('MINB', $this->user->id, 50000);

        $this->assertNotNull($result['error']);
        $this->assertEquals(0, $result['discount']);
    }

    public function test_unknown_code_returns_error(): void
    {
        $result = app(CouponService::class)->apply('NOPE-404', $this->user->id, 100000);

        $this->assertNotNull($result['error']);
        $this->assertNull($result['coupon']);
    }

    public function test_already_used_coupon_rejected(): void
    {
        $coupon = $this->makeCoupon(['code' => 'ONCE', 'discount' => 5000, 'discount_type' => 'amount']);
        CouponUsage::create(['user_id' => $this->user->id, 'coupon_id' => $coupon->id]);

        $result = app(CouponService::class)->apply('ONCE', $this->user->id, 100000);

        $this->assertNotNull($result['error']);
    }

    public function test_expired_coupon_rejected(): void
    {
        $this->makeCoupon(['code' => 'OLD', 'discount' => 5, 'discount_type' => 'percent', 'end_date' => now()->subDay()->timestamp]);

        $result = app(CouponService::class)->apply('OLD', $this->user->id, 100000);

        $this->assertNotNull($result['error']);
    }
}
