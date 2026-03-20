<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Animal;
use App\Models\MasterPhysStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoStatusTransitions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'animal:auto-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automate animal status transitions such as Weaning (Disapih)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto status transitions...');

        $cempeLahirLabel = 'Cempe Lahir';
        $cempeSapihLabel = 'Cempe Sapih';
        $menyusuiLabel = 'Menyusui';
        $daraLabel = 'Dara';

        $cempeLahirId = MasterPhysStatus::where('name', $cempeLahirLabel)->value('id');
        $cempeSapihId = MasterPhysStatus::where('name', $cempeSapihLabel)->value('id');
        $menyusuiId = MasterPhysStatus::where('name', $menyusuiLabel)->value('id');
        $daraId = MasterPhysStatus::where('name', $daraLabel)->value('id');

        if (!$cempeLahirId || !$cempeSapihId) {
            $this->error('Required physical statuses not found in DB. Check translation sync.');
            return;
        }

        // 1. Disapih (Weaning) - Cempe > 60 days
        $thresholdDate = Carbon::now()->subDays(60);
        $weanableAnimals = Animal::where('current_phys_status_id', $cempeLahirId)
            ->where('is_active', true)
            ->where('birth_date', '<=', $thresholdDate)
            ->get();

        $count = 0;
        DB::transaction(function () use ($weanableAnimals, $cempeSapihId, $menyusuiId, $daraId, &$count) {
            foreach ($weanableAnimals as $animal) {
                // Update Cempe to "Disapih"
                $animal->update(['current_phys_status_id' => $cempeSapihId]);
                $count++;

                // If mother is currently "Menyusui", revert to "Dara" (Ready to Mate)
                if ($animal->dam_id && $menyusuiId && $daraId) {
                    $dam = Animal::find($animal->dam_id);
                    // Check if Dam has other suckling kids before switching? 
                    // To be safe, we just switch her if she is marked Menyusui
                    if ($dam && $dam->current_phys_status_id === $menyusuiId) {
                        $dam->update(['current_phys_status_id' => $daraId]);
                        if ($dam->owner) {
                            $dam->owner->notify(new \App\Notifications\AnimalStatusNotification($dam, "Status berubah kembali ke Dara (Siap Kawin) setelah masa menyusui.", "success"));
                        }
                    }
                }
            }
        });

        $this->info("Successfully weaned {$count} animals.");

        // 2. Koloni Kawin > 60 days -> Selesai Kawin (READY)
        $matingThreshold = Carbon::now()->subDays(60);
        $matingMembers = \App\Models\MatingColonyMember::where('status', 'KAWIN')
            ->where('joined_date', '<=', $matingThreshold)
            ->get();
            
        $colonyCount = 0;
        DB::transaction(function () use ($matingMembers, &$colonyCount) {
            foreach ($matingMembers as $member) {
                $member->update([
                    'status' => 'SIAP',
                    'left_date' => Carbon::now()
                ]);

                if ($member->animal && $member->animal->owner) {
                    $member->animal->owner->notify(new \App\Notifications\AnimalStatusNotification($member->animal, "Selesai masa koloni kawin, status kembali SIAP.", "info"));
                }
                
                $colonyCount++;
            }
        });
        
        $this->info("Successfully processed {$colonyCount} mating colony members.");
    }
}
