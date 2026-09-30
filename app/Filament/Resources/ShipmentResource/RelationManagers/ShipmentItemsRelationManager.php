<?php

namespace App\Filament\Resources\ShipmentResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('orderDetail.product.name')->label('Produk'),
                Tables\Columns\TextColumn::make('orderDetail.variation')->label('Varian'),
                Tables\Columns\TextColumn::make('qty'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->form([
                    Forms\Components\Select::make('order_detail_id')->label('Item order')
                        ->options(fn (RelationManager $livewire) => $livewire->ownerRecord->order->orderDetails()
                            ->with('product')->get()->pluck('product.name', 'id'))
                        ->required(),
                    Forms\Components\TextInput::make('qty')->numeric()->required()->minValue(1)->default(1),
                ]),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }
}
