<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingConfigResource\Pages;
use App\Models\ShippingConfig;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;

class ShippingConfigResource extends Resource
{
    protected static ?string $model = ShippingConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = '🚚 Pengiriman';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info Provider')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Provider')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: RajaOngkir Pro'),
                        Select::make('provider_format')
                            ->label('Format Provider')
                            ->options([
                                'rajaongkir' => 'RajaOngkir',
                                'biteship' => 'Biteship',
                                'jne' => 'JNE',
                                'tiki' => 'Tiki',
                                'pos' => 'POS Indonesia',
                                'custom' => 'Custom',
                            ])
                            ->required()
                            ->native(false),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Section::make('Kredensial API')
                    ->schema([
                        TextInput::make('api_key_encrypted')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('Masukkan API key dari shipping provider'),
                        TextInput::make('base_url')
                            ->label('Base URL')
                            ->url()
                            ->placeholder('https://api.rajaongkir.com/starter'),
                    ])->columns(2),

                Section::make('Parameter Tambahan')
                    ->schema([
                        KeyValue::make('extra_params')
                            ->label('Extra Parameters')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->addButtonLabel('Tambah Parameter')
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Aktifkan shipping provider ini')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
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
                TextColumn::make('provider_format')
                    ->label('Format')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'rajaongkir' => 'info',
                        'biteship' => 'primary',
                        'jne' => 'success',
                        'tiki' => 'warning',
                        'pos' => 'danger',
                        'custom' => 'gray',
                        default => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->label('Aktif')
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
                Tables\Filters\SelectFilter::make('provider_format')
                    ->label('Format')
                    ->options([
                        'rajaongkir' => 'RajaOngkir',
                        'biteship' => 'Biteship',
                        'jne' => 'JNE',
                        'tiki' => 'Tiki',
                        'pos' => 'POS Indonesia',
                        'custom' => 'Custom',
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
            'index' => Pages\ListShippingConfigs::route('/'),
            'create' => Pages\CreateShippingConfig::route('/create'),
            'edit' => Pages\EditShippingConfig::route('/{record}/edit'),
        ];
    }
}
