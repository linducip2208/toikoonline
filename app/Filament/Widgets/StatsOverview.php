<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('grand_total');
        $ordersToday = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('delivery_status', 'pending')->count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Pesanan dibayar')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getRevenueTrend()),

            Stat::make('Pesanan Hari Ini', $ordersToday)
                ->description(date('l, d M Y'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),

            Stat::make('Total Produk', Product::count())
                ->description(Product::where('published', true)->count() . ' published')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),

            Stat::make('Pelanggan', User::where('user_type', 'customer')->count())
                ->description('Terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Menunggu Diproses', $pendingOrders)
                ->description('Pesanan pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'danger' : 'gray'),
        ];
    }

    protected function getRevenueTrend(): array
    {
        return Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total')
            ->toArray();
    }
}
