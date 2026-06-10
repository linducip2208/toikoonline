<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentGatewayConfigResource\Pages;
use App\Models\PaymentGatewayConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class PaymentGatewayConfigResource extends Resource
{
    protected static ?string $model = PaymentGatewayConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = '💳 Pembayaran';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info Gateway')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Gateway')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Midtrans Production'),
                        Select::make('gateway_format')
                            ->label('Format Gateway')
                            ->options([
                                'midtrans-snap' => 'Midtrans Snap (Redirect)',
                                'midtrans-core' => 'Midtrans Core API (VA/QR)',
                                'xendit-invoice' => 'Xendit Invoice',
                                'tripay-closed' => 'Tripay Closed Transaction',
                                'duitku-redirect' => 'Duitku (Redirect)',
                                'oyindonesia-api' => 'OY! Indonesia',
                                'ipaymu-api' => 'iPaymu',
                                'faspay-api' => 'Faspay',
                                'doku-api' => 'DOKU',
                                'esiapay-api' => 'ESIA Pay',
                            ])
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->live(),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Section::make('Kredensial API')
                    ->schema([
                        TextInput::make('api_key_encrypted')
                            ->label('API Key / Server Key')
                            ->password()
                            ->revealable()
                            ->placeholder('Masukkan API key dari payment gateway'),
                        TextInput::make('api_secret_encrypted')
                            ->label('API Secret / Client Key')
                            ->password()
                            ->revealable()
                            ->placeholder('Masukkan API secret dari payment gateway'),
                        TextInput::make('merchant_id')
                            ->label('Merchant ID / Client ID')
                            ->placeholder('Masukkan Merchant ID (jika ada)'),
                        TextInput::make('base_url')
                            ->label('Base URL')
                            ->url()
                            ->placeholder('https://app.sandbox.midtrans.com/snap/v1'),
                    ])->columns(2),

                Section::make('Konfigurasi Tambahan')
                    ->schema([
                        KeyValue::make('extra_headers')
                            ->label('Extra Headers')
                            ->keyLabel('Header Key')
                            ->valueLabel('Header Value')
                            ->addButtonLabel('Tambah Header')
                            ->columnSpanFull(),
                        KeyValue::make('config')
                            ->label('Config')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->addButtonLabel('Tambah Config')
                            ->helperText('merchant_code (Tripay), channels, dll')
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Aktifkan gateway ini untuk menerima pembayaran')
                            ->onColor('success')
                            ->offColor('danger'),
                        Toggle::make('is_sandbox')
                            ->label('Mode Sandbox')
                            ->helperText('ON = testing, OFF = production')
                            ->onColor('warning')
                            ->offColor('success'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gateway_format')
                    ->label('Format')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'midtrans-snap' => 'info',
                        'midtrans-core' => 'primary',
                        'xendit-invoice' => 'success',
                        'tripay-closed' => 'warning',
                        'duitku-redirect' => 'danger',
                        'oyindonesia-api' => 'violet',
                        'ipaymu-api' => 'orange',
                        'faspay-api' => 'teal',
                        'doku-api' => 'sky',
                        'esiapay-api' => 'rose',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'midtrans-snap' => 'Midtrans Snap',
                        'midtrans-core' => 'Midtrans Core',
                        'xendit-invoice' => 'Xendit Invoice',
                        'tripay-closed' => 'Tripay',
                        'duitku-redirect' => 'Duitku',
                        'oyindonesia-api' => 'OY! Indonesia',
                        'ipaymu-api' => 'iPaymu',
                        'faspay-api' => 'Faspay',
                        'doku-api' => 'DOKU',
                        'esiapay-api' => 'ESIA Pay',
                        default => $state,
                    }),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                IconColumn::make('is_sandbox')
                    ->label('Sandbox')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gateway_format')
                    ->label('Format')
                    ->options([
                        'midtrans-snap' => 'Midtrans Snap',
                        'midtrans-core' => 'Midtrans Core',
                        'xendit-invoice' => 'Xendit Invoice',
                        'tripay-closed' => 'Tripay',
                        'duitku-redirect' => 'Duitku',
                        'oyindonesia-api' => 'OY! Indonesia',
                        'ipaymu-api' => 'iPaymu',
                        'faspay-api' => 'Faspay',
                        'doku-api' => 'DOKU',
                        'esiapay-api' => 'ESIA Pay',
                    ]),
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Aktif',
                        false => 'Nonaktif',
                    ]),
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
            'index' => Pages\ListPaymentGatewayConfigs::route('/'),
            'create' => Pages\CreatePaymentGatewayConfig::route('/create'),
            'edit' => Pages\EditPaymentGatewayConfig::route('/{record}/edit'),
        ];
    }
}
