<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentTransactionResource\Pages;
use App\Models\PaymentTransaction;
use App\Services\Payment\RefundService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = '💳 Pembayaran';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')->label('Order')->searchable()->sortable(),
                TextColumn::make('gateway.name')->label('Gateway'),
                TextColumn::make('amount')->label('Amount')->money('IDR')->sortable(),
                TextColumn::make('status')->label('Status')->badge()->color(fn (string $state): string => match ($state) {
                    'paid' => 'success',
                    'pending', 'intent' => 'warning',
                    'refunded' => 'info',
                    default => 'danger',
                })->sortable(),
                TextColumn::make('gateway_reference')->label('Reference')->toggleable(),
                TextColumn::make('failed_signature_count')->label('Bad sig')->sortable(),
                TextColumn::make('created_at')->label('Created')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'intent' => 'Intent', 'pending' => 'Pending', 'paid' => 'Paid',
                    'failed' => 'Failed', 'refunded' => 'Refunded',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (PaymentTransaction $record): bool => $record->status === 'paid'
                        && $record->gateway
                        && app(RefundService::class)->isSupported($record->gateway))
                    ->action(fn (PaymentTransaction $record) => app(RefundService::class)->refund($record)),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentTransactions::route('/'),
            'view' => Pages\ViewPaymentTransaction::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
