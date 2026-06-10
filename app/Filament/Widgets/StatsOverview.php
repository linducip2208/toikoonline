<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Produk', Product::count())
                ->description('Produk aktif')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Total Pesanan', Order::count())
                ->description('Semua pesanan')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),

            Stat::make('Total Pelanggan', User::count())
                ->description('Pelanggan terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Pendapatan', 'Rp ' . number_format(Order::where('payment_status', 'paid')->sum('grand_total'), 0, ',', '.'))
                ->description('Pesanan selesai')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
