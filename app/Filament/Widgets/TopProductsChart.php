<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class TopProductsChart extends ChartWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = '10 Produk Terlaris';
    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $products = Product::published()->approved()->orderBy('num_of_sale', 'desc')->take(10)->get();

        return [
            'datasets' => [
                [
                    'label' => 'Terjual',
                    'data' => $products->pluck('num_of_sale')->toArray(),
                    'backgroundColor' => '#6366f1',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $products->pluck('name')->map(fn($n) => \Illuminate\Support\Str::limit($n, 20))->toArray(),
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
