<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HealthCheckCommand extends Command
{
    protected $signature = 'health:check {--json : Output hasil sebagai JSON}';
    protected $description = 'Cek kesehatan sistem: database, storage, cache, queue, disks';

    public function handle(): int
    {
        $results = self::runChecks();

        if ($this->option('json')) {
            $this->line(json_encode($results, JSON_PRETTY_PRINT));

            return collect($results)->contains(fn ($r) => $r['status'] === 'fail') ? 1 : 0;
        }

        foreach ($results as $check) {
            $icon = $check['status'] === 'ok' ? '<info>OK</info>' : '<error>FAIL</error>';
            $this->line("[{$icon}] {$check['label']}: {$check['detail']}");
        }

        return collect($results)->contains(fn ($r) => $r['status'] === 'fail') ? 1 : 0;
    }

    /** @return array<int, array{key: string, label: string, status: string, detail: string}> */
    public static function runChecks(): array
    {
        $out = [];

        try {
            DB::connection()->getPdo();
            $out[] = ['key' => 'db', 'label' => 'Database', 'status' => 'ok', 'detail' => 'Koneksi '.config('database.default').' berhasil'];
        } catch (\Throwable $e) {
            $out[] = ['key' => 'db', 'label' => 'Database', 'status' => 'fail', 'detail' => $e->getMessage()];
        }

        $storagePath = storage_path('app');
        $out[] = [
            'key' => 'storage',
            'label' => 'Storage writable',
            'status' => is_writable($storagePath) ? 'ok' : 'fail',
            'detail' => $storagePath.(is_writable($storagePath) ? ' dapat ditulis' : ' TIDAK dapat ditulis'),
        ];

        try {
            Cache::put('health:ping', 'pong', 60);
            $ok = Cache::get('health:ping') === 'pong';
            $out[] = ['key' => 'cache', 'label' => 'Cache ('.config('cache.default').')', 'status' => $ok ? 'ok' : 'fail', 'detail' => $ok ? 'Read/write cache berhasil' : 'Cache read/write gagal'];
        } catch (\Throwable $e) {
            $out[] = ['key' => 'cache', 'label' => 'Cache', 'status' => 'fail', 'detail' => $e->getMessage()];
        }

        try {
            if (config('queue.default') === 'database' && Schema::hasTable('jobs')) {
                $pending = DB::table('jobs')->count();
                $failed = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
                $out[] = ['key' => 'queue', 'label' => 'Queue (database)', 'status' => $failed > 50 ? 'fail' : 'ok', 'detail' => "{$pending} pending, {$failed} failed"];
            } else {
                $out[] = ['key' => 'queue', 'label' => 'Queue ('.config('queue.default').')', 'status' => 'ok', 'detail' => 'Driver non-database, tidak ada antrean lokal'];
            }
        } catch (\Throwable $e) {
            $out[] = ['key' => 'queue', 'label' => 'Queue', 'status' => 'fail', 'detail' => $e->getMessage()];
        }

        $free = @disk_free_space(storage_path());
        $total = @disk_total_space(storage_path());
        if ($free !== false && $total) {
            $pct = round($free / $total * 100, 1);
            $out[] = ['key' => 'disk', 'label' => 'Disk storage', 'status' => $pct < 10 ? 'fail' : 'ok', 'detail' => 'Bebas '.self::bytes($free).' dari '.self::bytes($total)." ({$pct}%)"];
        } else {
            $out[] = ['key' => 'disk', 'label' => 'Disk storage', 'status' => 'fail', 'detail' => 'Tidak dapat membaca info disk'];
        }

        foreach (self::integrationChecks() as $check) {
            $out[] = $check;
        }

        return $out;
    }

    /**
     * Peringatan konfigurasi integrasi (payment/shipping/gudang).
     * Status 'warn' — tidak menggagalkan health, tapi tampil di dashboard.
     */
    public static function integrationChecks(): array
    {
        $out = [];
        try {
            if (! Schema::hasTable('payment_gateway_configs')) {
                return $out;
            }
            $gw = \App\Models\PaymentGatewayConfig::where('is_active', true)->count();
            $out[] = [
                'key' => 'payment-gateway', 'label' => 'Payment gateway aktif',
                'status' => $gw > 0 ? 'ok' : 'warn',
                'detail' => $gw > 0 ? "{$gw} gateway aktif" : 'Belum ada gateway aktif — checkout online nonaktif, COD/manual tetap jalan',
            ];
            $sh = \App\Models\ShippingConfig::where('is_active', true)->count();
            $out[] = [
                'key' => 'shipping-provider', 'label' => 'Shipping provider aktif',
                'status' => $sh > 0 ? 'ok' : 'warn',
                'detail' => $sh > 0 ? "{$sh} provider aktif" : 'Belum ada provider aktif — ongkir live mati, gunakan kurir flat/manual',
            ];
            $origin = \App\Models\BusinessSetting::getValue('warehouse_area_id', env('SHOP_ORIGIN_AREA_ID', env('SHOP_ORIGIN_CITY_ID', '')));
            $out[] = [
                'key' => 'warehouse-origin', 'label' => 'Kota asal gudang',
                'status' => $origin ? 'ok' : 'warn',
                'detail' => $origin ? "Asal: {$origin}" : 'Belum diatur — isi warehouse_area_id / SHOP_ORIGIN_AREA_ID agar ongkir live jalan',
            ];
            $snap = (bool) env('MIDTRANS_SERVER_KEY');
            $out[] = [
                'key' => 'midtrans-keys', 'label' => 'Kunci Midtrans',
                'status' => $snap || $gw > 0 ? 'ok' : 'warn',
                'detail' => $snap ? 'MIDTRANS_SERVER_KEY terisi' : ($gw > 0 ? 'Gunakan kunci dari konfigurasi gateway (DB)' : 'Belum ada kunci — pembayaran online nonaktif'),
            ];
        } catch (\Throwable $e) {
            $out[] = ['key' => 'integrations', 'label' => 'Integrasi', 'status' => 'warn', 'detail' => $e->getMessage()];
        }

        return $out;
    }

    public static function bytes(int|float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
