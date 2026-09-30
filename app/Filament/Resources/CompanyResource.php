<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = '🏢 B2B';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(150),
            Forms\Components\TextInput::make('code')->label('Kode')->required()->maxLength(50)->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('tax_id')->label('NPWP')->maxLength(50),
            Forms\Components\TextInput::make('payment_terms_days')->label('Termin (hari)')->numeric()->default(0)->minValue(0)->maxValue(365),
            Forms\Components\TextInput::make('credit_limit')->label('Limit kredit (Rp)')->numeric()->default(0)->minValue(0),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Kode')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Anggota'),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('attachUser')
                    ->label('Tambah anggota')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Forms\Components\Select::make('user_id')->label('Pengguna')
                            ->options(\App\Models\User::orderBy('name')->limit(500)->pluck('name', 'id'))
                            ->searchable()->required(),
                        Forms\Components\Select::make('role')->options(['owner' => 'Owner', 'staff' => 'Staff'])->default('staff')->required(),
                        Forms\Components\Toggle::make('can_approve')->label('Bisa menyetujui'),
                    ])
                    ->action(function (Company $record, array $data) {
                        app(\App\Services\B2B\CompanyService::class)->attachUser(
                            $record,
                            \App\Models\User::findOrFail($data['user_id']),
                            $data['role'],
                            (bool) ($data['can_approve'] ?? false)
                        );
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
