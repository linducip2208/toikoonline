<?php

namespace App\Filament\Pages;

use App\Console\Commands\HealthCheckCommand;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class SystemHealth extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = '⚙️ Sistem';

    protected static ?string $navigationLabel = 'Kesehatan Sistem';

    protected static ?string $title = 'Kesehatan Sistem';

    protected static string $view = 'filament.pages.system-health';

    public array $checks = [];

    public string $backupOutput = '';

    public function mount(): void
    {
        $this->checks = HealthCheckCommand::runChecks();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Jalankan Ulang Pemeriksaan')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->checks = HealthCheckCommand::runChecks();
                    Notification::make()->title('Pemeriksaan selesai')->success()->send();
                }),
            Action::make('backup')
                ->label('Backup Database Sekarang')
                ->icon('heroicon-o-circle-stack')
                ->requiresConfirmation()
                ->action(function () {
                    Artisan::call('backup:database');
                    $this->backupOutput = trim(Artisan::output());
                    Notification::make()->title('Backup dijalankan')->body($this->backupOutput ?: 'Selesai')->success()->send();
                }),
        ];
    }

    public function getStorageUsage(): array
    {
        $free = @disk_free_space(storage_path()) ?: 0;
        $total = @disk_total_space(storage_path()) ?: 1;

        return [
            'free' => HealthCheckCommand::bytes($free),
            'total' => HealthCheckCommand::bytes($total),
            'pct_free' => round($free / max(1, $total) * 100, 1),
        ];
    }

    public function getBackups(): array
    {
        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            return [];
        }
        $files = glob($dir.'/*.sql') ?: [];

        return collect($files)
            ->map(fn ($f) => ['name' => basename($f), 'size' => HealthCheckCommand::bytes(filesize($f)), 'date' => date('d M Y H:i', filemtime($f))])
            ->sortByDesc('name')
            ->take(10)
            ->values()
            ->all();
    }
}
