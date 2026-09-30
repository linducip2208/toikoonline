<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingZoneResource\Pages;
use App\Models\ShippingZone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingZoneResource extends Resource
{
    protected static ?string $model = ShippingZone::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = '🚚 Pengiriman';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama zona')->required()->maxLength(128),
            Forms\Components\TextInput::make('country')->label('Negara (koma, * = semua)')->maxLength(255)->default('ID'),
            Forms\Components\TextInput::make('state')->label('Provinsi (koma, * = semua)')->maxLength(255),
            Forms\Components\TextInput::make('city')->label('Kota (koma, * = semua)')->maxLength(255),
            Forms\Components\TextInput::make('postcode')->label('Kode pos (koma, wildcards * didukung)')->maxLength(255),
            Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('city')->label('Kota')->limit(30),
                Tables\Columns\TextColumn::make('postcode')->label('Kode pos')->limit(30),
                Tables\Columns\TextColumn::make('methods_count')->label('Metode')->counts('methods'),
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
            'index' => Pages\ListShippingZones::route('/'),
            'create' => Pages\CreateShippingZone::route('/create'),
            'edit' => Pages\EditShippingZone::route('/{record}/edit'),
        ];
    }
}
