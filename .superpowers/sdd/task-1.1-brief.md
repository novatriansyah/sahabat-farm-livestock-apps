# Task 1.1: Multi-Sheet Animal Master Export

**Context:** This is the BLOCKER task — must complete before any other phase. Without export, owner cannot download production data, and system reset = permanent data loss.

---

## Requirements

Create a 12-sheet Excel export of all animal data, with format-safety features. The export must be filterable by partner_id, location_id, status, from/to dates.

### File: `app/Exports/AnimalMasterExport.php`

```php
<?php

namespace App\Exports;

use App\Exports\Sheets\IndukanSheet;
use App\Exports\Sheets\AnakanSheet;
use App\Exports\Sheets\WeightHistorySheet;
use App\Exports\Sheets\TreatmentHistorySheet;
use App\Exports\Sheets\EarTagHistorySheet;
use App\Exports\Sheets\OwnershipHistorySheet;
use App\Exports\Sheets\HppHistorySheet;
use App\Exports\Sheets\MatingColonySheet;
use App\Exports\Sheets\BirthEventSheet;
use App\Exports\Sheets\SalesHistorySheet;
use App\Exports\Sheets\DataConflictSheet;
use App\Exports\Sheets\SummarySheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnimalMasterExport implements WithMultipleSheets
{
    public function __construct(private array $filters = []) {}

    public function sheets(): array
    {
        return [
            'INDUKAN'          => new IndukanSheet($this->filters),
            'ANAKAN'           => new AnakanSheet($this->filters),
            'RIWAYAT BOBOT'    => new WeightHistorySheet($this->filters),
            'RIWAYAT KESEHATAN'=> new TreatmentHistorySheet($this->filters),
            'RIWAYAT EARTAG'   => new EarTagHistorySheet(),
            'RIWAYAT PEMILIK'  => new OwnershipHistorySheet(),
            'RIWAYAT HPP'      => new HppHistorySheet($this->filters),
            'KOLONI KAWIN'     => new MatingColonySheet($this->filters),
            'KELAHIRAN'        => new BirthEventSheet($this->filters),
            'PENJUALAN'        => new SalesHistorySheet($this->filters),
            'KONFLIK DATA'     => new DataConflictSheet(),
            'REKAP'            => new SummarySheet($this->filters),
        ];
    }
}
```

### 12 Sheet Files in `app/Exports/Sheets/`

Each sheet MUST implement: `FromQuery`, `WithTitle`, `WithHeadings`, `WithMapping`, `ShouldAutoSize`, `WithColumnFormatting`

**1. IndukanSheet** — title "INDUKAN"
- Query: `Animal::where('gender', 'BETINA')->where('is_active', true)->with(['breed','physStatus','location','partner','offspring','photos','videos'])`
- Headings: tag_id, legacy_tag_number, gender, breed_name, generation, ear_tag_color, birth_date, entry_date, acquisition_type, purchase_price, current_weight, physical_status, is_active, necklace_color, location_name, partner_name, current_hpp, total_offspring_count, last_lambing_date, lambing_interval_days, gdrive_folder_url, photo_url, video_url, notes, needs_review, created_at, updated_at
- Date format: `Y-m-d` as text
- tag_id: forced as text using `"=\"{$value}\""` prefix
- Filter: partner_id, location_id

**2. AnakanSheet** — title "ANAKAN"
- Query: `Animal::where('gender', 'JANTAN')->orWhere('gender', 'BETINA')->where('is_active', true)->whereNotNull('dam_id')->with(['sire','dam','breed','physStatus','location','partner','photos','videos'])`
- Headings: tag_id, legacy_tag_number, old_tag_id, dam_tag_id, sire_tag_id, sire_confidence, gender, breed_name, generation, generation_confidence, ear_tag_color, birth_date, birth_weight, is_birth_weight_estimated, litter_size, current_weight, adg, weaning_weight, weaning_date, physical_status, is_active, necklace_color, location_name, partner_name, current_hpp, purchase_price, sale_price, gdrive_folder_url, photo_url, video_url, confidence_level, data_source, notes, needs_review, created_at, updated_at, created_by, last_modified_by
- **CRITICAL:** gdrive_folder_url column must be present (was missing from old template)
- Filter: partner_id, location_id

**3. WeightHistorySheet** — title "RIWAYAT BOBOT"
- Query: `WeightLog::with('animal')` ordered by animal.tag_id, weighed_at
- Headings: tag_id, weight, weight_type, weighed_at, notes, created_by, created_at

**4. TreatmentHistorySheet** — title "RIWAYAT KESEHATAN"
- Query: `TreatmentLog::with('animal')` ordered by animal.tag_id, treatment_date
- Headings: tag_id, treatment_date, treatment_type, diagnosis, medicine, dosage, withdrawal_days, veterinarian, cost, notes, created_at

**5. EarTagHistorySheet** — title "RIWAYAT EARTAG"
- Query: `AnimalEarTagLog::with('animal')` ordered by animal.tag_id, changed_at
- Headings: tag_id, old_tag_id, new_tag_id, reason, changed_by, changed_at, notes

**6. OwnershipHistorySheet** — title "RIWAYAT PEMILIK"
- Query: `AnimalOwnershipLog::with(['animal','partner'])` ordered by animal.tag_id, start_date
- Headings: tag_id, partner_name, start_date, end_date, is_current, notes

**7. HppHistorySheet** — title "RIWAYAT HPP"
- Query: `HppMonthlySnapshot::with('animal')` ordered by animal.tag_id, period_year, period_month
- Headings: tag_id, period_year, period_month, opening_hpp, feed_cost, medicine_cost, other_cost, overhead_cost, closing_hpp, active_days

**8. MatingColonySheet** — title "KOLONI KAWIN"
- Query: `MatingColony::with(['sire','members.animal'])->orderBy('start_date')`
- Headings: colony_name, sire_tag_id, start_date, end_date, member_tags, notes

**9. BirthEventSheet** — title "KELAHIRAN"
- Query: `BreedingEvent::whereIn('event_type',['LAHIR','LAHIR_TUNGGAL','LAHIR_KEMBAR'])->with('animal')` ordered by event_date
- Headings: dam_tag_id, birth_date, event_type, offspring_count, breed_id, sire_tag_id, litter_size, notes

**10. SalesHistorySheet** — title "PENJUALAN"
- Query: `Invoice::with(['items.relatedAnimal','customer'])->where('type','COMMERCIAL')->orWhere('status','PAID')` ordered by issued_date
- Headings: invoice_number, customer_name, issued_date, total_amount, status, animal_tags, notes

**11. DataConflictSheet** — title "KONFLIK DATA"
- Query: animals where `needs_review` = true OR `sire_id` is null AND `acquisition_type` = 'HASIL_TERNAK'
- Headings: tag_id, issue_type, description, suggested_action, reported_at

**12. SummarySheet** — title "REKAP"
- Query: Aggregate counts from animals table
- Headings: metric, value, notes
- Rows: total_active, total_indukan, total_anakan, total_mitra, total_partners, total_purchase_price, total_current_hpp, total_unnumbered, data_quality_score

### File: `app/Http/Controllers/ExportController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Exports\AnimalMasterExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function animals(Request $request)
    {
        $filters = $request->only(['partner_id', 'location_id', 'status', 'from', 'to']);
        return Excel::download(
            new AnimalMasterExport($filters),
            'SFI_Export_Ternak_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function template()
    {
        return Excel::download(
            new AnimalMasterExport([]),
            'SFI_Template_Kosong.xlsx'
        );
    }

    public function fullBackup()
    {
        $data = [
            'animals'          => \App\Models\Animal::with(['breed','partner','location','physStatus'])->get()->toArray(),
            'weight_logs'      => \App\Models\WeightLog::all()->toArray(),
            'treatment_logs'   => \App\Models\TreatmentLog::all()->toArray(),
            'breeding_events'  => \App\Models\BreedingEvent::all()->toArray(),
            'mating_colonies'  => \App\Models\MatingColony::with('members')->get()->toArray(),
            'ear_tag_logs'     => \App\Models\AnimalEarTagLog::all()->toArray(),
            'ownership_logs'   => \App\Models\AnimalOwnershipLog::all()->toArray(),
            'invoices'         => \App\Models\Invoice::with('items')->get()->toArray(),
            'inventory_items'  => \App\Models\InventoryItem::all()->toArray(),
            'inventory_purchases' => \App\Models\InventoryPurchase::all()->toArray(),
            'inventory_usage'  => \App\Models\InventoryUsageLog::all()->toArray(),
            'hpp_manual_costs' => \App\Models\HppManualCost::all()->toArray(),
            'farm_settings'    => \App\Models\FarmSetting::all()->toArray(),
            'exported_at'      => now()->toIso8601String(),
        ];
        $filename = 'SFI_FullBackup_' . now()->format('Y-m-d_His') . '.json';
        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    public function reconcile(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx']);
        // Stub — will be implemented in Task 1.3
        return redirect()->back()->with('info', 'Reconciliation feature coming in Task 1.3');
    }
}
```

### Routes to add in `routes/web.php`

```php
Route::prefix('admin/export')->name('admin.export.')->group(function () {
    Route::get('/animals', [ExportController::class, 'animals'])->name('animals');
    Route::get('/animals/template', [ExportController::class, 'template'])->name('animals.template');
    Route::get('/full-backup', [ExportController::class, 'fullBackup'])->name('full-backup');
    Route::post('/reconcile', [ExportController::class, 'reconcile'])->name('reconcile');
});
```

### Format Compliance Checklist

1. Dates: `Y-m-d` text format (not serial Excel dates)
2. Decimals: `.` not `,`
3. Eartag numbers: forced as text using `"=\"{$value}\""` so `036` doesn't become `36`
4. `gdrive_folder_url` column exists in ANAKAN sheet
5. Empty cells: truly empty (not `NULL` or `0`)

### Existing Models to Reference

The following models exist and can be imported:
- `App\Models\Animal` — has relationships: breed(), partner(), location(), physStatus(), sire(), dam(), offspring(), weightLogs(), treatmentLogs(), earTagLogs(), ownershipLogs(), photos(), videos()
- `App\Models\WeightLog` — fields: animal_id, weight, weight_type, weighed_at, notes, created_by
- `App\Models\TreatmentLog` — fields: animal_id, treatment_date, treatment_type, diagnosis, medicine, dosage, withdrawal_days, cost, notes
- `App\Models\AnimalEarTagLog` — fields: animal_id, old_tag_id, new_tag_id, reason, changed_by, changed_at
- `App\Models\AnimalOwnershipLog` — fields: animal_id, partner_id, start_date, end_date, is_current
- `App\Models\HppMonthlySnapshot` — may not exist yet (will be built in Phase 2). For now, create a basic query or skip if table doesn't exist.
- `App\Models\MatingColony` — with relationship: sire(), members()
- `App\Models\MatingColonyMember` — fields: colony_id, animal_id
- `App\Models\BreedingEvent` — fields: animal_id, event_type, event_date, offspring_count, notes
- `App\Models\Invoice` — fields: invoice_number, customer_name, issued_date, total_amount, status, type with items() relationship
- `App\Models\InvoiceItem` — fields: invoice_id, related_animal_id, description, unit_price, quantity

### Report File

Write your report to `.superpowers/sdd/task-1.1-report.md`. Include:
- Commits made (with hashes)
- List of files created/modified
- Test results summary
- Any concerns