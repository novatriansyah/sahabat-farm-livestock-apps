<?php

namespace App\Console\Commands;

use App\Models\Animal;
use App\Services\GrowthCalculationService;
use App\Services\HppAllocationService;
use Illuminate\Console\Command;

class RebuildDerivedCommand extends Command
{
    protected $signature = 'sfi:rebuild-derived {--animal= : UUID of single animal} {--from= : Start date YYYY-MM-DD} {--to= : End date YYYY-MM-DD} {--all : Rebuild all animals and projections}';

    protected $description = 'Rebuild derived projections (ADG, HPP, ledgers) deterministically';

    public function handle(
        GrowthCalculationService $growthService,
        HppAllocationService $hppService
    ): int {
        $this->info('=== SFI DERIVED PROJECTION REBUILD ENGINE ===');

        $animalUuid = $this->option('animal');
        $all = $this->option('all');

        if ($animalUuid) {
            $animal = Animal::find($animalUuid);
            if (!$animal) {
                $this->error("Animal {$animalUuid} not found.");
                return 1;
            }

            $adgRes = $growthService->calculateForAnimal($animal);
            $hppRes = $hppService->rebuildAnimalHpp($animal);

            $this->info("Animal {$animal->tag_id}: ADG={$adgRes['display']}, HPP=" . number_format($hppRes, 2));
            return 0;
        }

        // Rebuild all
        $this->info('Rebuilding all ADG projections...');
        $animals = Animal::all();
        foreach ($animals as $animal) {
            $growthService->calculateForAnimal($animal);
        }

        $this->info('Rebuilding all HPP allocations & ledgers...');
        $summary = $hppService->rebuildAllHpp();

        $this->info("Rebuild Complete!");
        $this->info(" - Total Animals: {$summary['animal_count']}");
        $this->info(" - Active Ledgers: {$summary['ledger_count']}");
        $this->info(" - Result Checksum: {$summary['checksum']}");

        return 0;
    }
}
