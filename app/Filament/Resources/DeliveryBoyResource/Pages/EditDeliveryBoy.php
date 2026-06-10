<?php

namespace App\Filament\Resources\DeliveryBoyResource\Pages;

use App\Filament\Resources\DeliveryBoyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryBoy extends EditRecord
{
    protected static string $resource = DeliveryBoyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
