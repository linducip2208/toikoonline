<?php

namespace App\Filament\Resources\CustomerProductResource\Pages;

use App\Filament\Resources\CustomerProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerProducts extends ListRecords
{
    protected static string $resource = CustomerProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
