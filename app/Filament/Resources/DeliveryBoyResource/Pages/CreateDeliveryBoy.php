<?php

namespace App\Filament\Resources\DeliveryBoyResource\Pages;

use App\Filament\Resources\DeliveryBoyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateDeliveryBoy extends CreateRecord
{
    protected static string $resource = DeliveryBoyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_type'] = 'delivery_boy';
        return $data;
    }
}
