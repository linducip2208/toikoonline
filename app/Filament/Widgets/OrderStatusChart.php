<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderStatusChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Status Pesanan';
    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $statuses = Order::select('delivery_status', DB::raw('count(*) as count'))
            ->groupBy('delivery_status')
            ->get()
            ->pluck('count', 'delivery_status');

        $labels = ['Pending' => 'Pending', 'confirmed' => 'Dikonfirmasi', 'picked_up' => 'Diproses', 'on_delivery' => 'Dikirim', 'delivered' => 'Terkirim', 'cancelled' => 'Dibatalkan'];

        return [
            'labels' => array_values($labels),
            'datasets' => [
                [
                    'label' => 'Pesanan',
                    'data' => array_map(fn($k) => $statuses[$k] ?? 0, array_keys($labels)),
                    'backgroundColor' => ['#f59e0b', '#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#ef4444'],
                ],
            ],
        ];
    }
}
