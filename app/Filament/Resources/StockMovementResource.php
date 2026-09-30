<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\Inventory\InventoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = '📦 Inventaris';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('warehouse_id')->relationship('warehouse', 'name')->disabled(),
            Forms\Components\Select::make('product_id')->relationship('product', 'name')->disabled(),
            Forms\Components\TextInput::make('type')->disabled(),
            Forms\Components\TextInput::make('qty')->numeric()->disabled(),
            Forms\Components\Textarea::make('note')->disabled()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('warehouse.code')->label('Gudang')->sortable(),
                Tables\Columns\TextColumn::make('product.name')->label('Produk')->searchable(),
                Tables\Columns\TextColumn::make('type')->badge()->sortable(),
                Tables\Columns\TextColumn::make('qty')->sortable(),
                Tables\Columns\TextColumn::make('qty_after')->label('Stok akhir'),
                Tables\Columns\TextColumn::make('ref_type')->label('Ref')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options(array_combine(StockMovement::TYPES, StockMovement::TYPES)),
                Tables\Filters\SelectFilter::make('warehouse_id')->relationship('warehouse', 'name')->label('Gudang'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('adjust')
                    ->label('Penyesuaian stok')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->form([
                        Forms\Components\Select::make('warehouse_id')->label('Gudang')
                            ->options(Warehouse::pluck('name', 'id'))->required(),
                        Forms\Components\Select::make('product_id')->label('Produk')
                            ->options(Product::limit(200)->pluck('name', 'id'))->searchable()->required()->reactive(),
                        Forms\Components\Select::make('stock_id')->label('Varian')
                            ->options(fn (callable $get) => \App\Models\ProductStock::where('product_id', $get('product_id'))->pluck('variant', 'id'))
                            ->required(),
                        Forms\Components\TextInput::make('qty')->label('Qty (+/-)')->numeric()->required()
                            ->helperText('Positif menambah, negatif mengurangi.'),
                        Forms\Components\Textarea::make('note')->label('Catatan'),
                    ])
                    ->action(function (array $data, InventoryService $service) {
                        $service->adjust(
                            (int) $data['product_id'],
                            (int) $data['stock_id'],
                            (int) $data['qty'],
                            $data['note'] ?? null,
                            (int) $data['warehouse_id']
                        );
                        Notification::make()->title('Stok disesuaikan')->success()->send();
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
