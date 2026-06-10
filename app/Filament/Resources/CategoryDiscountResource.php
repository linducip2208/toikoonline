<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryDiscountResource\Pages;
use App\Models\CategoryDiscount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class CategoryDiscountResource extends Resource
{
    protected static ?string $model = CategoryDiscount::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = '🎫 Promo';

    protected static ?int $navigationSort = 4;

    protected static ?string $label = 'Diskon Kategori';

    protected static ?string $pluralLabel = 'Diskon Kategori';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Detail Diskon Kategori')
                ->schema([
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label('Kategori'),
                    TextInput::make('discount')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->label('Diskon'),
                    Select::make('discount_type')
                        ->options([
                            'percent' => 'Persen (%)',
                            'amount' => 'Nominal (Rp)',
                        ])
                        ->default('percent')
                        ->required()
                        ->label('Tipe Diskon'),
                    DateTimePicker::make('start_date')
                        ->label('Tanggal Mulai'),
                    DateTimePicker::make('end_date')
                        ->label('Tanggal Berakhir'),
                    Toggle::make('is_active')
                        ->default(true)
                        ->label('Aktif'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->label('Kategori'),
                TextColumn::make('discount')
                    ->money('IDR')
                    ->sortable()
                    ->label('Diskon'),
                TextColumn::make('discount_type')
                    ->badge()
                    ->label('Tipe'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable()
                    ->label('Aktif'),
                TextColumn::make('start_date')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->label('Mulai'),
                TextColumn::make('end_date')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->label('Berakhir'),
            ])
            ->filters([
                SelectFilter::make('discount_type')
                    ->options([
                        'percent' => 'Persen',
                        'amount' => 'Nominal',
                    ])
                    ->label('Tipe Diskon'),
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
            'index' => Pages\ListCategoryDiscounts::route('/'),
            'create' => Pages\CreateCategoryDiscount::route('/create'),
            'edit' => Pages\EditCategoryDiscount::route('/{record}/edit'),
        ];
    }
}
