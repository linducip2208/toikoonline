<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = '🏢 B2B';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('company_id')->label('Perusahaan')
                ->relationship('company', 'name')->searchable()->required(),
            Forms\Components\Select::make('user_id')->label('Pengguna')
                ->relationship('user', 'name')->searchable()->required(),
            Forms\Components\Select::make('status')->options([
                'draft' => 'Draf', 'sent' => 'Terkirim', 'approved' => 'Disetujui',
                'rejected' => 'Ditolak', 'expired' => 'Kedaluwarsa',
            ])->disabled(),
            Forms\Components\DateTimePicker::make('valid_until')->label('Berlaku hingga'),
            Forms\Components\Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            Forms\Components\Repeater::make('items')->label('Item')->relationship('items')
                ->schema([
                    Forms\Components\Select::make('product_id')->label('Produk')
                        ->options(\App\Models\Product::orderBy('name')->limit(500)->pluck('name', 'id'))
                        ->searchable()->required(),
                    Forms\Components\TextInput::make('variant')->label('Varian')->maxLength(255),
                    Forms\Components\TextInput::make('qty')->numeric()->default(1)->minValue(1)->required(),
                    Forms\Components\TextInput::make('price')->label('Harga (Rp)')->numeric()->required()->minValue(0),
                ])->columns(4)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('company.name')->label('Perusahaan')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Pengguna'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success', 'rejected' => 'danger', 'expired' => 'gray',
                        'sent' => 'info', default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('coupon.code')->label('Kupon')->placeholder('-'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui + buat kupon')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn (Quote $record) => in_array($record->status, ['draft', 'sent'], true) && ! $record->coupon_id)
                    ->form([
                        Forms\Components\TextInput::make('discount')->label('Diskon')->numeric()->default(0)->minValue(0)->required(),
                        Forms\Components\Select::make('discount_type')->label('Tipe')
                            ->options(['fixed' => 'Nominal (Rp)', 'percent' => 'Persen (%)'])->default('fixed')->required(),
                    ])
                    ->action(function (Quote $record, array $data) {
                        app(\App\Services\B2B\QuoteService::class)->approve($record, [
                            'discount' => $data['discount'],
                            'discount_type' => $data['discount_type'],
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
