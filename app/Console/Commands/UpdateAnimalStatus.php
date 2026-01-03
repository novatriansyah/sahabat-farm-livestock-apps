<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Animal;
use Carbon\Carbon;

class UpdateAnimalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'animals:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update animal statuses based on age (e.g. Weaning at 40 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting animal status updates...');

        // Logic 1: Weaning (Cempe Lahir -> Cempe Sapih) at 40 Days
        $nursingStatusId = 1; // Cempe Lahir
        $weanedStatusId = 2;  // Cempe Sapih

        // Find animals that are Nursing AND Age >= 40 days
        $animalsToWean = Animal::where('current_phys_status_id', $nursingStatusId)
            ->whereDate('birth_date', '<=', Carbon::now()->subDays(40))
            ->get();

        $count = 0;
        foreach ($animalsToWean as $animal) {
            $animal->update([
                'current_phys_status_id' => $weanedStatusId
            ]);
            $this->info("Animal {$animal->tag_id} weaned (Age: " . $animal->birth_date->diffInDays(Carbon::now()) . " days)");
            $count++;
        }

        $this->info("Completed. {$count} animals status updated to Weaned.");
        
        return Command::SUCCESS;
    }
}
