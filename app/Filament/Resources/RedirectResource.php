<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedirectResource\Pages;
use App\Models\Redirect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-circle';
    protected static ?string $navigationGroup = '🧩 CMS';
    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('from_path')->required()->maxLength(500)->unique(ignoreRecord: true)
                ->helperText('e.g. /old-page — leading slash, no trailing slash'),
            Forms\Components\TextInput::make('to_path')->required()->maxLength(500),
            Forms\Components\Select::make('code')->options([301 => '301 Permanent', 302 => '302 Temporary'])->default(301)->required(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_path')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('to_path')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('code')->badge(),
                Tables\Columns\TextColumn::make('hits')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit' => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}
