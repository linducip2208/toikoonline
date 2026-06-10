<?php

namespace App\Filament\Resources\CategoryDiscountResource\Pages;

use App\Filament\Resources\CategoryDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryDiscounts extends ListRecords
{
    protected static string $resource = CategoryDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
