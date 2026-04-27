<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Animal;
use App\Models\MasterPhysStatus;
use Carbon\Carbon;

class UpdateAnimalCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'animals:update-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update animal categories/status based on age (Cempe -> Lepas Sapih -> Dara)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting animal category update...');

        // Logic based on requirements:
        // Cempe (Suckling) -> Lepas Sapih (Weaned) -> Dara (Ready to Mate?)
        // Let's assume some standard days if not provided in prompt explicitly,
        // Prompt says "Auto-Categorization (Cempe -> Lepas Sapih -> Dara) ... based on Age Logic (Excel formulas)."
        // Since I don't have the Excel, I will use standard industry defaults for Sheep/Goats:
        // 0-3 Months: Cempe (Suckling)
        // 3-8 Months: Lepas Sapih (Weaned)
        // > 8 Months: Siap Kawin (Ready to Mate) / Dara

        $cempeId = \App\Models\FarmSetting::get('status_id_cempe');
        $bakalanId = \App\Models\FarmSetting::get('status_id_bakalan');
        $daraId = \App\Models\FarmSetting::get('status_id_dara');
        $jantanSiapId = \App\Models\FarmSetting::get('status_id_jantan_siap');
        $betinaSiapId = \App\Models\FarmSetting::get('status_id_betina_siap');

        if (!$cempeId || !$bakalanId || !$daraId || !$jantanSiapId || !$betinaSiapId) {
            $this->error('Required Master Physical Status IDs not found in settings.');
            return;
        }

        $animals = Animal::where('is_active', true)->get();
        $readyAge = (int) \App\Models\FarmSetting::get('min_age_mate_months_fallback', 8);

        foreach ($animals as $animal) {
            // Safety Check: Do not auto-update Sick or Quarantine animals
            if ($animal->physStatus->is_quarantine) {
                continue;
            }

            $ageMonths = $animal->birth_date->diffInMonths(Carbon::now());
            $newStatusId = null;

            if ($ageMonths < 3) {
                $newStatusId = $cempeId;
            } elseif ($ageMonths >= 3 && $ageMonths < $readyAge) {
                $newStatusId = ($animal->gender === 'JANTAN') ? $bakalanId : $daraId;
            } elseif ($ageMonths >= $readyAge) {
                // Only update to Ready if not currently Pregnant or Lactating
                if (!$animal->physStatus->is_pregnant && !$animal->physStatus->is_lactating) {
                    $newStatusId = ($animal->gender === 'JANTAN') ? $jantanSiapId : $betinaSiapId;
                }
            }

            // Only update if the status is actually changing and we have a target status
            if ($newStatusId && $newStatusId !== $animal->current_phys_status_id) {
                $animal->update(['current_phys_status_id' => $newStatusId]);
                $this->info("Updated Animal {$animal->tag_id} to status ID {$newStatusId}");
            }
        }

        $this->info('Animal category update complete.');
    }
}
