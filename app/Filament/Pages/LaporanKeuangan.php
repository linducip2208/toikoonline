<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\DB;

class LaporanKeuangan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.laporan-keuangan';
    protected static ?string $title = 'Laporan Keuangan';

    public ?array $data = [];
    public $reportData = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]);
        $this->loadReport();
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Filter')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\DatePicker::make('date_from')->label('Dari')->native(false),
                    Forms\Components\DatePicker::make('date_to')->label('Sampai')->native(false),
                ]),
            ]),
        ])->statePath('data');
    }

    public function loadReport(): void
    {
        $state = $this->form->getState();
        $from = Carbon::parse($state['date_from'] ?? now()->startOfMonth())->startOfDay();
        $to = Carbon::parse($state['date_to'] ?? now())->endOfDay();

        $paid = Order::where('payment_status', 'paid')->whereBetween('created_at', [$from, $to]);
        $totalRevenue = $paid->sum('grand_total');

        $this->reportData = [
            'total_revenue' => $totalRevenue,
            'total_orders' => Order::whereBetween('created_at', [$from, $to])->count(),
            'paid_amount' => $totalRevenue,
            'unpaid_amount' => Order::where('payment_status', 'unpaid')->whereBetween('created_at', [$from, $to])->sum('grand_total'),
            'avg_order' => $paid->avg('grand_total') ?? 0,
            'wallet_transactions' => Transaction::whereBetween('created_at', [$from, $to])->count(),
            'monthly_sales' => Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$from, $to])
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(grand_total) as total")
                ->groupBy('month')->orderBy('month')->get(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('filter')->label('Tampilkan')->icon('heroicon-o-funnel')->action('loadReport'),
            \Filament\Actions\Action::make('download')->label('Download PDF')->icon('heroicon-o-document-arrow-down')->color('gray'),
        ];
    }
}
