<?php

namespace Tests\Feature;

use App\Http\Middleware\RequirePair;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Address;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Cart;
use App\Models\Category;
use App\Models\ClubPointDetail;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Page;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Agent 4 end-to-end HTTP journeys (sqlite, no browser).
 *
 * Route registration: routes/*.php are owned by other agents, so the
 * Agent-4-documented routes are registered locally here with the EXACT same
 * URIs/names for HTTP coverage. See docs/INSTALLATION.md + docs wiring notes
 * for the production route lines the integrator must append to routes/web.php.
 */
class JourneyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(RequirePair::class);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Agent-4 routes, mirrored 1:1 with the documented production lines.
        Route::middleware('web')->group(function () {
            Route::get('/contact', [\App\Http\Controllers\Storefront\ContactController::class, 'show'])->name('contact.show');
            Route::post('/contact', [\App\Http\Controllers\Storefront\ContactController::class, 'store'])->name('contact.store');
            Route::get('/faq', [\App\Http\Controllers\Storefront\FaqController::class, 'index'])->name('faq.index');
            Route::get('/offline', fn () => view('pwa.offline'))->name('pwa.offline');
            Route::middleware(['auth'])->prefix('account')->name('customer.')->group(function () {
                Route::get('/addresses', [\App\Http\Controllers\Customer\AddressController::class, 'index'])->name('addresses');
                Route::get('/addresses/create', [\App\Http\Controllers\Customer\AddressController::class, 'create'])->name('addresses.create');
                Route::post('/addresses', [\App\Http\Controllers\Customer\AddressController::class, 'store'])->name('addresses.store');
                Route::get('/addresses/{address}/edit', [\App\Http\Controllers\Customer\AddressController::class, 'edit'])->name('addresses.edit');
                Route::put('/addresses/{address}', [\App\Http\Controllers\Customer\AddressController::class, 'update'])->name('addresses.update');
                Route::delete('/addresses/{address}', [\App\Http\Controllers\Customer\AddressController::class, 'destroy'])->name('addresses.destroy');
                Route::post('/addresses/{address}/default', [\App\Http\Controllers\Customer\AddressController::class, 'setDefault'])->name('addresses.default');
            });
            Route::prefix('install')->name('install.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Install\InstallController::class, 'index'])->name('index');
            });
        });
    }

    protected function seedProduct(int $price = 100000, int $stock = 10): Product
    {
        $seller = User::factory()->create(['user_type' => 'seller']);
        $category = Category::create(['name' => 'Kat Uji', 'slug' => 'kat-uji-'.uniqid()]);
        $product = Product::create([
            'name' => 'Produk Journey',
            'slug' => 'produk-journey-'.uniqid(),
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'unit_price' => $price,
            'published' => true,
            'approved' => true,
        ]);
        ProductStock::create(['product_id' => $product->id, 'variant' => '', 'price' => $price, 'qty' => $stock]);

        return $product;
    }

    public function test_register_then_login_journey(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pelanggan Baru',
            'email' => 'baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect('/account');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'baru@example.com', 'user_type' => 'customer']);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();

        $this->post('/login', ['email' => 'baru@example.com', 'password' => 'password123'])
            ->assertRedirect('/account');
        $this->assertAuthenticated();
    }

    public function test_purchase_receive_refund_approve_journey(): void
    {
        $product = $this->seedProduct();
        $user = User::factory()->create(['user_type' => 'customer']);
        $user->assignRole('customer');

        // cart add (real HTTP)
        $this->actingAs($user)->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 100000,
        ])->assertRedirect();
        $this->assertSame(1, Cart::where('user_id', $user->id)->count());

        // checkout COD (real HTTP, no gateway)
        $this->actingAs($user)->post('/checkout', [
            'shipping_address' => ['name' => 'Budi', 'phone' => '08123456789', 'address' => 'Jl. Tes 1'],
            'payment_type' => 'cod',
            'shipping_cost' => 0,
        ])->assertRedirect();

        $order = Order::where('user_id', $user->id)->firstOrFail();
        // 200000 barang + 5000 fee COD (config/payment.php) — fee ikut ditagih.
        $this->assertEquals(205000, (float) $order->grand_total);
        $this->assertEquals(5000, (float) $order->payment_fee);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('pending', $order->delivery_status);
        $this->assertSame(0, Cart::where('user_id', $user->id)->count());

        // receive (must be on delivery first)
        $order->update(['delivery_status' => 'on_delivery']);
        $this->actingAs($user)->post("/account/orders/{$order->id}/receive")->assertRedirect();
        $this->assertSame('delivered', $order->fresh()->delivery_status);

        // refund request (only paid orders)
        $order->update(['payment_status' => 'paid']);
        $this->actingAs($user)->post("/account/orders/{$order->id}/refund", [
            'refund_reason' => 'Barang cacat saat diterima, mohon refund sebagian.',
            'refund_amount' => 50000,
        ])->assertRedirect();
        $refund = RefundRequest::where('order_id', $order->id)->firstOrFail();
        $this->assertSame('pending', $refund->refund_status);

        // approve via real RefundService (Agent 2 contract, guarded)
        if (! class_exists(\App\Services\Refund\RefundService::class)) {
            $this->markTestSkipped('RefundService absent — refund stays pending (honest skip).');
        }
        $approved = app(\App\Services\Refund\RefundService::class)->approve($refund->fresh(), $user->id, 'Disetujui via journey test');
        $this->assertSame('refunded', $approved->refund_status);
        $this->assertContains($order->fresh()->payment_status, ['partially_refunded', 'refunded']);
    }

    public function test_addresses_crud_owner_checked(): void
    {
        $user = User::factory()->create(['user_type' => 'customer']);
        $other = User::factory()->create(['user_type' => 'customer']);

        // store + index render
        $this->actingAs($user)->post('/account/addresses', [
            'address' => 'Jl. Mawar No. 10',
            'postal_code' => '10110',
            'phone' => '0811111111',
            'set_default' => '1',
        ])->assertRedirect(route('customer.addresses'));
        $address = Address::where('user_id', $user->id)->firstOrFail();
        $this->assertTrue((bool) $address->set_default);

        $this->actingAs($user)->get('/account/addresses')->assertOk()->assertSee('Jl. Mawar No. 10');

        // second address becomes default, first unset
        $this->actingAs($user)->post('/account/addresses', [
            'address' => 'Jl. Melati No. 5',
            'postal_code' => '10220',
            'phone' => '0822222222',
            'set_default' => '1',
        ])->assertRedirect();
        $this->assertFalse((bool) $address->fresh()->set_default);

        // cross-user edit/update/destroy/default → 403, nothing changes
        $second = Address::where('user_id', $user->id)->where('address', 'Jl. Melati No. 5')->firstOrFail();
        $this->actingAs($other)->get("/account/addresses/{$second->id}/edit")->assertForbidden();
        $this->actingAs($other)->put("/account/addresses/{$second->id}", [
            'address' => 'HACK', 'phone' => '0800000000',
        ])->assertForbidden();
        $this->actingAs($other)->delete("/account/addresses/{$second->id}")->assertForbidden();
        $this->actingAs($other)->post("/account/addresses/{$second->id}/default")->assertForbidden();
        $this->assertSame('Jl. Melati No. 5', $second->fresh()->address);

        // owner update + set-default + destroy
        $this->actingAs($user)->put("/account/addresses/{$address->id}", [
            'address' => 'Jl. Mawar No. 11', 'phone' => '0811111111',
        ])->assertRedirect();
        $this->assertSame('Jl. Mawar No. 11', $address->fresh()->address);

        $this->actingAs($user)->post("/account/addresses/{$address->id}/default")->assertRedirect();
        $this->assertTrue((bool) $address->fresh()->set_default);

        $this->actingAs($user)->delete("/account/addresses/{$address->id}")->assertRedirect();
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_contact_form_creates_message_and_notifies_admin(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $admin->assignRole('super_admin');

        $this->get('/contact')->assertOk()->assertSee('Hubungi Kami');

        $this->post('/contact', [
            'name' => 'Siti',
            'email' => 'siti@example.com',
            'subject' => 'Tanya ongkir',
            'message' => 'Berapa ongkos kirim ke Bandung untuk 2kg?',
        ])->assertRedirect();

        $msg = ContactMessage::where('email', 'siti@example.com')->firstOrFail();
        $this->assertFalse((bool) $msg->is_read);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_faq_renders_from_cms_pages(): void
    {
        Page::create([
            'type' => 'faq',
            'title' => 'Bagaimana cara retur?',
            'slug' => 'faq-retur-'.uniqid(),
            'content' => '<p>Hubungi admin maksimal 7 hari.</p>',
            'status' => true,
        ]);

        $this->get('/faq')->assertOk()->assertSee('Bagaimana cara retur?');
    }

    public function test_cms_page_publish_renders_storefront(): void
    {
        $page = Page::create([
            'type' => 'info',
            'title' => 'Tentang Journey',
            'slug' => 'tentang-journey-'.uniqid(),
            'content' => '<p>Konten halaman CMS journey.</p>',
            'status' => true,
        ]);

        $this->get('/page/'.$page->slug)->assertOk()->assertSee('Tentang Journey');
    }

    public function test_i18n_session_locale_renders_with_fallback(): void
    {
        $product = $this->seedProduct();

        // English session locale renders the product page (fallback content ID OK).
        $this->withSession(['locale' => 'en'])->get('/products/'.$product->slug)
            ->assertOk()->assertSee($product->name);
        $this->assertSame('en', app()->getLocale());

        // Unknown locale falls back to default (id).
        $this->withSession(['locale' => 'xx'])->get('/products/'.$product->slug)->assertOk();
        $this->assertSame('id', app()->getLocale());
    }

    public function test_installer_index_renders_without_lock(): void
    {
        if (\App\Http\Middleware\InstallLock::isInstalled()) {
            $this->markTestSkipped('App already installed (lock present).');
        }
        $this->get('/install')->assertOk()->assertSee('Instalasi');
    }

    public function test_offline_page_renders(): void
    {
        $this->get('/offline')->assertOk()->assertSee('offline');
    }

    public function test_blog_renders_with_data(): void
    {
        $author = User::factory()->create();
        $cat = BlogCategory::create(['name' => 'Tips', 'slug' => 'tips-'.uniqid()]);
        Blog::create([
            'user_id' => $author->id,
            'category_id' => $cat->id,
            'title' => 'Artikel Journey',
            'slug' => 'artikel-journey-'.uniqid(),
            'content' => '<p>Isi artikel untuk verifikasi view blog.</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get('/blog')->assertOk()->assertSee('Artikel Journey');
    }

    protected function snapGateway(): PaymentGatewayConfig
    {
        return PaymentGatewayConfig::create([
            'name' => 'Midtrans Tes',
            'gateway_format' => 'midtrans-snap',
            'api_key_encrypted' => 'TEST-SERVER-KEY',
            'is_active' => true,
            'is_sandbox' => true,
        ]);
    }

    protected function snapPayload(Order $order): array
    {
        $payload = [
            'order_id' => $order->code,
            'status_code' => '200',
            'gross_amount' => '200000.00',
            'transaction_status' => 'settlement',
        ];
        $payload['signature_key'] = hash('sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'TEST-SERVER-KEY');

        return $payload;
    }

    public function test_paid_webhook_idempotent_single_points_row(): void
    {
        $gateway = $this->snapGateway();
        $user = User::factory()->create(['user_type' => 'customer']);
        $order = Order::create([
            'user_id' => $user->id,
            'code' => 'IDEM-'.uniqid(),
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
            'grand_total' => 200000,
        ]);

        $send = fn () => $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson('/webhooks/payment/'.$gateway->id, $this->snapPayload($order));

        $send()->assertOk()->assertJson(['status' => 'ok']);
        $send()->assertOk()->assertJson(['status' => 'ok']);

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame(1, ClubPointDetail::where('order_id', $order->id)->count());
        $this->assertSame(1, PaymentTransaction::where('idempotency_key', $order->code)->count());
    }
}
