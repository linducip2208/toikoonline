<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DeliveryBoyStats extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->user_type === 'delivery_boy' || Auth::user()?->hasRole('admin');
    }

    protected function getStats(): array
    {
        $deliveryBoyId = Auth::id();

        return [
            Stat::make('Pesanan Ditugaskan', Order::where('assign_delivery_boy', $deliveryBoyId)->count())
                ->description('Total pesanan untuk Anda')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Terkirim Hari Ini', Order::where('assign_delivery_boy', $deliveryBoyId)
                ->where('delivery_status', 'delivered')
                ->whereDate('updated_at', today())
                ->count())
                ->description('Pengiriman sukses hari ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Menunggu Pickup', Order::where('assign_delivery_boy', $deliveryBoyId)
                ->where('delivery_status', 'pending')
                ->count())
                ->description('Siap diambil')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Estimasi Pendapatan', 'Rp ' . number_format(Order::where('assign_delivery_boy', $deliveryBoyId)
                ->where('delivery_status', 'delivered')
                ->sum('grand_total'), 0, ',', '.'))
                ->description('Dari pesanan terkirim')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
