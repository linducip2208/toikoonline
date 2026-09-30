<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentResource\Pages;
use App\Filament\Resources\ShipmentResource\RelationManagers\ShipmentItemsRelationManager;
use App\Models\Order;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = '🛒 Pesanan';

    protected static ?int $navigationSort = 32;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('order_id')->label('Pesanan')
                ->options(Order::orderByDesc('id')->limit(200)->pluck('code', 'id'))
                ->searchable()->required(),
            Forms\Components\Select::make('courier')->label('Kurir')
                ->options(['jne' => 'JNE', 'jnt' => 'J&T', 'sicepat' => 'SiCepat', 'anteraja' => 'AnterAja', 'paxel' => 'Paxel', 'gosend' => 'GoSend'])
                ->searchable()->nullable(),
            Forms\Components\TextInput::make('tracking_number')->label('Nomor resi')->maxLength(100)
                ->helperText('Isi resi → resi di order + timeline lacak terisi otomatis.'),
            Forms\Components\Select::make('status')->options([
                'packed' => 'Dikemas',
                'shipped' => 'Dikirim',
                'delivered' => 'Terkirim',
            ])->required()->default('packed'),
            Forms\Components\DateTimePicker::make('shipped_at')->label('Dikirim pada'),
            Forms\Components\Textarea::make('note')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('order.code')->label('Pesanan')->searchable(),
                Tables\Columns\TextColumn::make('courier')->searchable(),
                Tables\Columns\TextColumn::make('tracking_number')->label('Resi')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('shipped_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'packed' => 'Dikemas',
                    'shipped' => 'Dikirim',
                    'delivered' => 'Terkirim',
                ]),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [ShipmentItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}
