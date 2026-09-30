<x-filament-panels::page>
    <form wire:submit="loadReport">
        {{ $this->form }}
        <div class="flex gap-2 mt-4">
            <x-filament::button type="submit" color="primary" icon="heroicon-o-funnel">
                Tampilkan
            </x-filament::button>
            <x-filament::button type="button" color="gray" icon="heroicon-o-document-arrow-down" tag="a" href="#">
                Download PDF
            </x-filament::button>
        </div>
    </form>

    <div class="mt-6">
        <h3 class="text-sm font-semibold text-gray-500 mb-3">{{ $dateLabel }}</h3>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Total Pendapatan</p>
            <p class="text-xl font-bold text-success-500">Rp {{ number_format($reportData['total_revenue'] ?? 0, 0, ',', '.') }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Total Pesanan</p>
            <p class="text-xl font-bold text-primary-500">{{ number_format($reportData['total_orders'] ?? 0) }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Pesanan Dibayar</p>
            <p class="text-xl font-bold text-success-500">{{ number_format($reportData['paid_orders'] ?? 0) }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Pending</p>
            <p class="text-xl font-bold text-warning-500">{{ number_format($reportData['pending_orders'] ?? 0) }}</p>
        </x-filament::section>
        <x-filament::section class="text-center">
            <p class="text-xs text-gray-500 mb-1">Rata-rata Order</p>
            <p class="text-xl font-bold text-gray-700">Rp {{ number_format($reportData['avg_order'] ?? 0, 0, ',', '.') }}</p>
        </x-filament::section>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <x-filament::section>
            <h3 class="text-base font-semibold mb-4">Pendapatan</h3>
            <canvas id="revenueChart" height="250"></canvas>
        </x-filament::section>
        <x-filament::section>
            <h3 class="text-base font-semibold mb-4">Jumlah Pesanan</h3>
            <canvas id="ordersChart" height="250"></canvas>
        </x-filament::section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const labels = @json($reportData['labels'] ?? []);
            const totals = @json($reportData['totals'] ?? []);
            const counts = @json($reportData['counts'] ?? []);

            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: totals,
                        backgroundColor: 'rgba(79,70,229,0.6)',
                        borderColor: '#4f46e5',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            new Chart(document.getElementById('ordersChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pesanan',
                        data: counts,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        });
    </script>
</x-filament-panels::page>
