<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CmsSectionResource\Pages;
use App\Models\CmsSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CmsSectionResource extends Resource
{
    protected static ?string $model = CmsSection::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = '🧩 CMS';
    protected static ?int $navigationSort = 11;
    protected static ?string $navigationLabel = 'Homepage Sections';
    protected static ?string $pluralLabel = 'Homepage Sections';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->required()->maxLength(60)->helperText('cth: hero, voucher_rail, flash_deals, categories'),
            Forms\Components\TextInput::make('title')->maxLength(255),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\KeyValue::make('data')->keyLabel('Opsi')->valueLabel('Nilai')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('key')->badge()->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCmsSections::route('/'),
            'create' => Pages\CreateCmsSection::route('/create'),
            'edit' => Pages\EditCmsSection::route('/{record}/edit'),
        ];
    }
}
