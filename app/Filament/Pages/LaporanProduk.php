<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Category;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class LaporanProduk extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.laporan-produk';
    protected static ?string $title = 'Laporan Produk';

    public ?array $data = [];
    public $reportData = [];

    public function mount(): void
    {
        $this->form->fill(['category_id' => null]);
        $this->loadReport();
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Filter')->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->options(Category::pluck('name', 'id'))
                    ->placeholder('Semua Kategori')
                    ->searchable(),
            ]),
        ])->statePath('data');
    }

    public function loadReport(): void
    {
        $state = $this->form->getState();

        $products = Product::published()->approved();
        if (!empty($state['category_id'])) {
            $products->whereHas('categories', fn($q) => $q->where('categories.id', $state['category_id']));
        }

        $this->reportData = [
            'total_products' => Product::count(),
            'published' => Product::published()->count(),
            'out_of_stock' => Product::published()->approved()
                ->whereHas('stocks', fn($q) => $q->where('qty', '<=', 0))->count(),
            'low_stock' => Product::published()->approved()
                ->whereHas('stocks', fn($q) => $q->where('qty', '>', 0)->where('qty', '<=', 10))->count(),
            'top_sellers' => Product::published()->approved()->orderBy('num_of_sale', 'desc')->take(10)->get(),
            'top_rated' => Product::published()->approved()->orderBy('rating', 'desc')->take(10)->get(),
            'by_category' => Category::withCount(['products' => fn($q) => $q->published()->approved()])
                ->having('products_count', '>', 0)->orderByDesc('products_count')->get(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('filter')->label('Filter')->icon('heroicon-o-funnel')->action('loadReport'),
        ];
    }
}
