<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class DeliveryDashboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = '🚚 Pengiriman';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.delivery-dashboard';

    protected static ?string $title = 'Dashboard Kurir';

    public static function canAccess(): bool
    {
        return Auth::user()?->user_type === 'delivery_boy' || Auth::user()?->hasRole('admin');
    }

    public function getViewData(): array
    {
        $deliveryBoyId = Auth::id();

        return [
            'assignedCount' => Order::where('assign_delivery_boy', $deliveryBoyId)->count(),
            'deliveredToday' => Order::where('assign_delivery_boy', $deliveryBoyId)
                ->where('delivery_status', 'delivered')
                ->whereDate('updated_at', today())
                ->count(),
            'pendingPickup' => Order::where('assign_delivery_boy', $deliveryBoyId)
                ->where('delivery_status', 'pending')
                ->count(),
            'totalEarnings' => Order::where('assign_delivery_boy', $deliveryBoyId)
                ->where('delivery_status', 'delivered')
                ->sum('grand_total'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->where('assign_delivery_boy', Auth::id()))
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->label('Kode Pesanan'),
                TextColumn::make('user.name')
                    ->searchable()
                    ->label('Pelanggan'),
                TextColumn::make('shipping_address')
                    ->limit(50)
                    ->label('Alamat'),
                TextColumn::make('delivery_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'picked_up' => 'warning',
                        'on_the_way' => 'info',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),
                TextColumn::make('grand_total')
                    ->money('IDR')
                    ->label('Total'),
            ])
            ->actions([
                Action::make('accept')
                    ->label('Terima')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(Order $record) => $record->delivery_status === 'pending')
                    ->action(fn(Order $record) => $record->update(['delivery_status' => 'picked_up'])),
                Action::make('pickup')
                    ->label('Ambil')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn(Order $record) => $record->delivery_status === 'picked_up')
                    ->action(fn(Order $record) => $record->update(['delivery_status' => 'on_the_way'])),
                Action::make('delivered')
                    ->label('Terkirim')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Order $record) => in_array($record->delivery_status, ['picked_up', 'on_the_way']))
                    ->action(fn(Order $record) => $record->update(['delivery_status' => 'delivered'])),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
