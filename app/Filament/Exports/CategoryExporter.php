<?php

namespace App\Filament\Exports;

use App\Models\Category;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CategoryExporter extends Exporter
{
    protected static ?string $model = Category::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('slug')->label('Slug'),
            ExportColumn::make('parent.name')->label('Parent'),
            ExportColumn::make('commision_rate')->label('Komisi %'),
            ExportColumn::make('featured')->label('Featured'),
        ];
    }
}
