<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // License pairing is an environment concern, not storefront behavior.
        $this->withoutMiddleware(\App\Http\Middleware\RequirePair::class);

        foreach (['super_admin', 'admin', 'customer', 'staff'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_homepage_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_products_page_loads(): void
    {
        $response = $this->get('/products');
        $response->assertStatus(200);
    }

    public function test_search_works(): void
    {
        $response = $this->get('/search?q=samsung');
        $response->assertStatus(200);
    }

    public function test_blog_page_loads(): void
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
    }

    public function test_sitemap_returns_xml(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_register_page_loads(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_admin_login_page_loads(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_auth_user_can_access_customer_dashboard(): void
    {
        $user = \App\Models\User::factory()->create(['user_type' => 'customer']);
        $user->assignRole('customer');

        $response = $this->actingAs($user)->get('/account');
        $response->assertStatus(200);
    }
}
