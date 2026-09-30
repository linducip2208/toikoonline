<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = '📢 Marketing';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'banner1' => 'Banner Section 1',
                        'banner2' => 'Banner Section 2',
                        'banner3' => 'Banner Section 3',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('photo')
                    ->numeric()
                    ->hint('Upload ID'),
                Forms\Components\TextInput::make('link')
                    ->maxLength(255)
                    ->hint('Contoh: /products?category=elektronik'),
                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->badge()->sortable(),
                Tables\Columns\TextColumn::make('position')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('link')->searchable()->limit(40),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options([
                    'banner1' => 'Banner Section 1',
                    'banner2' => 'Banner Section 2',
                    'banner3' => 'Banner Section 3',
                ]),
                Tables\Filters\SelectFilter::make('status')->options([1 => 'Aktif', 0 => 'Nonaktif']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
