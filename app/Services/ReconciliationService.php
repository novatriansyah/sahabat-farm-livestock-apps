<?php
namespace App\Services;
use App\Models\Animal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
class ReconciliationService
{
    public function compare(Collection $uploadedRows): array
    {
        $diff = [];
        $tagIds = $uploadedRows->pluck('tag_id')->filter();
        $existingAnimals = Animal::whereIn('tag_id', $tagIds)->get()->keyBy('tag_id');
        foreach ($uploadedRows as $row) {
            $tagId = $row['tag_id'] ?? null;
            if (!$tagId) continue;
            $existing = $existingAnimals->get($tagId);
            if (!$existing) { $diff[] = ['tag_id' => $tagId, 'action' => 'CREATE', 'changes' => $row]; continue; }
            $changes = [];
            $fields = ['birth_date','gender','generation','ear_tag_color','necklace_color','purchase_price','sale_price','partner_id','current_location_id','breed_id','google_drive_link'];
            foreach ($fields as $field) {
                $newVal = $row[$field] ?? null; $oldVal = $existing->$field;
                if ($field === 'birth_date' && $newVal) { $newVal = \Carbon\Carbon::parse($newVal)->format('Y-m-d'); $oldVal = $existing->birth_date?->format('Y-m-d'); }
                if ((string)$newVal !== (string)$oldVal) $changes[$field] = ['old' => $oldVal, 'new' => $newVal];
            }
            if (!empty($changes)) $diff[] = ['tag_id' => $tagId, 'action' => 'UPDATE', 'changes' => $changes];
        }
        return $diff;
    }
    public function applySelected(Collection $selectedChanges): void
    {
        DB::transaction(function () use ($selectedChanges) {
            foreach ($selectedChanges as $change) {
                $animal = Animal::where('tag_id', $change['tag_id'])->first();
                if (!$animal) continue;
                foreach ($change['changes'] as $field => $value) {
                    if (is_array($value)) $value = $value['new'];
                    $animal->$field = $value;
                }
                $animal->save();
            }
        });
    }
}