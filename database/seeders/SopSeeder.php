<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterDisease;
use App\Models\MasterPhysStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SopSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lifecycle States (Status Fisik)
        $statuses = [
            ['name' => 'SUCKLING', 'rules' => 'Cempe 0-3 bulan'],
            ['name' => 'WEANED', 'rules' => 'Lepas Sapih 3-6 bulan'],
            ['name' => 'READY_TO_MATE', 'rules' => 'Siap Kawin >6 bulan'],
            ['name' => 'PREGNANT', 'rules' => 'Bunting'],
            ['name' => 'LACTATING', 'rules' => 'Menyusui'],
            ['name' => 'FATTENING', 'rules' => 'Penggemukan - Siap Jual'],
            ['name' => 'QUARANTINE', 'rules' => 'Baru Datang/Sakit'],
        ];

        foreach ($statuses as $status) {
            MasterPhysStatus::updateOrCreate(['name' => $status['name']], $status);
        }

        // 2. Master Breeds
        // Ensure Categories exist
        $catSheep = MasterCategory::firstOrCreate(['name' => 'Domba']);
        $catGoat = MasterCategory::firstOrCreate(['name' => 'Kambing']);

        $breeds = [
            // Local
            ['name' => 'Domba Garut', 'category_id' => $catSheep->id, 'origin' => 'Garut', 'min_weight_mate' => 30, 'min_age_mate_months' => 8],
            ['name' => 'Domba Priangan', 'category_id' => $catSheep->id, 'origin' => 'Jawa Barat', 'min_weight_mate' => 30, 'min_age_mate_months' => 8],
            ['name' => 'DEG (Ekor Gemuk)', 'category_id' => $catSheep->id, 'origin' => 'Jawa Timur', 'min_weight_mate' => 35, 'min_age_mate_months' => 8],
            ['name' => 'DET (Ekor Tipis)', 'category_id' => $catSheep->id, 'origin' => 'Jawa', 'min_weight_mate' => 25, 'min_age_mate_months' => 8],
            // Import
            ['name' => 'Dorper', 'category_id' => $catSheep->id, 'origin' => 'South Africa', 'min_weight_mate' => 45, 'min_age_mate_months' => 8],
            ['name' => 'Texel', 'category_id' => $catSheep->id, 'origin' => 'Netherlands', 'min_weight_mate' => 45, 'min_age_mate_months' => 8],
            ['name' => 'Merino', 'category_id' => $catSheep->id, 'origin' => 'Spain', 'min_weight_mate' => 40, 'min_age_mate_months' => 8],
            ['name' => 'Awassi', 'category_id' => $catSheep->id, 'origin' => 'Middle East', 'min_weight_mate' => 40, 'min_age_mate_months' => 8],
        ];

        foreach ($breeds as $breed) {
            MasterBreed::updateOrCreate(['name' => $breed['name']], $breed);
        }

        // 3. Inventory Items (Medicines, Vitamins, Feed)
        $items = [
            // Medicines (0.1 ml / 1kg usually means 1ml/10kg)
            ['name' => 'Ivermectin', 'unit' => 'ml', 'category' => 'MEDICINE', 'dosage_per_kg' => 0.1, 'current_stock' => 100],
            ['name' => 'Penicillin', 'unit' => 'ml', 'category' => 'MEDICINE', 'dosage_per_kg' => 0.05, 'current_stock' => 100], // Example
            ['name' => 'Oxytetracycline', 'unit' => 'ml', 'category' => 'MEDICINE', 'dosage_per_kg' => 0.1, 'current_stock' => 100],
            ['name' => 'Albendazole', 'unit' => 'ml', 'category' => 'MEDICINE', 'dosage_per_kg' => 0.15, 'current_stock' => 200],
            // Vitamins
            ['name' => 'Vitamin E', 'unit' => 'ml', 'category' => 'VITAMIN', 'dosage_per_kg' => 0.05, 'current_stock' => 100],
            ['name' => 'B-Complex', 'unit' => 'ml', 'category' => 'VITAMIN', 'dosage_per_kg' => 0.1, 'current_stock' => 100],
            // Vaccines
            ['name' => 'Vaksin PMK', 'unit' => 'dose', 'category' => 'VACCINE', 'dosage_per_kg' => null, 'current_stock' => 50],
            // Feed
            ['name' => 'Gula Merah', 'unit' => 'kg', 'category' => 'FEED', 'current_stock' => 10],
            ['name' => 'Asam Jawa', 'unit' => 'kg', 'category' => 'FEED', 'current_stock' => 5],
            ['name' => 'Konsentrat Sahabat Farm', 'unit' => 'sak', 'category' => 'FEED', 'current_stock' => 100],
        ];

        foreach ($items as $item) {
            InventoryItem::updateOrCreate(['name' => $item['name']], $item);
        }

        // 4. Master Diseases
        $diseases = [
            ['name' => 'Mastitis', 'symptoms' => 'Ambing bengkak, panas, susu menggumpal'],
            ['name' => 'Brucellosis', 'symptoms' => 'Keguguran pada kebuntingan tua'],
            ['name' => 'Pneumonia', 'symptoms' => 'Batuk, nafas cepat, leleran hidung'],
            ['name' => 'PMK (FMD)', 'symptoms' => 'Lepuh di mulut/kaki, air liur berlebih'],
            ['name' => 'Kuku Busuk (Foot Rot)', 'symptoms' => 'Pincang, celah kuku bau busuk'],
            ['name' => 'Cacing Hati (Fascioliasis)', 'symptoms' => 'Kurus, anemia, rahang bawah bengkak'],
            ['name' => 'Cacingan (Umum)', 'symptoms' => 'Kurus, bulu kusam, diare'],
        ];

        foreach ($diseases as $disease) {
            MasterDisease::create($disease);
        }
    }
}
