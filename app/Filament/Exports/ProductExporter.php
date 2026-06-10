<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nama Produk'),
            ExportColumn::make('category.name')->label('Kategori'),
            ExportColumn::make('brand.name')->label('Brand'),
            ExportColumn::make('unit_price')->label('Harga'),
            ExportColumn::make('discount')->label('Diskon'),
            ExportColumn::make('slug')->label('Slug'),
            ExportColumn::make('published')->label('Published'),
        ];
    }
}
