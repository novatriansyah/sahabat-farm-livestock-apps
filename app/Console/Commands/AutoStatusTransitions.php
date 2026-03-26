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

        $cempeLabel = 'Cempe';
        $bakalanLabel = 'Bakalan (Jantan)';
        $daraLabel = 'Dara (Betina)';
        $menyusuiLabel = 'Menyusui';

        $cempeId = MasterPhysStatus::where('name', $cempeLabel)->value('id');
        $bakalanId = MasterPhysStatus::where('name', $bakalanLabel)->value('id');
        $daraId = MasterPhysStatus::where('name', $daraLabel)->value('id');
        $menyusuiId = MasterPhysStatus::where('name', $menyusuiLabel)->value('id');

        if (!$cempeId || !$bakalanId || !$daraId) {
            $this->error('Required physical statuses not found in DB.');
            return;
        }

        // 1. Disapih (Weaning) - Cempe > 60 days
        $thresholdDate = Carbon::now()->subDays(60);
        $weanableAnimals = Animal::where('current_phys_status_id', $cempeId)
            ->where('is_active', true)
            ->where('birth_date', '<=', $thresholdDate)
            ->get();

        $count = 0;
        foreach ($weanableAnimals as $animal) {
            $targetId = ($animal->gender === 'JANTAN') ? $bakalanId : $daraId;
            
            DB::transaction(function () use ($animal, $targetId, $menyusuiId, $daraId, &$count) {
                // Update Cempe to "Bakalan" or "Dara"
                $animal->update(['current_phys_status_id' => $targetId]);
                $count++;

                // If mother is currently "Menyusui", revert to "Dara" (Ready to Mate)
                if ($animal->dam_id && $menyusuiId && $daraId) {
                    $dam = Animal::find($animal->dam_id);
                    if ($dam && $dam->current_phys_status_id === $menyusuiId) {
                        $dam->update(['current_phys_status_id' => $daraId]);
                        if ($dam->owner) {
                            $dam->owner->notify(new \App\Notifications\AnimalStatusNotification($dam, "Status berubah kembali ke Dara (Betina) setelah masa menyusui.", "success"));
                        }
                    }
                }
            });
        }

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
