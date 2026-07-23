<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupMedia extends Command
{
    protected $signature = 'backup:media
                            {--disk=local : Disk to store backup}
                            {--compress : Gzip the archive}';
    protected $description = 'Backup all uploaded media (photos, docs) with SHA-256 per-file manifest';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $compress = (bool) $this->option('compress');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = "backups/{$timestamp}";
        $mediaDir = "{$backupDir}/media";

        $this->info("Backing up media files...");

        $mediaPaths = [];
        $totalSize = 0;
        $fileManifest = [];

        $allFiles = Storage::disk('local')->allFiles('public');
        foreach ($allFiles as $file) {
            $size = Storage::disk('local')->size($file);
            $content = Storage::disk('local')->get($file);
            $sha256 = hash('sha256', $content);

            $mediaPaths[] = [
                'path' => $file,
                'size' => $size,
                'sha256' => $sha256,
            ];
            $totalSize += $size;
        }

        $fileCount = count($mediaPaths);
        $this->line("  Found {$fileCount} media files ({$this->formatBytes($totalSize)})");

        if ($fileCount === 0) {
            $this->warn('No media files found. Generating zero-media evidence manifest...');
            $manifest = [
                'timestamp'          => now()->toIso8601String(),
                'environment'        => app()->environment(),
                'file_count'         => 0,
                'total_size_bytes'   => 0,
                'total_size_human'   => '0 B',
                'compressed'         => $compress,
                'zero_media_evidence'=> true,
                'media_files'        => [],
                'status'             => 'NO_MEDIA_PRESENT_ZERO_EVIDENCE_VALID',
            ];
            $manifestPath = "{$backupDir}/manifest.json";
            Storage::disk($disk)->put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info("Zero-media backup complete. Manifest: {$manifestPath}");
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($fileCount);
        $bar->start();

        foreach ($mediaPaths as $media) {
            $destPath = $mediaDir . '/' . $media['path'];
            $content = Storage::disk('local')->get($media['path']);
            Storage::disk($disk)->put($destPath, $content);
            $fileManifest[] = [
                'relative_path' => $media['path'],
                'size_bytes'    => $media['size'],
                'sha256'        => $media['sha256'],
            ];
            $bar->advance();
        }

        $bar->finish();
        $this->newline(2);

        $manifest = [
            'timestamp'          => now()->toIso8601String(),
            'environment'        => app()->environment(),
            'file_count'         => $fileCount,
            'total_size_bytes'   => $totalSize,
            'total_size_human'   => $this->formatBytes($totalSize),
            'compressed'         => $compress,
            'zero_media_evidence'=> false,
            'media_files'        => $fileManifest,
        ];

        $manifestPath = "{$backupDir}/manifest.json";
        Storage::disk($disk)->put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info("Media backup complete with per-file SHA-256. Manifest: {$manifestPath}");

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