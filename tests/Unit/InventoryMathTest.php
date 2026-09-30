<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use App\Services\Inventory\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryMathTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;
    protected ProductStock $stock;

    protected function setUp(): void
    {
        parent::setUp();

        if (! class_exists(InventoryService::class)) {
            $this->markTestSkipped('Agent 2 inventory service not present at read time — no fake assertions.');
        }

        $user = User::factory()->create(['user_type' => 'admin']);
        $category = Category::create(['name' => 'Inv Kat', 'slug' => 'inv-kat-'.uniqid()]);
        $this->product = Product::create([
            'name' => 'Produk Inv',
            'slug' => 'produk-inv-'.uniqid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'unit_price' => 50000,
        ]);
        $this->stock = ProductStock::create([
            'product_id' => $this->product->id,
            'variant' => '',
            'price' => 50000,
            'qty' => 10,
        ]);
    }

    public function test_available_qty_sums_stocks(): void
    {
        $this->assertSame(10, app(InventoryService::class)->availableQty($this->product->id));
    }

    public function test_receive_increases_stock_and_records_movement(): void
    {
        $movement = app(InventoryService::class)->receive($this->product->id, $this->stock->id, 5);

        $this->assertSame(15, $this->stock->fresh()->qty);
        $this->assertSame('receive', $movement->type);
        $this->assertSame(5, $movement->qty);
        $this->assertSame(15, $movement->qty_after);
    }

    public function test_reserve_decreases_stock(): void
    {
        $movement = app(InventoryService::class)->reserve($this->product->id, $this->stock->id, 4);

        $this->assertSame(6, $this->stock->fresh()->qty);
        $this->assertSame(-4, $movement->qty);
    }

    public function test_reserve_more_than_available_throws(): void
    {
        $this->expectException(ValidationException::class);

        app(InventoryService::class)->reserve($this->product->id, $this->stock->id, 999);
    }

    public function test_release_restores_stock(): void
    {
        $svc = app(InventoryService::class);
        $svc->reserve($this->product->id, $this->stock->id, 4);
        $svc->release($this->product->id, $this->stock->id, 4);

        $this->assertSame(10, $this->stock->fresh()->qty);
    }

    public function test_commit_decreases_stock(): void
    {
        $svc = app(InventoryService::class);
        $svc->reserve($this->product->id, $this->stock->id, 3);
        $movement = $svc->commit($this->product->id, $this->stock->id, 3);

        $this->assertSame(4, $this->stock->fresh()->qty);
        $this->assertSame('commit', $movement->type);
    }

    public function test_adjust_below_zero_throws_and_keeps_qty(): void
    {
        try {
            app(InventoryService::class)->adjust($this->product->id, $this->stock->id, -999);
            $this->fail('Expected ValidationException.');
        } catch (ValidationException) {
            $this->assertSame(10, $this->stock->fresh()->qty);
        }
    }
}
