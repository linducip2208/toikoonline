<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\ReportService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class TopProductsChart extends ChartWidget
{
    protected static ?int $sort = 6;
    protected static ?string $heading = '10 Produk Terlaris';
    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $products = ReportService::topProducts(10);

        return [
            'datasets' => [
                [
                    'label' => 'Terjual',
                    'data' => array_column($products, 'sold'),
                    'backgroundColor' => '#6366f1',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => array_map(fn ($p) => Str::limit($p['name'], 20), $products),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => ['x' => ['beginAtZero' => true]],
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}
