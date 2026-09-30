<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers\ItemsRelationManager;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = '📦 Inventaris';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')->required()->maxLength(50)->unique(ignoreRecord: true)
                ->default('PO-'.date('Ymd-His')),
            Forms\Components\Select::make('supplier_id')->relationship('supplier', 'name')->searchable()->required(),
            Forms\Components\Select::make('warehouse_id')->relationship('warehouse', 'name')->required(),
            Forms\Components\Select::make('status')->options([
                'draft' => 'Draf',
                'ordered' => 'Dipesan',
                'partially_received' => 'Diterima Sebagian',
                'received' => 'Diterima',
                'cancelled' => 'Dibatalkan',
            ])->required()->default('draft'),
            Forms\Components\Textarea::make('note')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->searchable(),
                Tables\Columns\TextColumn::make('warehouse.code')->label('Gudang'),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('subtotal')->money('IDR'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'Draf',
                    'ordered' => 'Dipesan',
                    'partially_received' => 'Diterima Sebagian',
                    'received' => 'Diterima',
                    'cancelled' => 'Dibatalkan',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('markOrdered')
                    ->label('Tandai dipesan')
                    ->icon('heroicon-o-check')
                    ->visible(fn (PurchaseOrder $record) => $record->status === 'draft')
                    ->action(fn (PurchaseOrder $record) => $record->update([
                        'status' => 'ordered',
                        'ordered_at' => now(),
                    ])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [ItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
