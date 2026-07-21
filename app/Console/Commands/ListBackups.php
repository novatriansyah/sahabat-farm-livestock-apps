<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ListBackups extends Command
{
    protected $signature = 'backup:list {--disk=local : Disk to list backups from}';
    protected $description = 'List all database backups with manifest info';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $dirs = Storage::disk($disk)->directories('backups');

        if (empty($dirs)) {
            $this->warn('No backups found.');
            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($dirs as $dir) {
            $manifestPath = $dir . '/manifest.json';
            if (!Storage::disk($disk)->exists($manifestPath)) {
                continue;
            }
            $manifest = json_decode(Storage::disk($disk)->get($manifestPath), true);
            $rows[] = [
                basename($dir),
                $manifest['timestamp'] ?? '?',
                $manifest['total_records'] ?? '?',
                $manifest['table_count'] ?? '?',
                substr($manifest['sha256'] ?? '?', 0, 16) . '...',
                $manifest['compressed'] ? 'GZIP' : 'SQL',
            ];
        }

        $this->table(['Backup', 'Timestamp', 'Records', 'Tables', 'SHA256', 'Format'], $rows);
        return Command::SUCCESS;
    }
}