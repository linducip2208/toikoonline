<?php

namespace App\Filament\Resources\CustomerProductResource\Pages;

use App\Filament\Resources\CustomerProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerProduct extends EditRecord
{
    protected static string $resource = CustomerProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
