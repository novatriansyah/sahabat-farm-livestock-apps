<?php

namespace App\Observers;

use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\BreedingEvent;

class AnimalObserver
{
    public function updated(Animal $animal): void
    {
        // Smart Breeding Separation Logic
        // If animal moves location, check if it was in a pending breeding event.
        if ($animal->isDirty('current_location_id')) {
            $this->checkBreedingSeparation($animal);
        }
    }

    public function saving(Animal $animal): void
    {
        // 1. Auto-Assign Ear Tag Color & Necklace Color based on Generation/Breed
        // Trigger if generation/breed changes OR if it's a new record (creating)
        if ($animal->isDirty('generation') || $animal->isDirty('breed_id') || !$animal->exists) {
            $color = $this->determineEarTagColor($animal);
            $animal->ear_tag_color = $color;
            // User requested to auto-fill necklace color as well
            if (!$animal->necklace_color) {
                $animal->necklace_color = $color;
            }
        }
    }

    private function checkBreedingSeparation(Animal $animal): void
    {
        // Find active breeding events where this animal is Sire OR Dam
        $breedingEvents = BreedingEvent::where('status', 'PENDING')
            ->where(function ($q) use ($animal) {
                $q->where('sire_id', $animal->id)
                  ->orWhere('dam_id', $animal->id);
            })
            ->with(['sire', 'dam'])
            ->get();

        foreach ($breedingEvents as $event) {
            // Check if Sire and Dam are now in different locations
            // Note: If one is missing (deleted?), we can't really check, but usually they exist.
            if ($event->sire && $event->dam) {
                if ($event->sire->current_location_id !== $event->dam->current_location_id) {
                    // They are separated. Mark event as COMPLETED (Mating Done).
                    $event->update(['status' => 'COMPLETED']); // Or 'SEPARATED' if that status exists
                }
            }
        }
    }

    private function determineEarTagColor(Animal $animal): ?string
    {
        // Fetch Breed Name safely
        $breedName = '';
        if ($animal->breed) {
            $breedName = strtolower($animal->breed->name);
        } elseif ($animal->breed_id) {
            $breed = MasterBreed::find($animal->breed_id);
            $breedName = $breed ? strtolower($breed->name) : '';
        }

        $generation = $animal->generation ? strtoupper($animal->generation) : '';

        // Logic from New Requirements:
        // 1. FB Dorper -> Original (no color / white)
        // 2. F1 Dorper -> Kuning
        // 3. F2 Dorper -> Orange
        // 4. F3 Dorper -> Orange Persegi
        // 5. F4 Dorper -> Hijau Persegi
        // 6. F5 Dorper -> Hijau Kuning
        // 7. F6 Dorper -> Kuning Orange
        // 8-14. Others (Garut, Cross Texel, Komposit, Cross Merino, Cross Dorper, Dombos, Lokal DET) -> Hijau

        // Check for Dorper specific logic
        // Note: "Cross Dorper" is listed as "Hijau" (No 12). So only "Dorper" (Full/Pure) + F-gens get colors?
        // Let's assume the user means Pure Dorper Breeding Program logic.

        if (str_contains($breedName, 'dorper') && !str_contains($breedName, 'cross')) {
            // FB = Full Blood? Assume FB or empty generation implies Pure if breed is just 'Dorper'
            if ($generation === 'FB' || $generation === 'PURE' || $generation === 'ORIGINAL') {
                return 'Original (Putih)';
            }

            return match ($generation) {
                'F1' => 'Kuning',
                'F2' => 'Orange',
                'F3' => 'Orange Persegi',
                'F4' => 'Hijau Persegi',
                'F5' => 'Hijau Kuning',
                'F6' => 'Kuning Orange',
                default => 'Kuning', // Fallback
            };
        }

        // Default for all other listed breeds (Garut, Cross Texel, etc.)
        return 'Hijau';
    }
}
