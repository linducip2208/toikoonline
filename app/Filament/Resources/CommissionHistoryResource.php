<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommissionHistoryResource\Pages;
use App\Models\CommissionHistory;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommissionHistoryResource extends Resource
{
    protected static ?string $model = CommissionHistory::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Komisi';
    protected static ?string $pluralLabel = 'Riwayat Komisi';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('order.code')->label('Order')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('seller.name')->label('Penjual')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('admin_commission')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('seller_earning')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['order', 'seller']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommissionHistories::route('/'),
        ];
    }
}
