<?php

namespace App\Filament\Resources\DeliveryBoyResource\Pages;

use App\Filament\Resources\DeliveryBoyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryBoys extends ListRecords
{
    protected static string $resource = DeliveryBoyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
