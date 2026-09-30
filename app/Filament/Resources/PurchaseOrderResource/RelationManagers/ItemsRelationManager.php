<?php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

use App\Models\ProductStock;
use App\Models\PurchaseOrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->relationship('product', 'name')->searchable()->required()->reactive(),
            Forms\Components\Select::make('stock_id')->label('Varian')
                ->options(fn (callable $get) => ProductStock::where('product_id', $get('product_id'))->pluck('variant', 'id'))
                ->nullable(),
            Forms\Components\TextInput::make('qty_ordered')->numeric()->required()->minValue(1)->default(1),
            Forms\Components\TextInput::make('unit_cost')->numeric()->required()->default(0)->prefix('Rp'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Produk'),
                Tables\Columns\TextColumn::make('stock.variant')->label('Varian'),
                Tables\Columns\TextColumn::make('qty_ordered')->label('Dipesan'),
                Tables\Columns\TextColumn::make('qty_received')->label('Diterima'),
                Tables\Columns\TextColumn::make('unit_cost')->money('IDR')->label('Harga beli'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\Action::make('receive')
                    ->label('Terima')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->form([Forms\Components\TextInput::make('qty')->numeric()->required()->minValue(1)->label('Qty diterima')])
                    ->action(function (PurchaseOrderItem $record, array $data) {
                        $record->purchaseOrder->receiveItem($record, (int) $data['qty']);
                        Notification::make()->title('Stok bertambah + movement tercatat')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
