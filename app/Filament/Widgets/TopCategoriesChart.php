<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\ReportService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class TopCategoriesChart extends ChartWidget
{
    protected static ?int $sort = 7;
    protected static ?string $heading = 'Kategori Terlaris';
    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $cats = ReportService::topCategories(10);

        return [
            'datasets' => [
                [
                    'label' => 'Terjual',
                    'data' => array_column($cats, 'sold'),
                    'backgroundColor' => '#10b981',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => array_map(fn ($c) => Str::limit($c['name'], 20), $cats),
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
