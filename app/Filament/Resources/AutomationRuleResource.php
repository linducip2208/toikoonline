<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutomationRuleResource\Pages;
use App\Models\AutomationRule;
use App\Services\Webhook\WebhookDispatcher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AutomationRuleResource extends Resource
{
    protected static ?string $model = AutomationRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = '🤖 Otomatisasi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama aturan')->required()->maxLength(128),
            Forms\Components\Select::make('event')->label('Event')->required()
                ->options(array_combine(WebhookDispatcher::supportedEvents(), WebhookDispatcher::supportedEvents())),
            Forms\Components\Repeater::make('conditions')->label('Syarat (semua harus cocok / AND)')
                ->schema([
                    Forms\Components\TextInput::make('field')->label('Field (cth: total, user_id, rating)')
                        ->required()->maxLength(64),
                    Forms\Components\Select::make('operator')->label('Operator')->required()
                        ->options(array_combine(AutomationRule::OPERATORS, AutomationRule::OPERATORS)),
                    Forms\Components\TextInput::make('value')->label('Nilai')->maxLength(255),
                ])
                ->columns(3)->columnSpanFull()->collapsible(),
            Forms\Components\Repeater::make('actions')->label('Aksi')
                ->schema([
                    Forms\Components\Select::make('kind')->label('Jenis')->required()
                        ->options(['notify' => 'Notifikasi', 'email' => 'Email', 'coupon' => 'Kupon', 'webhook' => 'Webhook'])
                        ->reactive(),
                    Forms\Components\TextInput::make('title')->label('Judul (notify)')->maxLength(128),
                    Forms\Components\TextInput::make('message')->label('Pesan (notify)')->maxLength(255),
                    Forms\Components\TextInput::make('to')->label('Ke email (email, kosong = email pembeli)')
                        ->email()->maxLength(255),
                    Forms\Components\TextInput::make('discount')->label('Nominal kupon IDR (coupon)')
                        ->numeric()->minValue(0),
                    Forms\Components\TextInput::make('code_prefix')->label('Prefix kode (coupon)')->maxLength(16),
                    Forms\Components\Select::make('event')->label('Event webhook (webhook)')
                        ->options(array_combine(WebhookDispatcher::supportedEvents(), WebhookDispatcher::supportedEvents())),
                ])
                ->columns(2)->columnSpanFull()->collapsible(),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('event')->label('Event')->badge(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('last_run_at')->label('Terakhir jalan')->dateTime('d M Y H:i')->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutomationRules::route('/'),
            'create' => Pages\CreateAutomationRule::route('/create'),
            'edit' => Pages\EditAutomationRule::route('/{record}/edit'),
        ];
    }
}
