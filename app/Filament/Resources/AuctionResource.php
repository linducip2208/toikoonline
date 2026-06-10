<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuctionResource\Pages;
use App\Models\Auction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class AuctionResource extends Resource
{
    protected static ?string $model = Auction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = '🎫 Promo';

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Lelang';

    protected static ?string $pluralLabel = 'Lelang';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Detail Lelang')
                ->schema([
                    Select::make('product_id')
                        ->relationship('product', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    TextInput::make('starting_bid')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->label('Harga Awal'),
                    TextInput::make('buy_now_price')
                        ->numeric()
                        ->prefix('Rp')
                        ->nullable()
                        ->label('Harga Beli Langsung'),
                    TextInput::make('bid_increment')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(1000)
                        ->required()
                        ->label('Kelipatan Tawar'),
                    DateTimePicker::make('start_date')
                        ->required()
                        ->label('Waktu Mulai'),
                    DateTimePicker::make('end_date')
                        ->required()
                        ->label('Waktu Berakhir'),
                    Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'active' => 'Aktif',
                            'ended' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ])
                        ->default('pending')
                        ->required()
                        ->label('Status'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->label('Produk')
                    ->limit(40),
                TextColumn::make('starting_bid')
                    ->money('IDR')
                    ->sortable()
                    ->label('Harga Awal'),
                TextColumn::make('current_bid')
                    ->money('IDR')
                    ->sortable()
                    ->label('Tawaran Saat Ini'),
                TextColumn::make('start_date')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Mulai'),
                TextColumn::make('end_date')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Berakhir'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'active' => 'success',
                        'ended' => 'warning',
                        'cancelled' => 'danger',
                    })
                    ->label('Status'),
                TextColumn::make('bids_count')
                    ->counts('bids')
                    ->label('Total Tawaran')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Aktif',
                        'ended' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->label('Status'),
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
            'index' => Pages\ListAuctions::route('/'),
            'create' => Pages\CreateAuction::route('/create'),
            'edit' => Pages\EditAuction::route('/{record}/edit'),
        ];
    }
}
