<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = '🧩 CMS';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationLabel = 'Menu';
    protected static ?string $pluralLabel = 'Menu';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('location')
                ->options(['header' => 'Header', 'footer_shop' => 'Footer — Belanja', 'footer_help' => 'Footer — Bantuan', 'mobile' => 'Mobile Bottom'])
                ->required()->default('header'),
            Forms\Components\Select::make('parent_id')->label('Parent (submenu)')
                ->options(fn () => Menu::orderBy('sort_order')->pluck('label', 'id')->all())->searchable()->nullable(),
            Forms\Components\TextInput::make('label')->required()->maxLength(100),
            Forms\Components\TextInput::make('url')->required()->maxLength(255)->placeholder('/page/tentang-kami atau https://...'),
            Forms\Components\TextInput::make('icon')->maxLength(50)->placeholder('cth: home, tag, truck'),
            Forms\Components\TextInput::make('badge_text')->label('Badge')->maxLength(30)->placeholder('Baru'),
            Forms\Components\Select::make('badge_color')->label('Warna badge')->options(['red' => 'Merah', 'green' => 'Hijau', 'blue' => 'Biru', 'amber' => 'Kuning', 'brand' => 'Brand'])->nullable(),
            Forms\Components\Select::make('language_code')->label('Bahasa (kosong = semua)')->options(['id' => 'Indonesia', 'en' => 'English'])->nullable(),
            Forms\Components\Toggle::make('mega_menu')->label('Mega menu')->default(false),
            Forms\Components\CheckboxList::make('visibility')->label('Visibilitas (kosong = semua)')
                ->options(['guest' => 'Hanya tamu', 'auth' => 'Hanya login'])->bulkToggleable(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\Toggle::make('open_new_tab')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location')->badge()->sortable(),
                Tables\Columns\TextColumn::make('label')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('parent.label')->label('Parent')->toggleable(),
                Tables\Columns\TextColumn::make('badge_text')->label('Badge')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('language_code')->label('Lang')->badge()->toggleable(),
                Tables\Columns\TextColumn::make('url')->limit(30)->searchable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
