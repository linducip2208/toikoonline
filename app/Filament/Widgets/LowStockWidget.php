<?php

namespace App\Filament\Widgets;

use App\Models\ProductStock;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
    protected static ?string $heading = 'Stok Menipis';

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductStock::query()
                    ->join('products', 'products.id', '=', 'product_stocks.product_id')
                    ->whereRaw('product_stocks.qty <= COALESCE(products.low_stock_qty, 10)')
                    ->orderBy('product_stocks.qty')
                    ->select('product_stocks.*')
            )
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->limit(30)
                    ->default('-'),
                TextColumn::make('variant')
                    ->label('Varian')
                    ->limit(15)
                    ->default('-'),
                TextColumn::make('qty')
                    ->label('Sisa')
                    ->badge()
                    ->color(fn ($state) => $state <= 0 ? 'danger' : 'warning'),
            ])
            ->emptyStateHeading('Stok aman')
            ->emptyStateDescription('Tidak ada produk dengan stok menipis.')
            ->paginated([5, 10]);
    }
}
