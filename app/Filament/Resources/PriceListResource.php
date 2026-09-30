<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceListResource\Pages;
use App\Models\PriceList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PriceListResource extends Resource
{
    protected static ?string $model = PriceList::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = '🏢 B2B';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('company_id')->label('Perusahaan (kosong = global)')
                ->relationship('company', 'name')->searchable()->nullable(),
            Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(150),
            Forms\Components\TextInput::make('currency')->default('IDR')->required()->maxLength(10),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            Forms\Components\Repeater::make('items')->label('Item harga')->relationship('items')
                ->schema([
                    Forms\Components\Select::make('product_id')->label('Produk')
                        ->options(\App\Models\Product::orderBy('name')->limit(500)->pluck('name', 'id'))
                        ->searchable()->required(),
                    Forms\Components\TextInput::make('variant')->label('Varian (opsional)')->maxLength(255),
                    Forms\Components\TextInput::make('price')->label('Harga (Rp)')->numeric()->required()->minValue(0),
                    Forms\Components\TextInput::make('min_qty')->label('Min. qty')->numeric()->default(1)->minValue(1),
                ])->columns(4)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('company.name')->label('Perusahaan')->placeholder('Global'),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Item'),
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
            'index' => Pages\ListPriceLists::route('/'),
            'create' => Pages\CreatePriceList::route('/create'),
            'edit' => Pages\EditPriceList::route('/{record}/edit'),
        ];
    }
}
