<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\ReportService;
use Filament\Widgets\ChartWidget;

class PaymentStatusChart extends ChartWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = 'Status Pembayaran';
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
        $statuses = ReportService::ordersByPaymentStatus($start, $end);

        $labels = ['paid' => 'Dibayar', 'unpaid' => 'Belum bayar', 'refunded' => 'Refund'];

        return [
            'labels' => array_values($labels),
            'datasets' => [
                [
                    'label' => 'Pesanan',
                    'data' => array_map(fn ($k) => $statuses[$k] ?? 0, array_keys($labels)),
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'],
                ],
            ],
        ];
    }
}
