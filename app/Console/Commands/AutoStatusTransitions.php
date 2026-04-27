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

        $cempeId = \App\Models\FarmSetting::get('status_id_cempe');
        $bakalanId = \App\Models\FarmSetting::get('status_id_bakalan');
        $daraId = \App\Models\FarmSetting::get('status_id_dara');
        $menyusuiId = \App\Models\FarmSetting::get('status_id_menyusui');

        if (!$cempeId || !$bakalanId || !$daraId || !$menyusuiId) {
            $this->error('Required physical status IDs not found in settings.');
            return;
        }

        // 1. Disapih (Weaning)
        $weanDays = (int) \App\Models\FarmSetting::get('weaning_age_days', 60);
        $thresholdDate = Carbon::now()->subDays($weanDays);
        $weanableAnimals = Animal::where('current_phys_status_id', $cempeId)
            ->where('is_active', true)
            ->where('birth_date', '<=', $thresholdDate)
            ->get();

        $count = 0;
        DB::transaction(function () use ($weanableAnimals, $bakalanId, $daraId, $menyusuiId, &$count) {
            foreach ($weanableAnimals as $animal) {
                $targetId = ($animal->gender === 'JANTAN') ? $bakalanId : $daraId;

                // Update Cempe to "Bakalan" or "Dara"
                $animal->update(['current_phys_status_id' => $targetId]);
                $count++;

                // If mother is currently "Menyusui", revert to "Dara" (Ready to Mate)
                if ($animal->dam_id && $menyusuiId && $daraId) {
                    $dam = Animal::find($animal->dam_id);
                    // check if dam is lactating
                    if ($dam && $dam->physStatus->is_lactating) {
                        $dam->update(['current_phys_status_id' => $daraId]);
                        if ($dam->owner) {
                            $dam->owner->notify(new \App\Notifications\AnimalStatusNotification($dam, "Status berubah kembali ke Dara (Betina) setelah masa menyusui.", "success"));
                        }
                    }
                }
            }
        });

        $this->info("Successfully weaned {$count} animals.");

        // 2. Koloni Kawin
        $colDays = (int) \App\Models\FarmSetting::get('mating_colony_days', 60);
        $matingThreshold = Carbon::now()->subDays($colDays);
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
