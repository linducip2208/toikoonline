<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrderChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan 30 Hari Terakhir';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $orders = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $orders->pluck('total')->toArray(),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(99,102,241,.1)',
                    'borderColor' => '#6366f1',
                ],
            ],
            'labels' => $orders->pluck('date')->map(fn($d) => date('d M', strtotime($d)))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
