<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\RefundRequest;
use App\Services\Refund\RefundService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RefundRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'refundRequests';

    protected static ?string $title = 'Pengajuan Refund';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Pelanggan'),
                Tables\Columns\TextColumn::make('refund_status')->label('Status')->badge(),
                Tables\Columns\TextColumn::make('refund_amount')->label('Nominal')->money('IDR'),
                Tables\Columns\TextColumn::make('refund_reason')->label('Alasan')->limit(40),
                Tables\Columns\TextColumn::make('created_at')->label('Diajukan')->dateTime()->sortable(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui refund')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (RefundRequest $record) => $record->refund_status === 'pending')
                    ->form([Forms\Components\Textarea::make('note')->label('Catatan internal')->nullable()])
                    ->action(function (RefundRequest $record, array $data, RefundService $service) {
                        $service->approve($record, auth()->id(), $data['note'] ?? null);
                        Notification::make()->title('Refund disetujui, stok dikembalikan')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (RefundRequest $record) => $record->refund_status === 'pending')
                    ->form([Forms\Components\Textarea::make('reject_reason')->label('Alasan penolakan')->required()])
                    ->action(function (RefundRequest $record, array $data, RefundService $service) {
                        $service->reject($record, $data['reject_reason'], auth()->id());
                        Notification::make()->title('Refund ditolak')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }
}
