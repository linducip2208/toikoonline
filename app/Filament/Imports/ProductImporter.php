<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->requiredMapping()->label('Nama'),
            ImportColumn::make('category_id')->label('Kategori ID'),
            ImportColumn::make('brand_id')->label('Brand ID'),
            ImportColumn::make('unit_price')->numeric()->label('Harga'),
            ImportColumn::make('description')->label('Deskripsi'),
            ImportColumn::make('slug')->label('Slug'),
        ];
    }

    public function resolveRecord(): ?Product
    {
        return Product::firstOrNew(['slug' => $this->data['slug'] ?? '']);
    }
}
