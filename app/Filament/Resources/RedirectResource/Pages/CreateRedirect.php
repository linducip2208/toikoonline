<?php

namespace App\Filament\Resources\RedirectResource\Pages;

use App\Filament\Resources\RedirectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRedirect extends CreateRecord
{
    protected static string $resource = RedirectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['from_path'] = \App\Models\Redirect::normalize($data['from_path'] ?? '/');

        return $data;
    }
}
