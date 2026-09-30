<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('code')->label('Kode'),
            ExportColumn::make('user.name')->label('Pelanggan'),
            ExportColumn::make('grand_total')->label('Total'),
            ExportColumn::make('payment_status')->label('Status Bayar'),
            ExportColumn::make('delivery_status')->label('Status Kirim'),
            ExportColumn::make('payment_type')->label('Metode Bayar'),
            ExportColumn::make('created_at')->label('Tanggal'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Ekspor ' . number_format($export->successful_rows) . ' pesanan berhasil.';
    }
}
