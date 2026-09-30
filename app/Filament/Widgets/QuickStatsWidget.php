<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuickStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $pendingOrders = Order::where('delivery_status', 'pending')->count();
        $totalCustomers = User::where('user_type', 'customer')->count();
        $lowStock = Product::published()->approved()
            ->whereHas('stocks', fn($q) => $q->where('qty', '>', 0)->where('qty', '<=', 10))->count();
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)->count();

        return [
            Stat::make('Pesanan Pending', $pendingOrders)
                ->description('Menunggu diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'danger' : 'gray'),
            Stat::make('Pesanan Bulan Ini', $ordersThisMonth)
                ->description(date('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('Total Pelanggan', $totalCustomers)
                ->description('Terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Stok Menipis', $lowStock)
                ->description('Produk ≤ 10 unit')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStock > 0 ? 'warning' : 'gray'),
        ];
    }
}
