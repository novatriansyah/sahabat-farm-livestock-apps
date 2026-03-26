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

        $cempeId = \App\Models\MasterPhysStatus::where('name', 'Cempe')->value('id');
        $bakalanId = \App\Models\MasterPhysStatus::where('name', 'Bakalan (Jantan)')->value('id');
        $daraId = \App\Models\MasterPhysStatus::where('name', 'Dara (Betina)')->value('id');

        if (!$cempeId || !$bakalanId || !$daraId) {
            $this->error('Required physical statuses not found.');
            return Command::FAILURE;
        }

        // Find animals that are Cempe AND Age >= 40 days (using the 40 days from original code)
        $animalsToWean = Animal::where('current_phys_status_id', $cempeId)
            ->whereDate('birth_date', '<=', Carbon::now()->subDays(40))
            ->get();

        $count = 0;
        foreach ($animalsToWean as $animal) {
            $targetId = ($animal->gender === 'JANTAN') ? $bakalanId : $daraId;
            $animal->update([
                'current_phys_status_id' => $targetId
            ]);
            $this->info("Animal {$animal->tag_id} weaned to " . ($animal->gender === 'JANTAN' ? 'Bakalan' : 'Dara') . " (Age: " . $animal->birth_date->diffInDays(Carbon::now()) . " days)");
            $count++;
        }

        $this->info("Completed. {$count} animals status updated to Weaned.");
        
        return Command::SUCCESS;
    }
}
