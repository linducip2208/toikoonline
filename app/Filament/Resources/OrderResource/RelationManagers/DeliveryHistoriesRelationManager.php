<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DeliveryHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveryHistories';

    protected static ?string $title = 'Timeline Pengiriman';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('delivery_status')->label('Kode')->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->searchable(),
                Tables\Columns\TextColumn::make('note')->label('Catatan / Resi')->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
