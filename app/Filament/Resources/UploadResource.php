<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UploadResource\Pages;
use App\Models\Upload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UploadResource extends Resource
{
    protected static ?string $model = Upload::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = '🧩 CMS';
    protected static ?int $navigationSort = 13;
    protected static ?string $navigationLabel = 'Media Library';
    protected static ?string $pluralLabel = 'Media Library';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('file_name')->label('File')->directory('cms-media')->required()->columnSpanFull(),
            Forms\Components\TextInput::make('file_original_name')->maxLength(255),
            Forms\Components\TextInput::make('type')->maxLength(50)->placeholder('image'),
            Forms\Components\TextInput::make('external_link')->url()->maxLength(500)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_name')->label('Preview'),
                Tables\Columns\TextColumn::make('file_original_name')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUploads::route('/'),
            'create' => Pages\CreateUpload::route('/create'),
            'edit' => Pages\EditUpload::route('/{record}/edit'),
        ];
    }
}
