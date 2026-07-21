<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupMedia extends Command
{
    protected $signature = 'backup:media
                            {--disk=local : Disk to store backup}
                            {--compress : Gzip the archive}';
    protected $description = 'Backup all uploaded media (photos, docs) with manifest';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $compress = $this->option('compress');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = "backups/{$timestamp}";
        $mediaDir = "{$backupDir}/media";

        $this->info("Backing up media files...");

        // Collect all media files from public storage
        $mediaPaths = [];
        $totalSize = 0;

        // Scan storage/app/public recursively
        $allFiles = Storage::disk('local')->allFiles('public');
        foreach ($allFiles as $file) {
            $size = Storage::disk('local')->size($file);
            $mediaPaths[] = [
                'path' => $file,
                'size' => $size,
            ];
            $totalSize += $size;
        }

        $fileCount = count($mediaPaths);
        $this->line("  Found {$fileCount} media files ({$this->formatBytes($totalSize)})");

        if ($fileCount === 0) {
            $this->warn('No media files found to back up.');
            return Command::SUCCESS;
        }

        // Copy files to backup directory
        $bar = $this->output->createProgressBar($fileCount);
        $bar->start();

        foreach ($mediaPaths as $media) {
            $destPath = $mediaDir . '/' . $media['path'];
            $content = Storage::disk('local')->get($media['path']);
            Storage::disk($disk)->put($destPath, $content);
            $bar->advance();
        }

        $bar->finish();
        $this->newline(2);

        // Generate manifest
        $manifest = [
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'file_count' => $fileCount,
            'total_size_bytes' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize),
            'compressed' => $compress,
        ];

        $manifestPath = "{$backupDir}/manifest.json";
        Storage::disk($disk)->put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
        $this->info("Media backup complete. Manifest: {$manifestPath}");

        return Command::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}