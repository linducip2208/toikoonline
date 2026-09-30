<?php

namespace App\Filament\Widgets;

use App\Models\Cart;
use App\Services\Analytics\ReportService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AbandonedCartsWidget extends BaseWidget
{
    protected static ?string $heading = 'Keranjang Terlantar (>24 jam)';

    protected static ?int $sort = 9;

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        try {
            return ReportService::abandonedCartCount() > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Cart::query()
                    ->with('product:id,name')
                    ->where('updated_at', '<', now()->subDay())
                    ->latest('updated_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->limit(30)
                    ->default('Produk dihapus'),
                TextColumn::make('quantity')
                    ->label('Qty'),
                TextColumn::make('updated_at')
                    ->label('Terakhir update')
                    ->dateTime('d M Y H:i'),
            ])
            ->emptyStateHeading('Tidak ada keranjang terlantar')
            ->paginated([5, 10]);
    }
}
