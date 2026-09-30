<?php

namespace App\Filament\Resources\PageResource;

use App\Models\PageRevision;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';

    protected static ?string $title = 'Revisions';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title')->limit(40),
                Tables\Columns\TextColumn::make('slug')->limit(40),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label('Restore')->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->action(function (PageRevision $record) {
                        $page = $record->page;
                        $page->update([
                            'title' => $record->title,
                            'slug' => $record->slug,
                            'content' => $record->content,
                            'blocks' => $record->blocks,
                        ]);
                        \Filament\Notifications\Notification::make()->title('Revision restored')->success()->send();
                    }),
            ]);
    }
}
