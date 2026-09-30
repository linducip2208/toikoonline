<?php

namespace App\Services\Analytics;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Single source of truth for dashboard numbers.
 * All Filament widgets + AnalyticsDashboard page MUST use this class
 * so identical filters always produce identical numbers.
 *
 * Every method returns 0 / [] on empty DB — never throws for missing data.
 */
class ReportService
{
    public const PRESETS = ['today', 'yesterday', '7d', '30d', '90d', 'year', 'custom'];

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function resolveRange(string $preset, ?string $from = null, ?string $to = null): array
    {
        $today = Carbon::today();

        return match ($preset) {
            'today' => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
            'yesterday' => [$today->copy()->subDay()->startOfDay(), $today->copy()->subDay()->endOfDay()],
            '7d' => [$today->copy()->subDays(6)->startOfDay(), $today->copy()->endOfDay()],
            '30d' => [$today->copy()->subDays(29)->startOfDay(), $today->copy()->endOfDay()],
            '90d' => [$today->copy()->subDays(89)->startOfDay(), $today->copy()->endOfDay()],
            'year' => [$today->copy()->startOfYear(), $today->copy()->endOfDay()],
            'custom' => [
                $from ? Carbon::parse($from)->startOfDay() : $today->copy()->subDays(29)->startOfDay(),
                $to ? Carbon::parse($to)->endOfDay() : $today->copy()->endOfDay(),
            ],
            default => [$today->copy()->subDays(29)->startOfDay(), $today->copy()->endOfDay()],
        };
    }

    protected static function inRange($query, Carbon $start, Carbon $end, string $column = 'created_at')
    {
        return $query->whereBetween($column, [$start, $end]);
    }

    public static function revenueTotal(Carbon $start, Carbon $end): float
    {
        return (float) self::inRange(Order::where('payment_status', 'paid'), $start, $end)->sum('grand_total');
    }

    public static function orderCount(Carbon $start, Carbon $end): int
    {
        return self::inRange(Order::query(), $start, $end)->count();
    }

    public static function customerCount(Carbon $start, Carbon $end): int
    {
        return self::inRange(User::where('user_type', 'customer'), $start, $end)->count();
    }

    public static function averageOrderValue(Carbon $start, Carbon $end): float
    {
        $paid = self::inRange(Order::where('payment_status', 'paid'), $start, $end);
        $count = (clone $paid)->count();
        if ($count === 0) {
            return 0;
        }

        return (float) (clone $paid)->sum('grand_total') / $count;
    }

    public static function refundsTotal(Carbon $start, Carbon $end): float
    {
        return (float) self::inRange(Order::where('payment_status', 'refunded'), $start, $end)->sum('grand_total');
    }

    /**
     * Paid revenue grouped per day, zero-filled for days without sales.
     *
     * @return array{labels: string[], data: float[]}
     */
    public static function revenueByDay(Carbon $start, Carbon $end): array
    {
        $rows = self::inRange(Order::where('payment_status', 'paid'), $start, $end)
            ->selectRaw('DATE(created_at) as d, SUM(grand_total) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('d');

        $labels = [];
        $data = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('d M');
            $data[] = (float) ($rows[$key]->total ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * @return array<string, int> keyed by raw status value
     */
    public static function ordersByDeliveryStatus(Carbon $start, Carbon $end): array
    {
        return self::inRange(Order::query(), $start, $end)
            ->select('delivery_status', DB::raw('count(*) as c'))
            ->groupBy('delivery_status')
            ->get()
            ->pluck('c', 'delivery_status')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /**
     * @return array<string, int> keyed by raw status value
     */
    public static function ordersByPaymentStatus(Carbon $start, Carbon $end): array
    {
        return self::inRange(Order::query(), $start, $end)
            ->select('payment_status', DB::raw('count(*) as c'))
            ->groupBy('payment_status')
            ->get()
            ->pluck('c', 'payment_status')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    public static function topProducts(int $limit = 10, ?Carbon $start = null, ?Carbon $end = null): array
    {
        if ($start && $end && Schema::hasTable('order_details')) {
            $rows = OrderDetail::query()
                ->join('orders', 'orders.id', '=', 'order_details.order_id')
                ->join('products', 'products.id', '=', 'order_details.product_id')
                ->whereBetween('orders.created_at', [$start, $end])
                ->select('products.id', 'products.name', DB::raw('SUM(order_details.quantity) as sold'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc(DB::raw('SUM(order_details.quantity)'))
                ->limit($limit)
                ->get();

            if ($rows->isNotEmpty()) {
                return $rows->map(fn ($r) => [
                    'id' => $r->id, 'name' => $r->name, 'sold' => (int) $r->sold,
                ])->all();
            }
        }

        return Product::orderBy('num_of_sale', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'num_of_sale'])
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'sold' => (int) $p->num_of_sale])
            ->all();
    }

    public static function topCategories(int $limit = 10, ?Carbon $start = null, ?Carbon $end = null): array
    {
        if (! Schema::hasTable('product_categories')) {
            return [];
        }

        $query = OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('product_categories', 'product_categories.product_id', '=', 'order_details.product_id')
            ->join('categories', 'categories.id', '=', 'product_categories.category_id')
            ->select('categories.id', 'categories.name', DB::raw('SUM(order_details.quantity) as sold'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc(DB::raw('SUM(order_details.quantity)'))
            ->limit($limit);

        if ($start && $end) {
            $query->whereBetween('orders.created_at', [$start, $end]);
        }

        return $query->get()
            ->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'sold' => (int) $r->sold])
            ->all();
    }

    public static function lowStockProducts(int $limit = 10, int $threshold = 10): array
    {
        return ProductStock::query()
            ->join('products', 'products.id', '=', 'product_stocks.product_id')
            ->whereRaw('product_stocks.qty <= COALESCE(products.low_stock_qty, '.$threshold.')')
            ->orderBy('product_stocks.qty')
            ->limit($limit)
            ->get(['product_stocks.id', 'product_stocks.product_id', 'product_stocks.variant', 'product_stocks.sku', 'product_stocks.qty', 'products.name as product_name'])
            ->map(fn ($r) => [
                'id' => $r->id,
                'product_id' => $r->product_id,
                'product' => $r->product_name,
                'variant' => $r->variant,
                'sku' => $r->sku,
                'qty' => (int) $r->qty,
            ])
            ->all();
    }

    public static function lowStockCount(int $threshold = 10): int
    {
        return ProductStock::query()
            ->join('products', 'products.id', '=', 'product_stocks.product_id')
            ->whereRaw('product_stocks.qty <= COALESCE(products.low_stock_qty, '.$threshold.')')
            ->count();
    }

    /**
     * Carts not checked out for >24h with a live product relation.
     */
    public static function abandonedCarts(int $limit = 10): array
    {
        return Cart::query()
            ->with('product:id,name')
            ->where('updated_at', '<', now()->subDay())
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'user_id' => $c->user_id,
                'product' => $c->product?->name ?? 'Produk dihapus',
                'quantity' => (int) $c->quantity,
                'updated_at' => $c->updated_at?->format('d M Y H:i'),
            ])
            ->all();
    }

    public static function abandonedCartCount(): int
    {
        return Cart::where('updated_at', '<', now()->subDay())->count();
    }
}
