<?php

namespace App\Observers;

use App\Models\Animal;
use App\Models\MasterBreed;

class AnimalObserver
{
    public function saving(Animal $animal): void
    {
        // 1. Auto-Assign Ear Tag Color based on Generation
        // Trigger if generation/breed changes OR if it's a new record (creating)
        if ($animal->isDirty('generation') || $animal->isDirty('breed_id') || !$animal->exists) {
            $animal->ear_tag_color = $this->determineEarTagColor($animal);
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

        // Logic from Requirements:
        // F1 Dorper: Kuning
        // F2 Dorper: Orange
        // F3 Dorper: Kuning Orange
        // F4 Dorper: Orange Persegi
        // F5 Dorper: Hijau Persegi
        // F6 Dorper: Kuning Orange
        // other jenis domba: Hijau

        // Check if Breed is Dorper
        if (str_contains($breedName, 'dorper')) {
            return match ($generation) {
                'F1' => 'Kuning',
                'F2' => 'Orange',
                'F3' => 'Kuning Orange',
                'F4' => 'Orange Persegi',
                'F5' => 'Hijau Persegi',
                'F6' => 'Kuning Orange', // Following prompt literally
                default => 'Kuning', // Fallback for Dorper
            };
        }

        // Other breeds
        return 'Hijau';
    }
}
