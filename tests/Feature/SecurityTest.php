<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentGatewayConfig;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\RequirePair::class);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
    }

    public function test_guest_cannot_access_account_area(): void
    {
        foreach (['/account', '/account/orders', '/account/profile', '/account/wishlist'] as $url) {
            $response = $this->get($url);
            $this->assertTrue(
                in_array($response->getStatusCode(), [302, 401, 403], true),
                "Guest access to {$url} returned {$response->getStatusCode()}"
            );
        }
    }

    public function test_customer_cannot_view_others_orders(): void
    {
        $owner = User::factory()->create(['user_type' => 'customer']);
        $intruder = User::factory()->create(['user_type' => 'customer']);
        $owner->assignRole('customer');
        $intruder->assignRole('customer');

        $order = Order::create([
            'user_id' => $owner->id,
            'code' => 'SEC-'.uniqid(),
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
            'grand_total' => 50000,
        ]);

        $response = $this->actingAs($intruder)->get('/account/orders/'.$order->id);
        $response->assertForbidden();

        $response = $this->actingAs($owner)->get('/account/orders/'.$order->id);
        $response->assertOk();
    }

    public function test_webhook_invalid_signature_returns_403(): void
    {
        $gateway = PaymentGatewayConfig::create([
            'name' => 'Midtrans Tes',
            'gateway_format' => 'midtrans-snap',
            'api_key_encrypted' => 'TEST-SERVER-KEY',
            'is_active' => true,
            'is_sandbox' => true,
        ]);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson('/webhooks/payment/'.$gateway->id, [
                'order_id' => 'FAKE-1',
                'status_code' => '200',
                'gross_amount' => '10000.00',
                'transaction_status' => 'settlement',
                'signature_key' => 'tampered',
            ]);

        $response->assertForbidden();
    }

    public function test_webhook_unknown_gateway_returns_404(): void
    {
        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson('/webhooks/payment/999999', ['order_id' => 'X']);

        $response->assertNotFound();
    }

    public function test_checkout_requires_login(): void
    {
        $response = $this->get('/checkout');
        $response->assertRedirect('/login');

        $response = $this->post('/checkout', []);
        $response->assertRedirect('/login');
    }

    public function test_account_views_render_for_customer(): void
    {
        $user = User::factory()->create(['user_type' => 'customer']);
        $user->assignRole('customer');

        foreach (['/account', '/account/orders', '/account/wishlist', '/account/profile'] as $url) {
            $response = $this->actingAs($user)->get($url);
            $response->assertOk("Customer view {$url} failed");
        }
    }
}
