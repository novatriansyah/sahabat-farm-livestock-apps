<?php

namespace App\Imports;

use App\Models\Animal;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\WeightLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AnimalsImport implements ToCollection, WithHeadingRow, WithValidation
{
    public $importedCount = 0;
    public $skippedCount = 0;

    public function collection(Collection $rows)
    {
        $defaultLocation = MasterLocation::first()->id ?? 1;
        $defaultCategory = MasterCategory::first()->id ?? 1;
        $defaultBreed = MasterBreed::first()->id ?? 1;
        $currentUser = auth()->user();

        foreach ($rows as $row) {
            if (empty($row['tag_id'])) continue;

            // DUPLICATE CHECK: Skip
            if (Animal::where('tag_id', $row['tag_id'])->exists()) {
                $this->skippedCount++;
                continue;
            }

            // Parsing format: [Kategori] Ras (e.g. [Kambing] Boer)
            $breedNameInput = $row['breed_name'] ?? '';
            $categoryId = $defaultCategory;
            $breedId = $defaultBreed;

            if (preg_match('/\[(.*?)\]\s*(.*)/', $breedNameInput, $matches)) {
                $categoryName = trim($matches[1]);
                $breedName = trim($matches[2]);

                // Find Category
                $category = MasterCategory::where('name', 'like', '%' . $categoryName . '%')->first();
                if ($category) {
                    $categoryId = $category->id;
                    // Find Breed within this category
                    $breed = MasterBreed::where('category_id', $categoryId)
                        ->where('name', 'like', '%' . $breedName . '%')
                        ->first();
                    if ($breed) $breedId = $breed->id;
                }
            } else {
                // Legacy format: just Breed Name
                $breed = MasterBreed::where('name', 'like', '%' . $breedNameInput . '%')->first();
                if ($breed) {
                    $breedId = $breed->id;
                    $categoryId = $breed->category_id;
                }
            }
            
            $locationId = $defaultLocation;
            if (!empty($row['location_name'])) {
                $loc = MasterLocation::where('name', 'like', '%' . $row['location_name'] . '%')->first();
                if ($loc) $locationId = $loc->id;
            }

            // Phys Status Lookup
            $physStatusId = MasterPhysStatus::first()->id ?? null;
            if (!empty($row['physical_status'])) {
                $statusInput = strtoupper($row['physical_status']);
                $genderInput = strtoupper($row['gender'] ?? '');
                
                // Gender-Aware Mapping for abbreviated names
                $statusNameSearch = $row['physical_status'];
                if ($statusInput === 'DARA') {
                    $statusNameSearch = 'Dara (Betina)';
                } elseif ($statusInput === 'BAKALAN') {
                    $statusNameSearch = 'Bakalan (Jantan)';
                } elseif ($statusInput === 'SIAP KAWIN') {
                    $statusNameSearch = ($genderInput === 'JANTAN' || $genderInput === 'MALE') ? 'Jantan siap kawin' : 'Betina siap kawin';
                }

                $status = MasterPhysStatus::where('name', 'like', '%' . $statusNameSearch . '%')->first();
                if ($status) $physStatusId = $status->id;
            }

            // Partner Assignment
            $partnerId = $currentUser->partner_id;
            if ($currentUser->role !== 'MITRA' && !empty($row['partner_name'])) {
                $partner = MasterPartner::where('name', 'like', '%' . $row['partner_name'] . '%')->first();
                if ($partner) $partnerId = $partner->id;
            }

            // Acquisition Type Logic
            $acqType = 'BELI'; // Default
            if (!empty($row['acquisition_type'])) {
                $val = strtoupper($row['acquisition_type']);
                if (in_array($val, ['BELI', 'HASIL_TERNAK', 'BOUGHT', 'BRED'])) {
                    $acqType = ($val === 'BRED' || $val === 'HASIL_TERNAK') ? 'HASIL_TERNAK' : 'BELI';
                }
            }
            
            $gender = 'BETINA'; // Default
            if (!empty($row['gender'])) {
                $gVal = strtoupper($row['gender']);
                 if (in_array($gVal, ['MALE', 'FEMALE', 'JANTAN', 'BETINA'])) {
                     $gender = ($gVal === 'MALE' || $gVal === 'JANTAN') ? 'JANTAN' : 'BETINA';
                 }
            }

            $animal = Animal::create([
                'tag_id' => $row['tag_id'],
                'gender' => $gender,
                'breed_id' => $breedId,
                'category_id' => $categoryId,
                'current_location_id' => $locationId,
                'current_phys_status_id' => $physStatusId,
                'owner_id' => $currentUser->id,
                'partner_id' => $partnerId,
                'birth_date' => $this->parseDate($row['birth_date'] ?? null),
                'acquisition_type' => $acqType,
                'purchase_price' => $row['purchase_price'] ?? 0,
                'generation' => $row['generation'] ?? null,
                'necklace_color' => $row['necklace_color'] ?? null,
                'is_active' => true,
            ]);

            // Create Initial Weight Log
            WeightLog::create([
                'animal_id' => $animal->id,
                'weigh_date' => Carbon::now(),
                'weight_kg' => $row['initial_weight_kg'] ?? 0,
            ]);

            $this->importedCount++;
        }
    }

    private function parseDate($value)
    {
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }

    public function rules(): array
    {
        return [
            'tag_id' => 'required',
            'initial_weight_kg' => 'required|numeric',
        ];
    }
}
