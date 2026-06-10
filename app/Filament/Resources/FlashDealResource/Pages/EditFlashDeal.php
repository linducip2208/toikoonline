<?php

namespace App\Filament\Resources\FlashDealResource\Pages;

use App\Filament\Resources\FlashDealResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlashDeal extends EditRecord
{
    protected static string $resource = FlashDealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
