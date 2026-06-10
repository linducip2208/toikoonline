<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryBoyResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class DeliveryBoyResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = '🚚 Pengiriman';

    protected static ?int $navigationSort = 5;

    protected static ?string $label = 'Kurir';

    protected static ?string $pluralLabel = 'Kurir';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_type', 'delivery_boy');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Data Kurir')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(200)
                        ->label('Nama'),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->label('Email'),
                    TextInput::make('password')
                        ->password()
                        ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                        ->minLength(8)
                        ->label('Password')
                        ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                        ->dehydrated(fn($state) => filled($state)),
                    TextInput::make('phone')
                        ->tel()
                        ->label('Telepon'),
                    TextInput::make('address')
                        ->label('Alamat'),
                    Hidden::make('user_type')
                        ->default('delivery_boy'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),
                TextColumn::make('phone')
                    ->searchable()
                    ->label('Telepon'),
                TextColumn::make('sellerOrders_count')
                    ->counts('sellerOrders')
                    ->label('Pesanan Ditugaskan')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->label('Terdaftar'),
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
            'index' => Pages\ListDeliveryBoys::route('/'),
            'create' => Pages\CreateDeliveryBoy::route('/create'),
            'edit' => Pages\EditDeliveryBoy::route('/{record}/edit'),
        ];
    }
}
