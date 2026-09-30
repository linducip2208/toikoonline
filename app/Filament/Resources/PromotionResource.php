<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = '🎫 Promo';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama promosi')->required()->maxLength(128),
            Forms\Components\Select::make('type')->label('Tipe')->required()->options([
                'auto_category_percent' => 'Diskon % kategori',
                'auto_bogo' => 'BOGO (termurah gratis per pasang)',
                'auto_tier' => 'Bertingkat (threshold subtotal)',
                'auto_free_shipping' => 'Gratis ongkir (threshold)',
            ]),
            Forms\Components\Textarea::make('config')->label('Config (JSON)')
                ->helperText('category_percent: {"category_id":1,"percent":10,"max_discount":20000} · bogo: {"product_id":5} atau {"category_id":2} · tier: {"tiers":[{"min_subtotal":100000,"percent":5}]} · free_shipping: {"min_subtotal":150000,"discount_amount":10000}')
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state) : $state)
                ->dehydrateStateUsing(fn ($state) => $state ? json_decode((string) $state, true) : null)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('priority')->label('Prioritas (kecil dulu)')->numeric()->default(0),
            Forms\Components\DateTimePicker::make('starts_at')->label('Mulai'),
            Forms\Components\DateTimePicker::make('ends_at')->label('Berakhir'),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipe')->badge(),
                Tables\Columns\TextColumn::make('priority')->label('Prioritas')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('starts_at')->label('Mulai')->dateTime('d M Y')->toggleable(),
                Tables\Columns\TextColumn::make('ends_at')->label('Berakhir')->dateTime('d M Y')->toggleable(),
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
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
