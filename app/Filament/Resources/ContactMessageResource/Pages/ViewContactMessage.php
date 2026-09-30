<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_read')
                ->label('Tandai sudah dibaca')
                ->icon('heroicon-o-check')
                ->visible(fn () => ! $this->record->is_read)
                ->action(function () {
                    $this->record->update(['is_read' => true]);
                    $this->refreshFormData(['is_read']);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
