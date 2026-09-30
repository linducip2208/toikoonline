<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\ReportService;
use Filament\Widgets\ChartWidget;

class OrderChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan';

    protected static ?int $sort = 2;

    public ?string $filter = '30d';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari ini',
            'yesterday' => 'Kemarin',
            '7d' => '7 hari',
            '30d' => '30 hari',
            '90d' => '90 hari',
            'year' => 'Tahun ini',
        ];
    }

    protected function getData(): array
    {
        [$start, $end] = ReportService::resolveRange($this->filter ?? '30d');
        $series = ReportService::revenueByDay($start, $end);

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $series['data'],
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(99,102,241,.1)',
                    'borderColor' => '#6366f1',
                ],
            ],
            'labels' => $series['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
