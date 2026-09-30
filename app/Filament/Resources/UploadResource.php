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
            Forms\Components\Select::make('folder_id')->label('Folder')->options(fn () => \App\Models\MediaFolder::pluck('name', 'id')->all())->searchable()->nullable(),
            Forms\Components\TextInput::make('type')->maxLength(50)->placeholder('image'),
            Forms\Components\TextInput::make('alt_text')->maxLength(255),
            Forms\Components\TextInput::make('caption')->maxLength(500)->columnSpanFull(),
            Forms\Components\TextInput::make('external_link')->url()->maxLength(500)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_name')->label('Preview'),
                Tables\Columns\TextColumn::make('file_original_name')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('folder.name')->label('Folder')->sortable(),
                Tables\Columns\TextColumn::make('alt_text')->limit(25)->toggleable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('folder_id')->label('Folder')->options(fn () => \App\Models\MediaFolder::pluck('name', 'id')->all()),
                Tables\Filters\Filter::make('broken')->label('Broken files (missing on disk)')
                    ->query(function ($q) {
                        $ids = \App\Models\Upload::all()->filter(function ($u) {
                            if ($u->external_link) {
                                return false;
                            }
                            if (! $u->file_name) {
                                return true;
                            }

                            return ! file_exists(storage_path('app/public/'.$u->file_name)) && ! file_exists(public_path('storage/'.$u->file_name));
                        })->pluck('id')->all();

                        return $q->whereIn('id', $ids === [] ? [0] : $ids);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('check')->label('Check file')->icon('heroicon-o-magnifying-glass')
                    ->action(function (\App\Models\Upload $record) {
                        $ok = $record->external_link ? true : (file_exists(storage_path('app/public/'.$record->file_name)) || file_exists(public_path('storage/'.$record->file_name)));
                        \Filament\Notifications\Notification::make()->title($ok ? 'File OK' : 'File MISSING on disk')->{ $ok ? 'success' : 'danger' }()->send();
                    }),
            ])
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
