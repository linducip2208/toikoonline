<x-filament-panels::page>
    <form wire:submit="loadReport">
        {{ $this->form }}
        <div class="flex gap-2 mt-4">
            <x-filament::button type="submit" color="primary" icon="heroicon-o-funnel">Filter</x-filament::button>
        </div>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 mt-6">
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Total Produk</p>
            <p class="text-xl font-bold text-primary-500">{{ number_format($reportData['total_products'] ?? 0) }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Published</p>
            <p class="text-xl font-bold text-success-500">{{ number_format($reportData['published'] ?? 0) }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Stok Habis</p>
            <p class="text-xl font-bold text-danger-500">{{ number_format($reportData['out_of_stock'] ?? 0) }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Stok Menipis</p>
            <p class="text-xl font-bold text-warning-500">{{ number_format($reportData['low_stock'] ?? 0) }}</p>
        </x-filament::section>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <x-filament::section>
            <h3 class="text-base font-semibold mb-4">10 Produk Terlaris</h3>
            <div class="space-y-2">
                @forelse($reportData['top_sellers'] ?? [] as $i => $p)
                <div class="flex items-center gap-3 py-1.5 border-b text-sm">
                    <span class="w-6 text-center font-bold text-gray-400">{{ $i + 1 }}</span>
                    <span class="flex-1 truncate">{{ $p->name }}</span>
                    <span class="text-success-600 font-semibold">{{ number_format($p->num_of_sale) }} terjual</span>
                </div>
                @empty
                <p class="text-gray-400 text-sm">Tidak ada data</p>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section>
            <h3 class="text-base font-semibold mb-4">10 Produk Rating Tertinggi</h3>
            <div class="space-y-2">
                @forelse($reportData['top_rated'] ?? [] as $i => $p)
                <div class="flex items-center gap-3 py-1.5 border-b text-sm">
                    <span class="w-6 text-center font-bold text-gray-400">{{ $i + 1 }}</span>
                    <span class="flex-1 truncate">{{ $p->name }}</span>
                    <span class="text-warning-500 font-semibold">
                        <i class="heroicon-s-star text-xs"></i> {{ number_format($p->rating, 1) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-400 text-sm">Tidak ada data</p>
                @endforelse
            </div>
        </x-filament::section>
    </div>

    <x-filament::section class="mt-6">
        <h3 class="text-base font-semibold mb-4">Produk per Kategori</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-gray-500">
                        <th class="py-2 px-3">Kategori</th>
                        <th class="py-2 px-3 text-right">Jumlah Produk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['by_category'] ?? [] as $cat)
                    <tr class="border-b">
                        <td class="py-2 px-3">{{ $cat->name }}</td>
                        <td class="py-2 px-3 text-right font-bold">{{ $cat->products_count }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="py-4 text-center text-gray-400">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
