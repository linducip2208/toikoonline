<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaFolderResource\Pages;
use App\Models\MediaFolder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MediaFolderResource extends Resource
{
    protected static ?string $model = MediaFolder::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = '🧩 CMS';
    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\Select::make('parent_id')->label('Parent')
                ->options(fn () => MediaFolder::pluck('name', 'id')->all())->searchable()->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('parent.name')->label('Parent'),
                Tables\Columns\TextColumn::make('uploads_count')->counts('uploads')->label('Files'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaFolders::route('/'),
            'create' => Pages\CreateMediaFolder::route('/create'),
            'edit' => Pages\EditMediaFolder::route('/{record}/edit'),
        ];
    }
}
