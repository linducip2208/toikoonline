<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerGroupResource\Pages;
use App\Models\CustomerGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerGroupResource extends Resource
{
    protected static ?string $model = CustomerGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = '👥 Pelanggan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(100)->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('discount_percent')->numeric()->required()->default(0)
                ->minValue(0)->maxValue(100)->suffix('%'),
            Forms\Components\Textarea::make('description')->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('discount_percent')->label('Diskon %')->sortable(),
                Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Anggota'),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('manageMembers')
                    ->label('Kelola anggota')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Forms\Components\Select::make('user_ids')->label('Pelanggan')
                            ->options(\App\Models\User::orderBy('name')->limit(500)->pluck('name', 'id'))
                            ->multiple()->searchable()->required(),
                    ])
                    ->action(function (CustomerGroup $record, array $data) {
                        $record->users()->syncWithoutDetaching($data['user_ids']);
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
            'index' => Pages\ListCustomerGroups::route('/'),
            'create' => Pages\CreateCustomerGroup::route('/create'),
            'edit' => Pages\EditCustomerGroup::route('/{record}/edit'),
        ];
    }
}
