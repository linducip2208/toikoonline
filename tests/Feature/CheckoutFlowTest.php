<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\DeliveryHistory;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PaymentGatewayConfig;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        // License pairing is an environment concern, not checkout behavior.
        $this->withoutMiddleware(\App\Http\Middleware\RequirePair::class);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $this->user = User::factory()->create(['user_type' => 'customer']);
        $this->user->assignRole('customer');

        $category = Category::create(['name' => 'Tes Kat', 'slug' => 'tes-kat-'.uniqid()]);
        $this->product = Product::create([
            'name' => 'Produk Tes',
            'slug' => 'produk-tes-'.uniqid(),
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'unit_price' => 100000,
        ]);
        ProductStock::create([
            'product_id' => $this->product->id,
            'variant' => '',
            'price' => 100000,
            'qty' => 10,
        ]);
    }

    protected function addCartItem(int $qty = 2): void
    {
        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'price' => 100000,
            'tax' => 0,
            'shipping_cost' => 0,
            'quantity' => $qty,
        ]);
    }

    protected function checkoutPayload(): array
    {
        return [
            'shipping_address' => ['name' => 'Budi', 'phone' => '08123456789', 'address' => 'Jl. Tes 1'],
            'payment_type' => 'cod',
            'shipping_cost' => 0,
        ];
    }

    public function test_checkout_requires_valid_input(): void
    {
        $this->addCartItem();

        $response = $this->actingAs($this->user)->post('/checkout', []);

        $response->assertSessionHasErrors(['shipping_address', 'payment_type']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rejects_insufficient_stock(): void
    {
        $this->addCartItem(999);

        $response = $this->actingAs($this->user)->post('/checkout', $this->checkoutPayload());

        $response->assertRedirect();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_creates_order_and_clears_cart(): void
    {
        $this->addCartItem();

        $response = $this->actingAs($this->user)->post('/checkout', $this->checkoutPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
        ]);
        $this->assertSame(0, Cart::where('user_id', $this->user->id)->count());
        $order = Order::where('user_id', $this->user->id)->first();
        $this->assertEquals(200000, (float) $order->grand_total);
        $this->assertEquals(2, $this->product->fresh()->num_of_sale);
    }

    public function test_checkout_empty_cart_redirects(): void
    {
        $response = $this->actingAs($this->user)->post('/checkout', $this->checkoutPayload());

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseCount('orders', 0);
    }

    protected function makeSnapGateway(): PaymentGatewayConfig
    {
        return PaymentGatewayConfig::create([
            'name' => 'Midtrans Tes',
            'gateway_format' => 'midtrans-snap',
            'api_key_encrypted' => 'TEST-SERVER-KEY',
            'is_active' => true,
            'is_sandbox' => true,
        ]);
    }

    protected function snapPayload(Order $order, string $status = 'settlement'): array
    {
        $payload = [
            'order_id' => $order->code,
            'status_code' => '200',
            'gross_amount' => '200000.00',
            'transaction_status' => $status,
        ];
        $payload['signature_key'] = hash('sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'TEST-SERVER-KEY');

        return $payload;
    }

    public function test_midtrans_webhook_marks_order_paid_with_valid_signature(): void
    {
        $gateway = $this->makeSnapGateway();

        $order = Order::create([
            'user_id' => $this->user->id,
            'code' => 'PAY-'.uniqid(),
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
            'grand_total' => 200000,
        ]);
        $detail = OrderDetail::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'price' => 100000,
            'quantity' => 2,
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
        ]);
        // Realistic precondition: admin already confirmed the order, so the
        // webhook's firstOrCreate timeline lookup hits an existing row.
        DeliveryHistory::create([
            'order_id' => $order->id,
            'order_detail_id' => $detail->id,
            'delivery_status' => 'confirmed',
            'status' => 'Pesanan dikonfirmasi',
        ]);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson('/webhooks/payment/'.$gateway->id, $this->snapPayload($order));

        $response->assertOk()->assertJson(['status' => 'ok']);
        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_midtrans_webhook_rejects_invalid_signature(): void
    {
        $gateway = $this->makeSnapGateway();

        $order = Order::create([
            'user_id' => $this->user->id,
            'code' => 'PAY-'.uniqid(),
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
            'grand_total' => 200000,
        ]);

        $payload = $this->snapPayload($order);
        $payload['signature_key'] = 'invalid-signature';

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson('/webhooks/payment/'.$gateway->id, $payload);

        $response->assertForbidden();
        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }
}
