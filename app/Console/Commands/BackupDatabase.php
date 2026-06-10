<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup database to storage/app/backups';

    public function handle(): void
    {
        $db = config('database.connections.mysql');

        $backupDir = storage_path('app/backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = sprintf('%s_%s.sql', $db['database'], now()->format('Y-m-d_His'));
        $path = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
            escapeshellarg($db['username']),
            escapeshellarg($db['password']),
            escapeshellarg($db['host']),
            escapeshellarg($db['port'] ?? '3306'),
            escapeshellarg($db['database']),
            escapeshellarg($path)
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $this->error("Backup failed: " . implode("\n", $output));
            return;
        }

        $size = round(filesize($path) / 1024, 2);
        $this->info("Backup saved: {$filename} ({$size} KB)");

        $keepDays = 7;
        $files = glob($backupDir . '/*.sql');
        $cutoff = now()->subDays($keepDays)->timestamp;

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                $this->line("Pruned old backup: " . basename($file));
            }
        }
    }
}
