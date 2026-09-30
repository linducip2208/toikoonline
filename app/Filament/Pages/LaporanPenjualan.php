<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\DB;

class LaporanPenjualan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.laporan-penjualan';
    protected static ?string $title = 'Laporan Penjualan';

    public ?array $data = [];
    public $reportData = [];
    public $dateLabel = '30 Hari Terakhir';

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->subDays(30)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'group_by' => 'daily',
        ]);
        $this->loadReport();
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Filter Laporan')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\DatePicker::make('date_from')->label('Dari')->native(false),
                    Forms\Components\DatePicker::make('date_to')->label('Sampai')->native(false),
                    Forms\Components\Select::make('group_by')->label('Kelompok')->options([
                        'daily' => 'Harian',
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                    ])->default('daily'),
                ]),
            ]),
        ])->statePath('data');
    }

    public function loadReport(): void
    {
        $state = $this->form->getState();
        $from = Carbon::parse($state['date_from'] ?? now()->subDays(30))->startOfDay();
        $to = Carbon::parse($state['date_to'] ?? now())->endOfDay();
        $group = $state['group_by'] ?? 'daily';

        $format = match ($group) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $labelFormat = match ($group) {
            'weekly' => 'Minggu ke-%u %Y',
            'monthly' => '%b %Y',
            default => '%d %b',
        };

        $sales = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as period, SUM(grand_total) as total, COUNT(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $this->dateLabel = $from->format('d M Y') . ' — ' . $to->format('d M Y');

        $this->reportData = [
            'total_revenue' => Order::where('payment_status', 'paid')->whereBetween('created_at', [$from, $to])->sum('grand_total'),
            'total_orders' => Order::whereBetween('created_at', [$from, $to])->count(),
            'paid_orders' => Order::where('payment_status', 'paid')->whereBetween('created_at', [$from, $to])->count(),
            'pending_orders' => Order::where('payment_status', 'unpaid')->whereBetween('created_at', [$from, $to])->count(),
            'avg_order' => Order::where('payment_status', 'paid')->whereBetween('created_at', [$from, $to])->avg('grand_total') ?? 0,
            'labels' => $sales->pluck('period')->map(fn($p) => Carbon::createFromFormat(match($group) {
                'weekly' => 'Y-W', 'monthly' => 'Y-m', default => 'Y-m-d'
            }, $p)->format($labelFormat))->toArray(),
            'totals' => $sales->pluck('total')->toArray(),
            'counts' => $sales->pluck('count')->toArray(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('filter')
                ->label('Tampilkan')
                ->icon('heroicon-o-funnel')
                ->action('loadReport'),
            \Filament\Actions\Action::make('download')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray'),
        ];
    }
}
