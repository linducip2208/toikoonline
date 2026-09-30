<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebhookSubscriptionResource\Pages;
use App\Models\WebhookDelivery;
use App\Models\WebhookSubscription;
use App\Services\Webhook\WebhookDispatcher;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebhookSubscriptionResource extends Resource
{
    protected static ?string $model = WebhookSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = '💳 Pembayaran';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('url')->label('URL')->url()->required()->maxLength(1024)->columnSpanFull(),
            Select::make('event')->label('Event')->options(array_combine(
                WebhookDispatcher::supportedEvents(),
                WebhookDispatcher::supportedEvents()
            ))->required(),
            TextInput::make('secret')->label('Signing secret')->password()->revealable()->required()->maxLength(255)
                ->helperText('Used for HMAC-SHA256 signature (X-Webhook-Signature).'),
            Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')->label('URL')->limit(50)->searchable(),
                TextColumn::make('event')->label('Event')->badge(),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('created_at')->label('Created')->dateTime('d M Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('testSend')
                    ->label('Test send')
                    ->icon('heroicon-o-paper-airplane')
                    ->action(function (WebhookSubscription $record) {
                        $delivery = WebhookDelivery::create([
                            'subscription_id' => $record->id,
                            'event' => $record->event,
                            'payload' => ['ping' => true, 'sent_at' => now()->toIso8601String()],
                            'status' => 'pending',
                            'attempts' => 0,
                        ]);
                        WebhookDispatcher::attempt($delivery->fresh());
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebhookSubscriptions::route('/'),
            'create' => Pages\CreateWebhookSubscription::route('/create'),
            'edit' => Pages\EditWebhookSubscription::route('/{record}/edit'),
        ];
    }
}
