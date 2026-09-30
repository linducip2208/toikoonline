<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use App\Services\Analytics\ReportService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuickStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        [$start, $end] = ReportService::resolveRange('30d');

        $pendingOrders = Order::where('delivery_status', 'pending')->count();
        $totalCustomers = User::where('user_type', 'customer')->count();
        $lowStock = ReportService::lowStockCount();
        $ordersThisMonth = ReportService::orderCount($start, $end);

        return [
            Stat::make('Pesanan Pending', $pendingOrders)
                ->description('Menunggu diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'danger' : 'gray'),
            Stat::make('Pesanan 30 Hari', $ordersThisMonth)
                ->description($start->format('d M').' – '.$end->format('d M Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('Total Pelanggan', $totalCustomers)
                ->description('Terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Stok Menipis', $lowStock)
                ->description('Varian ≤ ambang batas')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStock > 0 ? 'warning' : 'gray'),
        ];
    }
}
