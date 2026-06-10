<?php

namespace App\Filament\Resources\CategoryDiscountResource\Pages;

use App\Filament\Resources\CategoryDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryDiscount extends EditRecord
{
    protected static string $resource = CategoryDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
