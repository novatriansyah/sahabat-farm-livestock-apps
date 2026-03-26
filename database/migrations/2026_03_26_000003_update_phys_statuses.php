<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Get old status IDs
        $oldStatuses = DB::table('master_phys_statuses')->pluck('id', 'name')->toArray();

        // 2. Define new statuses
        $newStatuses = [
            'Cempe',
            'Bakalan (Jantan)',
            'Dara (Betina)',
            'Jantan siap kawin',
            'Betina siap kawin',
            'Bunting',
            'Menyusui',
            'Penggemukan - Siap Jual',
            'Karantina'
        ];

        // 3. Clear and insert new statuses
        // We use a temporary table to hold mappings if we want to preserve IDs, 
        // but it's cleaner to just update the names and add new ones.
        
        // Let's update existing names and add missing ones to minimize FK issues if any.
        $existingCount = count($oldStatuses);
        foreach ($newStatuses as $index => $name) {
            $id = $index + 1;
            DB::table('master_phys_statuses')->updateOrInsert(
                ['id' => $id],
                ['name' => $name, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // Remove statuses beyond the new 9 if they exist
        DB::table('master_phys_statuses')->where('id', '>', count($newStatuses))->delete();

        // 4. Remap Animals
        // Old mappings (based on names to be safe):
        // 'Cempe Lahir' -> 'Cempe' (ID 1)
        // 'Cempe Sapih' -> 'Cempe' (ID 1)
        // 'Dara'        -> 'Dara (Betina)' (ID 3)
        // 'Pejantan'    -> 'Jantan siap kawin' (ID 4)
        // 'Bunting'     -> 'Bunting' (ID 6)
        // 'Menyusui'    -> 'Menyusui' (ID 7)
        // 'Penggemukan' -> 'Penggemukan - Siap Jual' (ID 8)
        // 'Karantina'   -> 'Karantina' (ID 9)

        $mappings = [
            'Cempe Lahir' => 'Cempe',
            'Cempe Sapih' => 'Cempe',
            'Dara' => 'Dara (Betina)',
            'Pejantan' => 'Jantan siap kawin',
            'Bunting' => 'Bunting',
            'Menyusui' => 'Menyusui',
            'Penggemukan - Siap Jual' => 'Penggemukan - Siap Jual',
            'Karantina' => 'Karantina',
            'Siap Kawin' => 'Jantan siap kawin', // Fallback
            'Sedang Kawin' => 'Jantan siap kawin', // Fallback
            'Sakit' => 'Karantina', // Fallback
            'Sehat' => 'Bakalan (Jantan)', // Fallback or Keep existing?
        ];

        foreach ($mappings as $oldName => $newName) {
            $newId = DB::table('master_phys_statuses')->where('name', $newName)->value('id');
            if ($newId && isset($oldStatuses[$oldName])) {
                DB::table('animals')
                    ->where('current_phys_status_id', $oldStatuses[$oldName])
                    ->update(['current_phys_status_id' => $newId]);
            }
        }
        
        // Ensure no animals have invalid IDs
        $maxId = count($newStatuses);
        DB::table('animals')->where('current_phys_status_id', '>', $maxId)->update(['current_phys_status_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting would require knowing the exact previous state, 
        // which varies by environment. For now, we'll leave it as is or 
        // implement a basic rollback to the seeder defaults.
    }
};
