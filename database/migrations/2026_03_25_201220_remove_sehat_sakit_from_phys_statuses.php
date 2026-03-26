<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $redundantNames = ['Sehat', 'Sakit'];
        
        // Get IDs of statuses to be removed
        $toRemove = DB::table('master_phys_statuses')
            ->whereIn('name', $redundantNames)
            ->pluck('id')
            ->toArray();

        if (empty($toRemove)) {
            return;
        }

        // Find a fallback status (prefer "Dara" or just the first available one that isn't being removed)
        $fallbackStatus = DB::table('master_phys_statuses')
            ->whereNotIn('name', $redundantNames)
            ->where('name', 'Dara')
            ->first();

        if (!$fallbackStatus) {
            $fallbackStatus = DB::table('master_phys_statuses')
                ->whereNotIn('name', $redundantNames)
                ->first();
        }

        if ($fallbackStatus) {
            // Update animals currently using the redundant statuses
            DB::table('animals')
                ->whereIn('current_phys_status_id', $toRemove)
                ->update(['current_phys_status_id' => $fallbackStatus->id]);
        }

        // Delete the redundant statuses
        DB::table('master_phys_statuses')
            ->whereIn('id', $toRemove)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add them if needed, although they are redundant
        foreach (['Sehat', 'Sakit'] as $name) {
            DB::table('master_phys_statuses')->insertOrIgnore([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
