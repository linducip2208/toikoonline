<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmsTemplateResource\Pages;
use App\Models\SmsTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SmsTemplateResource extends Resource
{
    protected static ?string $model = SmsTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';
    protected static ?string $navigationGroup = '⚙️ Sistem';
    protected static ?int $navigationSort = 6;
    protected static ?string $label = 'Template SMS';
    protected static ?string $pluralLabel = 'Template SMS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('identifier')->required()->maxLength(100),
            Forms\Components\Select::make('locale')->label('Locale')->options(['id' => 'Indonesia (id)', 'en' => 'English (en)'])->default('id')->required(),
            Forms\Components\Select::make('sms_type')->options(['customer' => 'Customer', 'admin' => 'Admin', 'seller' => 'Seller'])->required(),
            Forms\Components\Textarea::make('body')->required()->rows(4)->columnSpanFull(),
            Forms\Components\Toggle::make('status')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identifier')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('locale')->badge()->sortable(),
                Tables\Columns\TextColumn::make('sms_type')->badge()->sortable(),
                Tables\Columns\TextColumn::make('body')->limit(60),
                Tables\Columns\IconColumn::make('status')->boolean(),
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
            'index' => Pages\ListSmsTemplates::route('/'),
            'create' => Pages\CreateSmsTemplate::route('/create'),
            'edit' => Pages\EditSmsTemplate::route('/{record}/edit'),
        ];
    }
}
