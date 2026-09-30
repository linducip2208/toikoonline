<?php

namespace App\Filament\Resources\RedirectResource\Pages;

use App\Filament\Resources\RedirectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRedirect extends EditRecord
{
    protected static string $resource = RedirectResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['from_path'] = \App\Models\Redirect::normalize($data['from_path'] ?? '/');

        return $data;
    }
}
