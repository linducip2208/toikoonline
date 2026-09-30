<?php

namespace App\Filament\Pages;

use App\Services\Analytics\ReportService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;

class AnalyticsDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.pages.analytics-dashboard';
    protected static ?string $title = 'Dasbor Analitik';
    protected static ?string $navigationLabel = 'Dasbor Analitik';

    public ?array $data = [];
    public array $stats = [];
    public array $topProducts = [];
    public array $topCategories = [];
    public array $lowStock = [];
    public array $daily = [];
    public string $dateLabel = '';

    public function mount(): void
    {
        $this->form->fill(['preset' => '30d', 'date_from' => null, 'date_to' => null]);
        $this->loadReport();
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Filter Periode')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Select::make('preset')
                        ->label('Periode')
                        ->options([
                            'today' => 'Hari ini',
                            'yesterday' => 'Kemarin',
                            '7d' => '7 hari terakhir',
                            '30d' => '30 hari terakhir',
                            '90d' => '90 hari terakhir',
                            'year' => 'Tahun ini',
                            'custom' => 'Kustom',
                        ])
                        ->default('30d')
                        ->live(),
                    Forms\Components\DatePicker::make('date_from')
                        ->label('Dari')
                        ->native(false)
                        ->visible(fn (Forms\Get $get) => $get('preset') === 'custom'),
                    Forms\Components\DatePicker::make('date_to')
                        ->label('Sampai')
                        ->native(false)
                        ->visible(fn (Forms\Get $get) => $get('preset') === 'custom'),
                ]),
            ]),
        ])->statePath('data');
    }

    public function loadReport(): void
    {
        $state = $this->form->getState();
        $preset = in_array($state['preset'] ?? '30d', ReportService::PRESETS, true) ? $state['preset'] : '30d';

        [$start, $end] = ReportService::resolveRange(
            $preset,
            isset($state['date_from']) ? (string) $state['date_from'] : null,
            isset($state['date_to']) ? (string) $state['date_to'] : null,
        );

        $revenue = ReportService::revenueTotal($start, $end);
        $orders = ReportService::orderCount($start, $end);

        $this->dateLabel = $start->format('d M Y').' — '.$end->format('d M Y');
        $this->stats = [
            'revenue' => $revenue,
            'orders' => $orders,
            'customers' => ReportService::customerCount($start, $end),
            'aov' => ReportService::averageOrderValue($start, $end),
            'refunds' => ReportService::refundsTotal($start, $end),
            'by_payment' => ReportService::ordersByPaymentStatus($start, $end),
            'by_delivery' => ReportService::ordersByDeliveryStatus($start, $end),
        ];
        $this->daily = ReportService::revenueByDay($start, $end);
        $this->topProducts = ReportService::topProducts(10, $start, $end);
        $this->topCategories = ReportService::topCategories(10, $start, $end);
        $this->lowStock = ReportService::lowStockProducts(10);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('filter')
                ->label('Tampilkan')
                ->icon('heroicon-o-funnel')
                ->action('loadReport'),
        ];
    }
}
