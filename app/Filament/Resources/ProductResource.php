<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ExportAction;
use App\Filament\Imports\ProductImporter;
use App\Filament\Exports\ProductExporter;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Group;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = '📦 Katalog';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Produk')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull()
                        ->placeholder('Nama produk lengkap'),
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')->required()->maxLength(100),
                            TextInput::make('slug')->required()->maxLength(150),
                        ]),
                    Select::make('brand_id')
                        ->relationship('brand', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    RichEditor::make('description')
                        ->columnSpanFull()
                        ->toolbarButtons([
                            'bold','italic','underline','strike','link',
                            'h2','h3','blockquote','bulletList','orderedList',
                        ]),
                    TagsInput::make('tags')
                        ->separator(',')
                        ->columnSpanFull()
                        ->placeholder('Ketik tag lalu tekan Enter'),
                    TextInput::make('unit')
                        ->placeholder('pcs, kg, box, pasang')
                        ->maxLength(50),
                    FileUpload::make('thumbnail_img')
                        ->image()
                        ->directory('products/thumbnails')
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetWidth('800')
                        ->imageResizeTargetHeight('800'),
                    FileUpload::make('photos')
                        ->image()
                        ->multiple()
                        ->directory('products/photos')
                        ->reorderable()
                        ->panelLayout('grid')
                        ->maxFiles(10)
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Harga & Stok')
                ->schema([
                    TextInput::make('unit_price')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->helperText('Harga jual dasar (jika tidak ada varian)'),
                    TextInput::make('purchase_price')
                        ->numeric()
                        ->prefix('Rp')
                        ->helperText('Harga beli / modal'),
                    TextInput::make('discount')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->helperText('Nominal diskon'),
                    Select::make('discount_type')
                        ->options([
                            'amount' => 'Nominal (Rp)',
                            'percent' => 'Persen (%)',
                        ])
                        ->default('amount'),
                    TextInput::make('min_qty')
                        ->numeric()
                        ->default(1)
                        ->helperText('Minimal pembelian'),
                    TextInput::make('max_qty')
                        ->numeric()
                        ->default(10)
                        ->helperText('Maksimal per transaksi'),
                    TextInput::make('low_stock_qty')
                        ->numeric()
                        ->default(0)
                        ->label('Batas Stok Rendah')
                        ->helperText('Notifikasi jika stok di bawah ini'),
                ])->columns(2),

            Section::make('Varian Produk')
                ->schema([
                    Toggle::make('variant_product')
                        ->label('Produk punya varian?')
                        ->live()
                        ->helperText('Aktifkan jika produk memiliki varian (warna, ukuran, dll)'),
                    TagsInput::make('attributes')
                        ->label('Atribut Varian (panduan)')
                        ->placeholder('contoh: Warna, Ukuran')
                        ->separator(',')
                        ->visible(fn(Get $get) => $get('variant_product'))
                        ->helperText('Catatan atribut yang tersedia — tidak mempengaruhi data')
                        ->columnSpanFull(),
                    TagsInput::make('colors')
                        ->label('Daftar Warna')
                        ->placeholder('contoh: Merah, Biru, Hitam')
                        ->separator(',')
                        ->visible(fn(Get $get) => $get('variant_product'))
                        ->columnSpanFull(),
                    Repeater::make('variants_data')
                        ->label('Daftar Varian')
                        ->relationship('stocks')
                        ->schema([
                            TextInput::make('variant')
                                ->label('Nama Varian')
                                ->required()
                                ->placeholder('Contoh: Merah / XL / Katun')
                                ->hintIcon('heroicon-m-information-circle', 'Nama varian yang tampil ke pembeli'),
                            TextInput::make('sku')
                                ->label('SKU')
                                ->unique(ignoreRecord: true)
                                ->placeholder('SKU-001')
                                ->hintIcon('heroicon-m-information-circle', 'Stock Keeping Unit unik'),
                            TextInput::make('price')
                                ->numeric()
                                ->prefix('Rp')
                                ->label('Harga')
                                ->helperText('Kosongkan untuk ikut harga dasar'),
                            TextInput::make('qty')
                                ->numeric()
                                ->label('Stok')
                                ->default(0),
                            ColorPicker::make('color_code')
                                ->label('Warna')
                                ->rgba(),
                            FileUpload::make('image')
                                ->image()
                                ->label('Foto Varian')
                                ->directory('products/variants')
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('1:1')
                                ->imageResizeTargetWidth('400')
                                ->imageResizeTargetHeight('400'),
                        ])
                        ->columns(3)
                        ->visible(fn(Get $get) => $get('variant_product'))
                        ->itemLabel(fn(array $state): ?string =>
                            ($state['variant'] ?? 'Varian Baru') .
                            (($state['sku'] ?? null) ? ' — ' . $state['sku'] : '') .
                            (($state['price'] ?? null) ? ' — Rp ' . number_format((float)$state['price'], 0, ',', '.') : '') .
                            (($state['qty'] ?? null) ? ' (' . $state['qty'] . ' pcs)' : '')
                        )
                        ->collapsible()
                        ->cloneable()
                        ->reorderable()
                        ->addActionLabel('Tambah Varian')
                        ->defaultItems(0),
                    Placeholder::make('variant_hint')
                        ->content('Gunakan tombol "Tambah Varian" untuk menambahkan varian secara manual. Setiap varian bisa memiliki harga, stok, SKU, dan foto sendiri.')
                        ->visible(fn(Get $get) => $get('variant_product')),
                ]),

            Section::make('Harga Grosir (Wholesale)')
                ->schema([
                    Toggle::make('wholesale_product')
                        ->label('Aktifkan Harga Grosir?')
                        ->live()
                        ->helperText('Berikan harga spesial untuk pembelian dalam jumlah besar'),
                    Repeater::make('wholesale_prices')
                        ->relationship('wholesalePrices')
                        ->schema([
                            TextInput::make('min_qty')
                                ->numeric()
                                ->label('Min Qty')
                                ->required()
                                ->minValue(1),
                            TextInput::make('max_qty')
                                ->numeric()
                                ->label('Max Qty')
                                ->required()
                                ->minValue(1),
                            TextInput::make('price')
                                ->numeric()
                                ->prefix('Rp')
                                ->label('Harga Grosir')
                                ->required(),
                        ])
                        ->columns(3)
                        ->visible(fn(Get $get) => $get('wholesale_product'))
                        ->collapsible()
                        ->defaultItems(1)
                        ->itemLabel(fn(array $state): ?string =>
                            ($state['min_qty'] ?? '?') . '-' . ($state['max_qty'] ?? '?') . ' pcs' .
                            (($state['price'] ?? null) ? ' → Rp ' . number_format((float)$state['price'], 0, ',', '.') : '')
                        ),
                ]),

            Section::make('Pajak (GST)')
                ->schema([
                    Toggle::make('gst_enable')
                        ->label('Aktifkan GST / PPN?')
                        ->live()
                        ->helperText('Tambahkan pajak otomatis ke harga produk'),
                    Select::make('gst_rate')
                        ->label('Tarif Pajak')
                        ->options([
                            '0' => '0%',
                            '5' => '5%',
                            '11' => '11% (PPN Indonesia)',
                            '12' => '12%',
                            '18' => '18%',
                            '28' => '28%',
                        ])
                        ->visible(fn(Get $get) => $get('gst_enable'))
                        ->default('11'),
                ]),

            Section::make('Pengiriman')
                ->schema([
                    Select::make('shipping_type')
                        ->options([
                            'flat_rate' => 'Flat Rate (Tarif Tetap)',
                            'free' => 'Gratis Ongkir',
                            'product_wise' => 'Per Produk (Kurir)',
                        ])
                        ->default('flat_rate'),
                    TextInput::make('shipping_cost')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(0)
                        ->helperText('Hanya untuk Flat Rate'),
                    TextInput::make('est_shipping_days')
                        ->numeric()
                        ->label('Estimasi Hari Pengiriman')
                        ->default(0),
                    TextInput::make('weight')
                        ->numeric()
                        ->label('Berat (kg)')
                        ->default(0)
                        ->helperText('Untuk kalkulasi ongkir kurir'),
                    TextInput::make('height')
                        ->numeric()
                        ->label('Tinggi (cm)'),
                    TextInput::make('width')
                        ->numeric()
                        ->label('Lebar (cm)'),
                    TextInput::make('length')
                        ->numeric()
                        ->label('Panjang (cm)'),
                    Toggle::make('cash_on_delivery')
                        ->label('COD Tersedia')
                        ->default(true),
                ])->columns(3),

            Section::make('SEO & Publikasi')
                ->schema([
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('URL-friendly nama produk')
                        ->prefix(url('/products') . '/'),
                    TextInput::make('meta_title')
                        ->maxLength(255)
                        ->helperText('Judul SEO (default: nama produk)'),
                    Textarea::make('meta_description')
                        ->maxLength(1000)
                        ->rows(3)
                        ->helperText('Deskripsi untuk hasil pencarian Google'),
                    FileUpload::make('meta_img')
                        ->image()
                        ->label('Meta Image')
                        ->directory('products/meta')
                        ->helperText('Gambar untuk OG:Image (1200x630)'),
                    Toggle::make('published')
                        ->default(true)
                        ->helperText('Produk akan tampil di toko'),
                    Toggle::make('approved')
                        ->default(true)
                        ->helperText('Produk sudah diverifikasi'),
                    Toggle::make('todays_deal')
                        ->label('Deal Hari Ini'),
                    Toggle::make('featured')
                        ->label('Produk Unggulan'),
                ])->columns(2),

            Section::make('AI Content Generator')
                ->schema([
                    Placeholder::make('ai_info')
                        ->content('Isi nama produk dan kategori terlebih dahulu, lalu klik tombol generate untuk membuat konten otomatis dengan AI.'),
                    Actions::make([
                        Action::make('generate_description')
                            ->label('Generate Deskripsi')
                            ->icon('heroicon-o-sparkles')
                            ->color('info')
                            ->action(function (Set $set, $state) {
                                $service = new \App\Services\Ai\AiContentService();
                                $desc = $service->generateProductDescription($state['name'] ?? '', $state['category_id'] ?? '');
                                if ($desc) {
                                    $set('description', $desc);
                                }
                            }),
                        Action::make('generate_meta')
                            ->label('Generate Meta')
                            ->icon('heroicon-o-document-text')
                            ->color('gray')
                            ->action(function (Set $set, $state) {
                                $service = new \App\Services\Ai\AiContentService();
                                $meta = $service->generateMetaDescription($state['name'] ?? '', '');
                                if ($meta) {
                                    $set('meta_description', $meta);
                                }
                            }),
                        Action::make('generate_tags')
                            ->label('Generate Tags')
                            ->icon('heroicon-o-tag')
                            ->color('warning')
                            ->action(function (Set $set, $state) {
                                $service = new \App\Services\Ai\AiContentService();
                                $tags = $service->generateProductTags($state['name'] ?? '', '');
                                if ($tags) {
                                    $set('tags', $tags);
                                }
                            }),
                    ]),
                ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->withSum('stocks', 'qty'))
            ->columns([
                ImageColumn::make('thumbnail_img')
                    ->label('Foto')
                    ->circular()
                    ->size(40),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->description(fn($record) => 'SKU: ' . ($record->stocks->first()?->sku ?? '-')),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('unit_price')
                    ->money('IDR')
                    ->sortable()
                    ->label('Harga'),
                TextColumn::make('stocks_sum_qty')
                    ->label('Stok')
                    ->sortable()
                    ->color(fn($state) => $state <= 10 ? 'danger' : 'success')
                    ->formatStateUsing(fn($state) => number_format($state ?? 0)),
                TextColumn::make('num_of_sale')
                    ->sortable()
                    ->label('Terjual')
                    ->toggleable(),
                TextColumn::make('rating')
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format((float)$state, 1)),
                IconColumn::make('variant_product')
                    ->label('Varian')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle'),
                IconColumn::make('published')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('featured')
                    ->boolean()
                    ->label('Unggulan')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori'),
                SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->label('Brand'),
                SelectFilter::make('variant_product')
                    ->label('Varian')
                    ->options([true => 'Punya Varian', false => 'Tanpa Varian']),
                SelectFilter::make('featured')
                    ->label('Unggulan')
                    ->options([true => 'Ya', false => 'Tidak']),
            ])
            ->headerActions([
                ImportAction::make()->importer(ProductImporter::class),
                ExportAction::make()->exporter(ProductExporter::class),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
