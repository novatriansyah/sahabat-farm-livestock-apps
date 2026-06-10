<?php

namespace App\Observers;

use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\BreedingEvent;
use Illuminate\Support\Facades\Cache;

class AnimalObserver
{
    public function updated(Animal $animal): void
    {
        // Smart Breeding Separation Logic
        // If animal moves location, check if it was in a pending breeding event.
        if ($animal->isDirty('current_location_id')) {
            $this->checkBreedingSeparation($animal);
        }

        // Cache Invalidation
        $this->clearDashboardCache($animal);
    }
    
    public function created(Animal $animal): void
    {
        $this->clearDashboardCache($animal);
    }

    public function deleted(Animal $animal): void
    {
        $this->clearDashboardCache($animal);
    }

    public function saving(Animal $animal): void
    {
        // 1. Auto-Assign Ear Tag Color based on Generation
        // Trigger if generation/breed changes OR if it's a new record (creating)
        if ($animal->isDirty('generation') || $animal->isDirty('breed_id') || !$animal->exists) {
            $animal->ear_tag_color = $this->determineEarTagColor($animal);
        }
    }

    private function checkBreedingSeparation(Animal $animal): void
    {
        // Find active breeding events where this animal is Sire OR Dam
        $breedingEvents = BreedingEvent::where('status', 'MENUNGGU')
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
                    $event->update(['status' => 'SELESAI']); // Or 'SEPARATED' if that status exists
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

        // Check if Breed is Dorper
        if (str_contains($breedName, 'dorper')) {
            $key = 'eartag_map_dorper_' . strtolower($generation);
            $mappedColor = \App\Models\FarmSetting::get($key);
            if ($mappedColor) {
                return $mappedColor;
            }

            // Fallback to hardcoded mapping if settings are not seeded (e.g. in test environments)
            return match ($generation) {
                'F1' => 'Kuning',
                'F2' => 'Orange',
                'F3' => 'Kuning Orange',
                'F4' => 'Orange Persegi',
                'F5' => 'Hijau Persegi',
                'F6' => 'Kuning Orange',
                default => 'Kuning',
            };
        }

        // Other breeds
        return \App\Models\FarmSetting::get('eartag_map_default', 'Hijau');
    }

    private function clearDashboardCache(Animal $animal): void
    {
        // Clear Global Cache
        Cache::forget('dashboard_stats_global');

        // Clear Partner Cache if exists
        if ($animal->partner_id) {
            Cache::forget('dashboard_stats_' . $animal->partner_id);
        }
    }
}
