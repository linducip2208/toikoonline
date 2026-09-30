<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DynamicPopupResource\Pages;
use App\Models\DynamicPopup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DynamicPopupResource extends Resource
{
    protected static ?string $model = DynamicPopup::class;
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = '🧩 CMS';
    protected static ?int $navigationSort = 12;
    protected static ?string $navigationLabel = 'Popup Promo';
    protected static ?string $pluralLabel = 'Popup Promo';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Toggle::make('status')->label('Aktif')->default(true),
            Forms\Components\TextInput::make('title')->required()->maxLength(191),
            Forms\Components\Textarea::make('summary')->rows(3)->columnSpanFull(),
            Forms\Components\FileUpload::make('banner')->image()->directory('popups'),
            Forms\Components\TextInput::make('btn_text')->maxLength(191)->placeholder('Klaim Sekarang'),
            Forms\Components\TextInput::make('btn_link')->maxLength(191)->placeholder('/coupons'),
            Forms\Components\ColorPicker::make('btn_background_color')->default('#4f46e5'),
            Forms\Components\ColorPicker::make('btn_text_color')->default('#ffffff'),
            Forms\Components\Toggle::make('show_subscribe_form')->label('Tampilkan form newsletter?')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('status')->boolean()->label('Aktif'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('btn_text')->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDynamicPopups::route('/'),
            'create' => Pages\CreateDynamicPopup::route('/create'),
            'edit' => Pages\EditDynamicPopup::route('/{record}/edit'),
        ];
    }
}
