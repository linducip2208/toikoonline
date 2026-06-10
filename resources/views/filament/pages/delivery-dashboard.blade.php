<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-box text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-stone-900">{{ $assignedCount }}</p>
                        <p class="text-xs text-stone-500">Pesanan Ditugaskan</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-stone-900">{{ $deliveredToday }}</p>
                        <p class="text-xs text-stone-500">Terkirim Hari Ini</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                        <i class="fas fa-clock text-amber-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-stone-900">{{ $pendingPickup }}</p>
                        <p class="text-xs text-stone-500">Menunggu Pickup</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-money-bill text-purple-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-stone-900">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</p>
                        <p class="text-xs text-stone-500">Total Pendapatan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-stone-200">
            <div class="p-4 border-b border-stone-200">
                <h3 class="text-lg font-semibold text-stone-800">Daftar Pesanan</h3>
            </div>
            <div class="p-4">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
