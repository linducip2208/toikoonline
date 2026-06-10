<?php

namespace App\Filament\Resources\FlashDealResource\Pages;

use App\Filament\Resources\FlashDealResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlashDeals extends ListRecords
{
    protected static string $resource = FlashDealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
