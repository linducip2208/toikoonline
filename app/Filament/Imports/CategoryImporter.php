<?php

namespace App\Filament\Imports;

use App\Models\Category;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class CategoryImporter extends Importer
{
    protected static ?string $model = Category::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->requiredMapping()->label('Nama'),
            ImportColumn::make('slug')->label('Slug'),
            ImportColumn::make('parent_id')->label('Parent ID'),
            ImportColumn::make('commision_rate')->numeric()->label('Komisi %'),
            ImportColumn::make('meta_title')->label('Meta Title'),
            ImportColumn::make('meta_description')->label('Meta Description'),
        ];
    }

    public function resolveRecord(): ?Category
    {
        $slug = $this->data['slug'] ?? Str::slug($this->data['name'] ?? '');

        return Category::firstOrNew(['slug' => $slug]);
    }
}
