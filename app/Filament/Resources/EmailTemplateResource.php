<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';
    protected static ?string $navigationGroup = '⚙️ Sistem';
    protected static ?int $navigationSort = 5;
    protected static ?string $label = 'Template Email';
    protected static ?string $pluralLabel = 'Template Email';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('identifier')->required()->maxLength(255)->disabled(fn($record) => $record?->is_status_changeable === false),
            Forms\Components\Select::make('email_type')->options(['customer' => 'Customer', 'admin' => 'Admin', 'seller' => 'Seller'])->required(),
            Forms\Components\TextInput::make('subject')->required()->maxLength(255),
            Forms\Components\Textarea::make('default_text')->required()->rows(6)->columnSpanFull(),
            Forms\Components\Toggle::make('status')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identifier')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email_type')->badge()->sortable(),
                Tables\Columns\TextColumn::make('subject')->searchable()->limit(50),
                Tables\Columns\IconColumn::make('status')->boolean(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
