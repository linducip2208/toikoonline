<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Nama'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('phone')->label('Telepon'),
            ExportColumn::make('city')->label('Kota'),
            ExportColumn::make('user_type')->label('Tipe'),
            ExportColumn::make('created_at')->label('Terdaftar'),
        ];
    }
}
