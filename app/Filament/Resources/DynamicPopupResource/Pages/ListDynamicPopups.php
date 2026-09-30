<?php

namespace App\Filament\Resources\DynamicPopupResource\Pages;

use App\Filament\Resources\DynamicPopupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDynamicPopups extends ListRecords
{
    protected static string $resource = DynamicPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
