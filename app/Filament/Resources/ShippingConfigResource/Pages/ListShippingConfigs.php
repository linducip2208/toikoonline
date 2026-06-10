<?php

namespace App\Filament\Resources\ShippingConfigResource\Pages;

use App\Filament\Resources\ShippingConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingConfigs extends ListRecords
{
    protected static string $resource = ShippingConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
