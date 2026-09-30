<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxRateResource\Pages;
use App\Models\TaxRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaxRateResource extends Resource
{
    protected static ?string $model = TaxRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = '💰 Pajak';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(100)
                ->helperText('cth: PPN Indonesia 11%'),
            Forms\Components\TextInput::make('country')->default('*')->maxLength(10)
                ->helperText('Kode negara atau * untuk semua.'),
            Forms\Components\TextInput::make('state')->default('*')->maxLength(100),
            Forms\Components\TextInput::make('city')->default('*')->maxLength(100),
            Forms\Components\TextInput::make('postcode')->default('*')->maxLength(20)
                ->helperText('Mendukung wildcard *, cth: 40*.'),
            Forms\Components\TextInput::make('rate')->numeric()->required()->default(0)->suffix('%'),
            Forms\Components\Toggle::make('inclusive')->label('Harga sudah termasuk pajak'),
            Forms\Components\TextInput::make('priority')->numeric()->default(0)
                ->helperText('Prioritas tertinggi menang bila zona tumpang tindih.'),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('country')->sortable(),
                Tables\Columns\TextColumn::make('city')->searchable(),
                Tables\Columns\TextColumn::make('postcode')->searchable(),
                Tables\Columns\TextColumn::make('rate')->suffix('%')->sortable(),
                Tables\Columns\IconColumn::make('inclusive')->label('Inklusif')->boolean(),
                Tables\Columns\TextColumn::make('priority')->sortable(),
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
            'index' => Pages\ListTaxRates::route('/'),
            'create' => Pages\CreateTaxRate::route('/create'),
            'edit' => Pages\EditTaxRate::route('/{record}/edit'),
        ];
    }
}
