<?php

namespace App\Filament\Resources\DynamicPopupResource\Pages;

use App\Filament\Resources\DynamicPopupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDynamicPopup extends EditRecord
{
    protected static string $resource = DynamicPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
