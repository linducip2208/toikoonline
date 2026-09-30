<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GlobalBlockResource\Pages;
use App\Models\GlobalBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GlobalBlockResource extends Resource
{
    protected static ?string $model = GlobalBlock::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationGroup = '🧩 CMS';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Blok Global';
    protected static ?string $pluralLabel = 'Blok Global';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->required()->maxLength(100)->unique(ignoreRecord: true)
                ->helperText('cth: footer_cta, promo_banner — dipakai blok Builder "global".')
                ->rules(['alpha_dash']),
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\Builder::make('blocks')
                ->label('Isi blok (skema sama dengan Page Builder)')
                ->columnSpanFull()->collapsible()
                ->blocks([
                    Forms\Components\Builder\Block::make('hero')->label('Hero')->schema([
                        Forms\Components\TextInput::make('heading')->required(),
                        Forms\Components\Textarea::make('subheading')->rows(2),
                        Forms\Components\TextInput::make('cta_text'),
                        Forms\Components\TextInput::make('cta_url'),
                        Forms\Components\FileUpload::make('image')->image()->directory('cms-pages')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])->maxSize(10240)->rules([new \App\Rules\SafeUpload]),
                    ]),
                    Forms\Components\Builder\Block::make('html')->label('HTML bebas')->schema([
                        Forms\Components\RichEditor::make('html')->required()->columnSpanFull(),
                    ]),
                    Forms\Components\Builder\Block::make('banner_grid')->label('Grid banner (2 kolom)')->schema([
                        Forms\Components\TextInput::make('title'),
                        Forms\Components\Repeater::make('items')->schema([
                            Forms\Components\FileUpload::make('image')->image()->directory('cms-pages')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])->maxSize(10240)->rules([new \App\Rules\SafeUpload]),
                            Forms\Components\TextInput::make('link'),
                            Forms\Components\TextInput::make('caption'),
                        ])->columns(3)->columnSpanFull(),
                    ]),
                    Forms\Components\Builder\Block::make('newsletter')->label('Buletin')->schema([
                        Forms\Components\TextInput::make('title')->default('Dapatkan promo terbaru'),
                        Forms\Components\Textarea::make('subtitle')->rows(2),
                    ]),
                    Forms\Components\Builder\Block::make('gallery')->label('Galeri')->schema([
                        Forms\Components\TextInput::make('title'),
                        Forms\Components\Repeater::make('images')->schema([
                            Forms\Components\FileUpload::make('image')->image()->directory('cms-pages')->required()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])->maxSize(10240)->rules([new \App\Rules\SafeUpload]),
                            Forms\Components\TextInput::make('caption'),
                        ])->columns(2)->columnSpanFull(),
                    ]),
                    Forms\Components\Builder\Block::make('video')->label('Video')->schema([
                        Forms\Components\TextInput::make('title'),
                        Forms\Components\TextInput::make('url')->url()->required(),
                        Forms\Components\Textarea::make('caption')->rows(2),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->badge()->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Aktif'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGlobalBlocks::route('/'),
            'create' => Pages\CreateGlobalBlock::route('/create'),
            'edit' => Pages\EditGlobalBlock::route('/{record}/edit'),
        ];
    }
}
