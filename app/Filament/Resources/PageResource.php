<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = '🧩 CMS';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(50)
                    ->default('custom'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Toggle::make('status')->label('Tampil?')->default(true),
                Forms\Components\DateTimePicker::make('published_at')->label('Scheduled publish (empty = immediately)'),
                Forms\Components\Toggle::make('show_in_footer')->label('Tampilkan di footer?')->default(false),
                Forms\Components\RichEditor::make('content')
                    ->label('Konten utama (fallback jika blocks kosong)')
                    ->columnSpanFull(),
                Forms\Components\Builder::make('blocks')
                    ->label('Page Builder — susun blok konten')
                    ->columnSpanFull()
                    ->collapsible()
                    ->blocks([
                        Forms\Components\Builder\Block::make('hero')
                            ->label('Hero')
                            ->schema([
                                Forms\Components\TextInput::make('heading')->required(),
                                Forms\Components\Textarea::make('subheading')->rows(2),
                                Forms\Components\TextInput::make('cta_text'),
                                Forms\Components\TextInput::make('cta_url'),
                                Forms\Components\FileUpload::make('image')->image()->directory('cms-pages')->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])->maxSize(10240)->rules([new \App\Rules\SafeUpload]),
                            ]),
                        Forms\Components\Builder\Block::make('html')
                            ->label('HTML bebas')
                            ->schema([
                                Forms\Components\RichEditor::make('html')->required()->columnSpanFull(),
                            ]),
                        Forms\Components\Builder\Block::make('banner_grid')
                            ->label('Grid banner (2 kolom)')
                            ->schema([
                                Forms\Components\TextInput::make('title'),
                                Forms\Components\Repeater::make('items')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')->image()->directory('cms-pages')->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])->maxSize(10240)->rules([new \App\Rules\SafeUpload]),
                                        Forms\Components\TextInput::make('link'),
                                        Forms\Components\TextInput::make('caption'),
                                    ])->columns(3)->columnSpanFull(),
                            ]),
                        Forms\Components\Builder\Block::make('product_grid')
                            ->label('Grid produk (otomatis)')
                            ->schema([
                                Forms\Components\TextInput::make('title')->placeholder('Produk Pilihan'),
                                Forms\Components\Select::make('source')->options(['featured' => 'Featured', 'best_seller' => 'Terlaris', 'latest' => 'Terbaru', 'category' => 'Per kategori'])->default('featured'),
                                Forms\Components\TextInput::make('limit')->numeric()->default(8),
                            ]),
                        Forms\Components\Builder\Block::make('testimonial')
                            ->label('Testimoni')
                            ->schema([
                                Forms\Components\TextInput::make('title')->placeholder('Kata pelanggan'),
                                Forms\Components\Repeater::make('items')->schema([
                                    Forms\Components\TextInput::make('name')->required(),
                                    Forms\Components\Textarea::make('quote')->required()->rows(2),
                                    Forms\Components\TextInput::make('rating')->numeric()->minValue(1)->maxValue(5)->default(5),
                                ])->columns(3)->columnSpanFull(),
                            ]),
                        Forms\Components\Builder\Block::make('faq')
                            ->label('FAQ (akordeon)')
                            ->schema([
                                Forms\Components\TextInput::make('title')->placeholder('Pertanyaan umum'),
                                Forms\Components\Repeater::make('items')->schema([
                                    Forms\Components\TextInput::make('question')->required()->columnSpanFull(),
                                    Forms\Components\Textarea::make('answer')->required()->rows(2)->columnSpanFull(),
                                ])->columnSpanFull(),
                            ]),
                        Forms\Components\Builder\Block::make('countdown')
                            ->label('Hitung mundur')
                            ->schema([
                                Forms\Components\TextInput::make('title'),
                                Forms\Components\DateTimePicker::make('ends_at')->required(),
                                Forms\Components\TextInput::make('cta_url')->placeholder('/flash-deals/slug'),
                            ]),
                        Forms\Components\Builder\Block::make('newsletter')
                            ->label('Buletin')
                            ->schema([
                                Forms\Components\TextInput::make('title')->default('Dapatkan promo terbaru'),
                                Forms\Components\Textarea::make('subtitle')->rows(2),
                            ]),
                        Forms\Components\Builder\Block::make('contact_form')
                            ->label('Formulir kontak')
                            ->schema([
                                Forms\Components\TextInput::make('title')->default('Hubungi kami'),
                                Forms\Components\Textarea::make('description')->rows(2),
                            ]),
                        Forms\Components\Builder\Block::make('gallery')
                            ->label('Galeri')
                            ->schema([
                                Forms\Components\TextInput::make('title'),
                                Forms\Components\Repeater::make('images')->schema([
                                    Forms\Components\FileUpload::make('image')->image()->directory('cms-pages')->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])->maxSize(10240)->rules([new \App\Rules\SafeUpload])->required(),
                                    Forms\Components\TextInput::make('caption'),
                                ])->columns(2)->columnSpanFull(),
                            ]),
                        Forms\Components\Builder\Block::make('video')
                            ->label('Video')
                            ->schema([
                                Forms\Components\TextInput::make('title'),
                                Forms\Components\TextInput::make('url')->url()->required()->placeholder('https://www.youtube.com/embed/...'),
                                Forms\Components\Textarea::make('caption')->rows(2),
                            ]),
                        Forms\Components\Builder\Block::make('pricing')
                            ->label('Harga')
                            ->schema([
                                Forms\Components\TextInput::make('title'),
                                Forms\Components\Repeater::make('plans')->schema([
                                    Forms\Components\TextInput::make('name')->required(),
                                    Forms\Components\TextInput::make('price')->required()->placeholder('99000'),
                                    Forms\Components\Textarea::make('features')->rows(3)->helperText('Satu fitur per baris'),
                                    Forms\Components\TextInput::make('cta_text'),
                                    Forms\Components\TextInput::make('cta_url'),
                                ])->columns(2)->columnSpanFull(),
                            ]),
                        Forms\Components\Builder\Block::make('global')
                            ->label('Blok Global (reusable)')
                            ->schema([
                                Forms\Components\Select::make('key')
                                    ->label('Blok global')
                                    ->options(fn () => \App\Models\GlobalBlock::active()->pluck('title', 'key')->all())
                                    ->searchable()->required()
                                    ->helperText('Isi blok diambil dari referensi — maks 1 level nesting.'),
                            ]),
                    ]),
                Forms\Components\TextInput::make('meta_title')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('meta_description')
                    ->maxLength(1000)->columnSpanFull(),
                Forms\Components\TextInput::make('keywords')
                    ->maxLength(1000),
                Forms\Components\FileUpload::make('meta_image')
                    ->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])->maxSize(10240)->rules([new \App\Rules\SafeUpload]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('status')->boolean()->label('Tampil'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()->toggleable(),
                Tables\Columns\IconColumn::make('show_in_footer')->boolean()->label('Footer'),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')->label('Tampil'),
                Tables\Filters\Filter::make('scheduled')->label('Scheduled (future)')
                    ->query(fn (Builder $q) => $q->where('published_at', '>', now())),
                Tables\Filters\Filter::make('trashed_status')->label('Trashed (status off)')
                    ->query(fn (Builder $q) => $q->where('status', false)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')->label('Preview')->icon('heroicon-o-eye')
                    ->url(fn (Page $record) => route('page.show', $record->slug).'?preview='.$record->getKey(), true),
                Tables\Actions\Action::make('duplicate')->label('Duplicate')->icon('heroicon-o-square-2-stack')
                    ->requiresConfirmation()
                    ->action(function (Page $record) {
                        $copy = $record->replicate();
                        $copy->slug = ($record->slug ?: 'page').'-copy-'.time();
                        $copy->title = $record->title.' (Copy)';
                        $copy->published_at = null;
                        $copy->save();
                    }),
                Tables\Actions\Action::make('trash')->label('Trash')->icon('heroicon-o-trash')
                    ->requiresConfirmation()->color('danger')
                    ->action(fn (Page $record) => $record->update(['status' => false])),
                Tables\Actions\Action::make('restore_status')->label('Restore')->icon('heroicon-o-arrow-uturn-left')
                    ->visible(fn (Page $record) => ! $record->status)
                    ->action(fn (Page $record) => $record->update(['status' => true])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\PageResource\RevisionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
