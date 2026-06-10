<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashDealResource\Pages;
use App\Models\FlashDeal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlashDealResource extends Resource
{
    protected static ?string $model = FlashDeal::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = '🎫 Promo';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\TextInput::make('start_date')
                    ->numeric(),
                Forms\Components\TextInput::make('end_date')
                    ->numeric(),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\Toggle::make('featured')
                    ->required(),
                Forms\Components\TextInput::make('background_color')
                    ->maxLength(255),
                Forms\Components\TextInput::make('text_color')
                    ->maxLength(255),
                Forms\Components\TextInput::make('banner')
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\IconColumn::make('featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('background_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('text_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('banner')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlashDeals::route('/'),
            'create' => Pages\CreateFlashDeal::route('/create'),
            'edit' => Pages\EditFlashDeal::route('/{record}/edit'),
        ];
    }
}
