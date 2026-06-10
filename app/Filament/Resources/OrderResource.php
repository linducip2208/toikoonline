<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\PaymentGatewayConfig;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = '🛒 Pesanan';

    protected static ?int $navigationSort = 31;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info Pesanan')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Pesanan')
                            ->disabled(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Pelanggan')
                            ->disabled(),
                        Select::make('delivery_status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Dikonfirmasi',
                                'picked_up' => 'Diambil',
                                'on_delivery' => 'Dalam Pengiriman',
                                'delivered' => 'Terkirim',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required(),
                        Select::make('payment_type')
                            ->label('Metode Pembayaran')
                            ->options(function () {
                                return PaymentGatewayConfig::where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->nullable(),
                        Select::make('payment_status')
                            ->options([
                                'unpaid' => 'Belum Dibayar',
                                'paid' => 'Dibayar',
                                'refunded' => 'Direfund',
                            ])
                            ->required(),
                        TextInput::make('grand_total')
                            ->prefix('Rp')
                            ->disabled(),
                        TextInput::make('coupon_discount')
                            ->prefix('Rp')
                            ->disabled(),
                        Textarea::make('shipping_address')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grand_total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('delivery_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'confirmed' => 'info',
                        'picked_up' => 'warning',
                        'on_delivery' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'paid' => 'success',
                        'refunded' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('delivery_status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Dikonfirmasi',
                        'picked_up' => 'Diambil',
                        'on_delivery' => 'Dalam Pengiriman',
                        'delivered' => 'Terkirim',
                        'cancelled' => 'Dibatalkan',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Dibayar',
                        'refunded' => 'Direfund',
                    ]),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
