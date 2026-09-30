<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2">
        @foreach($this->checks as $check)
            <div class="rounded-xl border p-4 {{ $check['status'] === 'ok' ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                <div class="flex items-center gap-2 font-bold {{ $check['status'] === 'ok' ? 'text-green-800' : 'text-red-800' }}">
                    <span>{{ $check['status'] === 'ok' ? '✅' : '❌' }}</span>
                    {{ $check['label'] }}
                </div>
                <p class="mt-1 text-sm {{ $check['status'] === 'ok' ? 'text-green-700' : 'text-red-700' }}">{{ $check['detail'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-xl border border-stone-200 bg-white p-4">
        <h3 class="font-bold text-stone-900">Penggunaan Storage</h3>
        @php $usage = $this->getStorageUsage(); @endphp
        <p class="mt-1 text-sm text-stone-600">Bebas {{ $usage['free'] }} dari {{ $usage['total'] }} ({{ $usage['pct_free'] }}% bebas)</p>
        @if($this->backupOutput)
            <p class="mt-2 rounded-lg bg-stone-100 p-2 text-xs text-stone-700">{{ $this->backupOutput }}</p>
        @endif
    </div>

    <div class="mt-6 rounded-xl border border-stone-200 bg-white p-4">
        <h3 class="font-bold text-stone-900">Backup Terakhir (maks. 10)</h3>
        @php $backups = $this->getBackups(); @endphp
        @if(count($backups) > 0)
            <ul class="mt-2 space-y-1 text-sm text-stone-600">
                @foreach($backups as $b)
                    <li>{{ $b['name'] }} — {{ $b['size'] }} — {{ $b['date'] }}</li>
                @endforeach
            </ul>
        @else
            <p class="mt-1 text-sm text-stone-500">Belum ada file backup di storage/app/backups.</p>
        @endif
    </div>
</x-filament-panels::page>
