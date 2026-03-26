<?php

namespace App\Console\Commands;

use App\Models\AnimalPhoto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanOldPhotosAndLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-old-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up animal photos and records older than 6 months';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup process...');

        $cutoffDate = Carbon::now()->subMonths(6);

        // 1. Cleanup Animal Photos
        $oldPhotos = AnimalPhoto::where('created_at', '<', $cutoffDate);
        $photoCount = 0;

        $oldPhotos->chunkById(200, function ($photos) use (&$photoCount) {
            $photoUrls = $photos->pluck('photo_url')->filter()->all();

            // Batch delete files from storage
            if (count($photoUrls) > 0) {
                Storage::disk('public')->delete($photoUrls);
            }

            // Batch delete records from database
            AnimalPhoto::whereIn('id', $photos->pluck('id'))->delete();

            $photoCount += $photos->count();
        });

        $this->info("Deleted {$photoCount} old animal photos.");

        // 2. Generic Soft Delete Cleanup (if any models use it in the future)
        // Since we currently don't use SoftDeletes trait in main models, 
        // this is a placeholder or can be used for specific log tables if needed.
        
        $this->info('Cleanup process completed.');
    }
}
