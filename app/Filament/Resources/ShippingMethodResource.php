<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingMethodResource\Pages;
use App\Models\ShippingMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingMethodResource extends Resource
{
    protected static ?string $model = ShippingMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = '🚚 Pengiriman';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('zone_id')->label('Zona')
                ->relationship('zone', 'name')->required()->searchable()->preload(),
            Forms\Components\TextInput::make('name')->label('Nama layanan')->required()->maxLength(128),
            Forms\Components\TextInput::make('courier')->label('Kurir')->default('local')->maxLength(64),
            Forms\Components\TextInput::make('service')->label('Service')->default('standard')->maxLength(64),
            Forms\Components\TextInput::make('base_rate')->label('Tarif dasar (IDR)')->numeric()->default(0)->minValue(0),
            Forms\Components\TextInput::make('per_kg')->label('Per kg tambahan (IDR)')->numeric()->default(0)->minValue(0),
            Forms\Components\Textarea::make('weight_tiers')->label('Tier berat (JSON: [{"max_weight_kg":1,"rate":9000}])')
                ->helperText('Kosongkan bila pakai tarif dasar + per kg.')
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state) : $state)
                ->dehydrateStateUsing(fn ($state) => $state ? json_decode((string) $state, true) : null)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('free_min_subtotal')->label('Gratis min. belanja (IDR, kosong = tidak ada)')
                ->numeric()->minValue(0),
            Forms\Components\TextInput::make('eta')->label('Estimasi')->maxLength(64)->placeholder('1-2 hari'),
            Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('zone.name')->label('Zona')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Layanan')->searchable(),
                Tables\Columns\TextColumn::make('courier')->label('Kurir'),
                Tables\Columns\TextColumn::make('base_rate')->label('Tarif dasar')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('free_min_subtotal')->label('Gratis min.')->money('IDR')->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingMethods::route('/'),
            'create' => Pages\CreateShippingMethod::route('/create'),
            'edit' => Pages\EditShippingMethod::route('/{record}/edit'),
        ];
    }
}
