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

        // I need to find the IDs for these statuses.
        // Assuming MasterPhysStatus has these names.

        $cempe = MasterPhysStatus::where('name', 'Cempe Lahir')->first();
        $weaned = MasterPhysStatus::where('name', 'Cempe Sapih')->first();
        $ready = MasterPhysStatus::where('name', 'Dara')->first();

        if (!$cempe || !$weaned || !$ready) {
            $this->error('Master Physical Statuses not found. Please seed DB.');
            return;
        }

        $animals = Animal::where('is_active', true)->get();

        foreach ($animals as $animal) {
            // Safety Check: Do not auto-update Sick or Quarantine animals
            // Note: SICK, QUARANTINE, ISOLATION might be in health_status Enum or PhysStatus.
            // PhysStatus 'Karantina' exists.
            $currentStatusName = $animal->physStatus->name ?? '';
            if (in_array($currentStatusName, ['SICK', 'Karantina', 'ISOLATION'])) {
                continue;
            }

            $ageMonths = $animal->birth_date->diffInMonths(Carbon::now());

            $newStatusId = null;

            if ($ageMonths < 3) {
                $newStatusId = $cempe->id;
            } elseif ($ageMonths >= 3 && $ageMonths < 8) {
                $newStatusId = $weaned->id;
            } elseif ($ageMonths >= 8) {
                // Only update to Ready if not currently Pregnant or Lactating
                if (!in_array($currentStatusName, ['Bunting', 'Menyusui'])) {
                    $newStatusId = $ready->id;
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
