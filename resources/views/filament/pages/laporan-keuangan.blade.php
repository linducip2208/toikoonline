<x-filament-panels::page>
    <form wire:submit="loadReport">
        {{ $this->form }}
        <div class="flex gap-2 mt-4">
            <x-filament::button type="submit" color="primary" icon="heroicon-o-funnel">Tampilkan</x-filament::button>
            <x-filament::button type="button" color="gray" icon="heroicon-o-document-arrow-down" tag="a" href="#">Download PDF</x-filament::button>
        </div>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6 mt-6">
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Total Pendapatan</p>
            <p class="text-xl font-bold text-success-500">Rp {{ number_format($reportData['total_revenue'] ?? 0, 0, ',', '.') }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Total Pesanan</p>
            <p class="text-xl font-bold text-primary-500">{{ number_format($reportData['total_orders'] ?? 0) }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Sudah Dibayar</p>
            <p class="text-xl font-bold text-success-500">Rp {{ number_format($reportData['paid_amount'] ?? 0, 0, ',', '.') }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Belum Dibayar</p>
            <p class="text-xl font-bold text-danger-500">Rp {{ number_format($reportData['unpaid_amount'] ?? 0, 0, ',', '.') }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Rata-rata Order</p>
            <p class="text-xl font-bold text-gray-700">Rp {{ number_format($reportData['avg_order'] ?? 0, 0, ',', '.') }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Transaksi Wallet</p>
            <p class="text-xl font-bold text-gray-700">{{ number_format($reportData['wallet_transactions'] ?? 0) }}</p>
        </x-filament::section>
    </div>

    <x-filament::section>
        <h3 class="text-base font-semibold mb-4">Pendapatan Bulanan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-gray-500">
                        <th class="py-2 px-3">Bulan</th>
                        <th class="py-2 px-3 text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['monthly_sales'] ?? [] as $row)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('F Y') }}</td>
                        <td class="py-2 px-3 text-right font-bold">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="py-4 text-center text-gray-400">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
