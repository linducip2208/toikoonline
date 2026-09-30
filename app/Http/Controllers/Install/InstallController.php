<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Http\Middleware\InstallLock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

/**
 * Web installer wizard. Lock-based only: runs whenever
 * storage/app/installed.lock is absent (including local dev), and is blocked
 * by InstallLock middleware once the lock exists.
 *
 * ROUTES (integrator: append to routes/web.php — this file must not be edited by Agent 4):
 *
 *   Route::prefix('install')->name('install.')->group(function () {
 *       Route::get('/', [InstallController::class, 'index'])->name('index');
 *       Route::post('/database', [InstallController::class, 'database'])->name('database');
 *       Route::post('/admin', [InstallController::class, 'admin'])->name('admin');
 *       Route::post('/store', [InstallController::class, 'store'])->name('store');
 *   });
 */
class InstallController extends Controller
{
    public function index()
    {
        if (InstallLock::isInstalled()) {
            return redirect()->to('/')->with('error', 'Aplikasi sudah terinstal.');
        }

        return view('install.index', [
            'requirements' => $this->requirements(),
            'installed' => false,
            'env' => [
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
                'db_host' => config('database.connections.mysql.host'),
                'db_port' => config('database.connections.mysql.port'),
                'db_database' => config('database.connections.mysql.database'),
                'db_username' => config('database.connections.mysql.username'),
            ],
        ]);
    }

    public function database(Request $request)
    {
        if (InstallLock::isInstalled()) {
            return redirect()->to('/')->with('error', 'Aplikasi sudah terinstal.');
        }

        $data = $request->validate([
            'db_host' => 'required|string|max:100',
            'db_port' => 'required|numeric|min:1|max:65535',
            'db_database' => 'required|string|max:100',
            'db_username' => 'required|string|max:100',
            'db_password' => 'nullable|string|max:200',
        ]);

        // 1. Test connection with the SUPPLIED credentials (no .env write yet).
        try {
            config([
                'database.connections.install_test' => array_merge(
                    config('database.connections.mysql'),
                    [
                        'host' => $data['db_host'],
                        'port' => $data['db_port'],
                        'database' => $data['db_database'],
                        'username' => $data['db_username'],
                        'password' => $data['db_password'] ?? '',
                    ]
                ),
            ]);
            DB::connection('install_test')->getPdo();
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Koneksi database gagal: '.$e->getMessage());
        }

        // 2. Persist to .env (keys only — password never echoed back to views).
        $this->writeEnv([
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => (string) $data['db_port'],
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'] ?? '',
        ]);

        // 3. Fresh config + migrate + seed.
        try {
            Artisan::call('config:clear');
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Migrasi/seeding gagal: '.$e->getMessage());
        }

        return redirect()->route('install.index')->with('success', 'Database terhubung, migrasi & seeding berhasil. Lanjut: buat akun admin.');
    }

    public function admin(Request $request)
    {
        if (InstallLock::isInstalled()) {
            return redirect()->to('/')->with('error', 'Aplikasi sudah terinstal.');
        }

        if (! Schema::hasTable('users')) {
            return back()->with('error', 'Tabel users belum ada — jalankan langkah Database dulu.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('super_admin');

        return redirect()->route('install.index')->with('success', 'Akun admin dibuat. Lanjut: konfigurasi toko & selesaikan instalasi.');
    }

    public function store(Request $request)
    {
        if (InstallLock::isInstalled()) {
            return redirect()->to('/')->with('error', 'Aplikasi sudah terinstal.');
        }

        $data = $request->validate([
            'app_name' => 'required|string|max:100',
            'app_url' => 'required|url|max:200',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50|timezone',
            'language' => 'required|string|in:id,en',
        ]);

        $this->writeEnv([
            'APP_NAME' => '"'.$data['app_name'].'"',
            'APP_URL' => $data['app_url'],
            'APP_TIMEZONE' => $data['timezone'],
            'APP_LOCALE' => $data['language'],
        ]);

        if (Schema::hasTable('business_settings')) {
            \App\Models\BusinessSetting::updateOrCreate(['key' => 'shop_name'], ['value' => $data['app_name']]);
            \App\Models\BusinessSetting::updateOrCreate(['key' => 'currency'], ['value' => $data['currency']]);
            \App\Models\BusinessSetting::updateOrCreate(['key' => 'timezone'], ['value' => $data['timezone']]);
        }

        Artisan::call('config:clear');

        file_put_contents(InstallLock::lockPath(), json_encode([
            'installed_at' => now()->toIso8601String(),
            'app' => $data['app_name'],
        ], JSON_PRETTY_PRINT));

        return view('install.finish', ['appName' => $data['app_name']]);
    }

    /** @return array<int, array{label: string, ok: bool, hint: string}> */
    public function requirements(): array
    {
        $checks = [];
        $checks[] = [
            'label' => 'PHP >= 8.2 (saat ini '.PHP_VERSION.')',
            'ok' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'hint' => 'Upgrade PHP ke 8.2+',
        ];
        foreach (['mbstring', 'openssl', 'pdo', 'pdo_mysql', 'tokenizer', 'xml', 'ctype', 'json', 'fileinfo', 'gd', 'intl'] as $ext) {
            $checks[] = [
                'label' => 'Ekstensi PHP: '.$ext,
                'ok' => extension_loaded($ext),
                'hint' => 'Aktifkan ekstensi '.$ext.' di php.ini',
            ];
        }
        foreach (['storage/app', 'storage/framework', 'storage/logs', 'bootstrap/cache'] as $dir) {
            $path = base_path($dir);
            $checks[] = [
                'label' => 'Writable: '.$dir,
                'ok' => is_dir($path) && is_writable($path),
                'hint' => 'chmod -R 775 '.$dir,
            ];
        }
        $checks[] = [
            'label' => '.env tersedia',
            'ok' => file_exists(base_path('.env')) || file_exists(base_path('.env.example')),
            'hint' => 'Salin .env.example menjadi .env',
        ];

        return $checks;
    }

    private function writeEnv(array $pairs): void
    {
        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }
        $content = file_get_contents($envPath);
        foreach ($pairs as $key => $value) {
            $escaped = addcslashes((string) $value, '"');
            $line = $key.'="'.$escaped.'"';
            if (preg_match('/^'.$key.'=.*$/m', $content)) {
                $content = preg_replace('/^'.$key.'=.*$/m', $line, $content);
            } else {
                $content .= "\n".$line."\n";
            }
        }
        file_put_contents($envPath, $content);
    }
}
