<x-filament-panels::page>
    <form wire:submit="loadReport">
        {{ $this->form }}
        <div class="flex gap-2 mt-4">
            <x-filament::button type="submit" color="primary" icon="heroicon-o-funnel">
                Tampilkan
            </x-filament::button>
        </div>
    </form>

    <div class="mt-6">
        <h3 class="text-sm font-semibold text-gray-500 mb-3">{{ $dateLabel }}</h3>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Pendapatan (dibayar)</p>
            <p class="text-xl font-bold text-success-500">Rp {{ number_format($stats['revenue'] ?? 0, 0, ',', '.') }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Pesanan</p>
            <p class="text-xl font-bold text-primary-500">{{ number_format($stats['orders'] ?? 0) }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Pelanggan Baru</p>
            <p class="text-xl font-bold text-primary-500">{{ number_format($stats['customers'] ?? 0) }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Rata-rata Order (AOV)</p>
            <p class="text-xl font-bold text-gray-700">Rp {{ number_format($stats['aov'] ?? 0, 0, ',', '.') }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Refund</p>
            <p class="text-xl font-bold text-danger-500">Rp {{ number_format($stats['refunds'] ?? 0, 0, ',', '.') }}</p>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <x-filament::section>
            <x-slot name="heading">Status Pembayaran</x-slot>
            @forelse(($stats['by_payment'] ?? []) as $status => $count)
                <div class="flex justify-between py-1 text-sm">
                    <span>{{ $status }}</span><strong>{{ $count }}</strong>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada data pada periode ini.</p>
            @endforelse
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">Status Pengiriman</x-slot>
            @forelse(($stats['by_delivery'] ?? []) as $status => $count)
                <div class="flex justify-between py-1 text-sm">
                    <span>{{ $status }}</span><strong>{{ $count }}</strong>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada data pada periode ini.</p>
            @endforelse
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <x-filament::section>
            <x-slot name="heading">Produk Terlaris</x-slot>
            @forelse($topProducts as $p)
                <div class="flex justify-between py-1 text-sm">
                    <span class="truncate mr-2">{{ $p['name'] }}</span><strong>{{ $p['sold'] }}</strong>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada penjualan pada periode ini.</p>
            @endforelse
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">Kategori Terlaris</x-slot>
            @forelse($topCategories as $c)
                <div class="flex justify-between py-1 text-sm">
                    <span class="truncate mr-2">{{ $c['name'] }}</span><strong>{{ $c['sold'] }}</strong>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada penjualan pada periode ini.</p>
            @endforelse
        </x-filament::section>
    </div>

    <x-filament::section>
        <x-slot name="heading">Stok Menipis</x-slot>
        @forelse($lowStock as $s)
            <div class="flex justify-between py-1 text-sm">
                <span class="truncate mr-2">{{ $s['product'] }}{{ $s['variant'] ? ' — '.$s['variant'] : '' }}</span>
                <strong class="{{ $s['qty'] <= 0 ? 'text-danger-500' : 'text-warning-500' }}">Sisa {{ $s['qty'] }}</strong>
            </div>
        @empty
            <p class="text-sm text-gray-500">Stok aman — tidak ada produk menipis.</p>
        @endforelse
    </x-filament::section>
</x-filament-panels::page>
