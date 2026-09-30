<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\ReportService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        [$start, $end] = ReportService::resolveRange('30d');

        $revenue = ReportService::revenueTotal($start, $end);
        $orders = ReportService::orderCount($start, $end);
        $customers = ReportService::customerCount($start, $end);
        $aov = ReportService::averageOrderValue($start, $end);
        $refunds = ReportService::refundsTotal($start, $end);
        $pending = ReportService::ordersByDeliveryStatus($start, $end)['pending'] ?? 0;

        $trend = ReportService::revenueByDay($start, $end)['data'];

        return [
            Stat::make('Pendapatan (30 hari)', 'Rp '.number_format($revenue, 0, ',', '.'))
                ->description('Pesanan dibayar')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($trend),

            Stat::make('Pesanan (30 hari)', $orders)
                ->description('Total pesanan masuk')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),

            Stat::make('Pelanggan Baru (30 hari)', $customers)
                ->description('Pendaftar baru')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Rata-rata Order (AOV)', 'Rp '.number_format($aov, 0, ',', '.'))
                ->description('30 hari terakhir')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),

            Stat::make('Refund (30 hari)', 'Rp '.number_format($refunds, 0, ',', '.'))
                ->description($pending.' pesanan pending')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color($refunds > 0 ? 'danger' : 'gray'),
        ];
    }
}
