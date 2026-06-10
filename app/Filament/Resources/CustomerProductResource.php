<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerProductResource\Pages;
use App\Models\CustomerProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class CustomerProductResource extends Resource
{
    protected static ?string $model = CustomerProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = '👥 Pelanggan';

    protected static ?int $navigationSort = 5;

    protected static ?string $label = 'Iklan Baris';

    protected static ?string $pluralLabel = 'Iklan Baris';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Iklan')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label('Penjual'),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(200)
                        ->label('Judul Iklan'),
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label('Kategori'),
                    TextInput::make('unit_price')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->label('Harga'),
                    RichEditor::make('description')
                        ->label('Deskripsi')
                        ->columnSpanFull(),
                    FileUpload::make('photos')
                        ->image()
                        ->multiple()
                        ->directory('classifieds/photos')
                        ->columnSpanFull()
                        ->label('Foto'),
                    FileUpload::make('thumbnail_img')
                        ->image()
                        ->directory('classifieds/thumbnails')
                        ->label('Thumbnail'),
                ])->columns(2),

            Section::make('Detail & Status')
                ->schema([
                    TextInput::make('condition')
                        ->label('Kondisi')
                        ->placeholder('Baru / Bekas'),
                    Textarea::make('location')
                        ->label('Lokasi')
                        ->placeholder('Contoh: Jakarta Selatan, DKI Jakarta'),
                    TagsInput::make('tags')
                        ->label('Tags'),
                    TextInput::make('video_provider')
                        ->label('Provider Video')
                        ->placeholder('youtube / vimeo'),
                    TextInput::make('video_link')
                        ->label('Link Video')
                        ->url(),
                    Toggle::make('published')
                        ->default(false)
                        ->label('Dipublikasikan'),
                    Toggle::make('status')
                        ->default(false)
                        ->label('Disetujui'),
                ])->columns(2),

            Section::make('SEO')
                ->schema([
                    TextInput::make('slug')
                        ->label('Slug')
                        ->unique(ignoreRecord: true),
                    TextInput::make('meta_title')
                        ->label('Meta Title'),
                    Textarea::make('meta_description')
                        ->label('Meta Description'),
                ])->columns(1)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->label('Judul'),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Penjual'),
                TextColumn::make('category.name')
                    ->sortable()
                    ->label('Kategori'),
                TextColumn::make('unit_price')
                    ->money('IDR')
                    ->sortable()
                    ->label('Harga'),
                IconColumn::make('published')
                    ->boolean()
                    ->sortable()
                    ->label('Publikasi'),
                IconColumn::make('status')
                    ->boolean()
                    ->sortable()
                    ->label('Disetujui'),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->label('Dibuat'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerProducts::route('/'),
            'create' => Pages\CreateCustomerProduct::route('/create'),
            'edit' => Pages\EditCustomerProduct::route('/{record}/edit'),
        ];
    }
}
