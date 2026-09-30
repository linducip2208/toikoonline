<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\ReportService;
use Filament\Widgets\ChartWidget;

class OrderStatusChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Status Pengiriman';
    protected int|string|array $columnSpan = 1;

    public ?string $filter = '30d';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari ini',
            '7d' => '7 hari',
            '30d' => '30 hari',
            '90d' => '90 hari',
            'year' => 'Tahun ini',
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        [$start, $end] = ReportService::resolveRange($this->filter ?? '30d');
        $statuses = ReportService::ordersByDeliveryStatus($start, $end);

        $labels = ['pending' => 'Pending', 'confirmed' => 'Dikonfirmasi', 'picked_up' => 'Diproses', 'on_delivery' => 'Dikirim', 'delivered' => 'Terkirim', 'cancelled' => 'Dibatalkan'];

        return [
            'labels' => array_values($labels),
            'datasets' => [
                [
                    'label' => 'Pesanan',
                    'data' => array_map(fn ($k) => $statuses[$k] ?? 0, array_keys($labels)),
                    'backgroundColor' => ['#f59e0b', '#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#ef4444'],
                ],
            ],
        ];
    }
}
