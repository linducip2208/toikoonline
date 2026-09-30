<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Models\Wallet;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;
    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Pelanggan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('payment_method')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWallets::route('/'),
        ];
    }
}
