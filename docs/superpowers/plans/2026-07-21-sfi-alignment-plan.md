# SFI System Alignment — Full Implementation Plan

> **For agentic workers:**
**Goal:** Align the existing Laravel 12 SFI livestock management system with both Masterplan 1 (GPT — governance/verification) and Masterplan 2 (Claude — technical specs), closing 18 identified gaps across 6 phases.

**Architecture:** Follow existing patterns — Service/Observer/Action classes, UUID primary keys, Blade + Tailwind v4 + Alpine.js views, Hostinger shared-hosting constraints. All new parameters go into `farm_settings` table or dedicated master tables, not hardcoded. Each phase produces 3 output governance files per Masterplan 1 requirements.

**Tech Stack:** Laravel 12 · PHP 8.2/8.3 · MySQL · Blade + Tailwind v4 + Alpine.js · Vite 6 · Hostinger shared hosting · `maatwebsite/excel` · `barryvdh/laravel-dompdf` · `phpoffice/phppresentation`

## Global Constraints

- **NO hardcoded parameters** — all configurable via `farm_settings` or master tables
- **NO data reset before export verified** — owner must download data first
- **NO generation recalculation** until `sire_id` field is populated
- **Bahasa Indonesia** for UI, **English** for table/column/variable names
- **Soft deletes** only — never permanent data removal
- **Every migration must have working `down()`**
- **Hostinger shared constraints:** no `exec()`, no Puppeteer/wkhtmltopdf, use DomPDF, chunk exports, use database queue driver
- **AI-assisted dev:** code changes must be logged to `/docs/sfi-progress/TAHAP-{n}-LAPORAN.md`, `TAHAP-{n}-SOURCECODE.md`, `TAHAP-{n}-FEEDBACK.md`

---

# PHASE 1 — EXPORT & BACKUP (BLOCKER)

**Critical:** Must complete before any other phase. Without export, existing production data cannot be curated and reset = permanent data loss.

### Task 1.1: Multi-Sheet Animal Master Export

**Files:**
- Create: `app/Exports/AnimalMasterExport.php`
- Create: `app/Exports/Sheets/IndukanSheet.php`
- Create: `app/Exports/Sheets/AnakanSheet.php`
- Create: `app/Exports/Sheets/WeightHistorySheet.php`
- Create: `app/Exports/Sheets/TreatmentHistorySheet.php`
- Create: `app/Exports/Sheets/EarTagHistorySheet.php`
- Create: `app/Exports/Sheets/OwnershipHistorySheet.php`
- Create: `app/Exports/Sheets/HppHistorySheet.php`
- Create: `app/Exports/Sheets/MatingColonySheet.php`
- Create: `app/Exports/Sheets/BirthEventSheet.php`
- Create: `app/Exports/Sheets/SalesHistorySheet.php`
- Create: `app/Exports/Sheets/DataConflictSheet.php`
- Create: `app/Exports/Sheets/SummarySheet.php`
- Create: `app/Http/Controllers/ExportController.php`
- Modify: `routes/web.php`

**Interfaces:**
- Consumes: `Animal` model with all relationships (sire, dam, partner, breed, location, physStatus, category, weightLogs, treatmentLogs, earTagLogs, ownershipLogs, breedingEvents, exitLogs)
- Produces: `AnimalMasterExport` implementing `WithMultipleSheets`, filterable by `partner_id`, `status`, `fromDate`, `toDate`, `location_id`

- [ ] **Step 1: Create the multi-sheet export class**

```php
// app/Exports/AnimalMasterExport.php
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

- [ ] **Step 2: Create IndukanSheet**

```php
// app/Exports/Sheets/IndukanSheet.php
<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class IndukanSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'INDUKAN'; }

    public function headings(): array
    {
        return [
            'tag_id', 'legacy_tag_number', 'gender', 'breed_name',
            'generation', 'ear_tag_color', 'birth_date', 'entry_date',
            'acquisition_type', 'purchase_price', 'current_weight',
            'physical_status', 'is_active', 'necklace_color',
            'location_name', 'partner_name',
            'current_hpp', 'total_offspring_count',
            'last_lambing_date', 'lambing_interval_days',
            'gdrive_folder_url', 'photo_url', 'video_url',
            'notes', 'needs_review',
            'created_at', 'updated_at',
        ];
    }

    public function map($animal): array
    {
        return [
            $this->forceText($animal->tag_id),
            $animal->legacy_tag_id,
            $animal->gender,
            $animal->breed?->name,
            $animal->generation,
            $animal->ear_tag_color,
            $animal->birth_date?->format('Y-m-d') ?: '',
            $animal->entry_date?->format('Y-m-d') ?: '',
            $animal->acquisition_type,
            $animal->purchase_price,
            $animal->latestWeight()?->weight,
            $animal->physStatus?->name,
            $animal->is_active ? 'Ya' : 'Tidak',
            $animal->necklace_color,
            $animal->location?->name,
            $animal->partner?->name,
            $animal->current_hpp,
            $animal->offspring()->count(),
            $animal->latestBirthEvent()?->created_at?->format('Y-m-d'),
            $animal->lambing_interval_days,
            $animal->google_drive_link,
            $animal->photos()->first()?->url,
            $animal->videos()->first()?->url,
            $animal->notes,
            $animal->needs_review ? 'Ya' : '',
            $animal->created_at?->format('Y-m-d H:i:s'),
            $animal->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        return Animal::query()
            ->where('gender', 'BETINA')
            ->where('is_active', true)
            ->with(['breed', 'physStatus', 'location', 'partner', 'offspring', 'photos', 'videos'])
            ->when($this->filters['partner_id'] ?? null, fn($q, $id) => $q->where('partner_id', $id))
            ->when($this->filters['location_id'] ?? null, fn($q, $id) => $q->where('current_location_id', $id))
            ->orderBy('tag_id');
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT];
    }

    private function forceText($value): string
    {
        return "=\"{$value}\"";
    }
}
```

- [ ] **Step 3: Create AnakanSheet** — headings MUST include ALL columns per masterplan:

```php
// app/Exports/Sheets/AnakanSheet.php
// Headings:
// tag_id, legacy_tag_number, old_tag_id,
// dam_tag_id, sire_tag_id, sire_confidence,
// gender, breed_name, generation, generation_confidence, ear_tag_color,
// birth_date, birth_weight, is_birth_weight_estimated, litter_size,
// current_weight, adg, weaning_weight, weaning_date,
// physical_status, is_active, necklace_color,
// location_name, partner_name,
// current_hpp, purchase_price, sale_price,
// gdrive_folder_url, photo_url, video_url,  ← CRITICAL: was missing
// confidence_level, data_source, notes, needs_review,
// created_at, updated_at, created_by, last_modified_by
```

- [ ] **Step 4: Create WeightHistorySheet** — query `WeightLog` with animal tag_id, weight, weighed_at, filter by date range

- [ ] **Step 5: Create remaining sheets** (TreatmentHistory, EarTagHistory, OwnershipHistory, HppHistory, MatingColony, BirthEvent, SalesHistory, DataConflict, Summary) — all follow same FromQuery/WithTitle/WithHeadings/WithMapping pattern

- [ ] **Step 6: Create ExportController**

```php
// app/Http/Controllers/ExportController.php
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
            'animals'          => \App\Models\Animal::with(['breed', 'partner', 'location', 'physStatus'])->get()->toArray(),
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
        // Process in queued job for large files
        // Return diff view with accept/reject toggles
    }
}
```

- [ ] **Step 7: Add export routes**

```php
// routes/web.php — add to admin PEMILIK group:
Route::prefix('admin/export')->name('admin.export.')->group(function () {
    Route::get('/animals', [ExportController::class, 'animals'])->name('animals');
    Route::get('/animals/template', [ExportController::class, 'template'])->name('animals.template');
    Route::get('/full-backup', [ExportController::class, 'fullBackup'])->name('full-backup');
    Route::post('/reconcile', [ExportController::class, 'reconcile'])->name('reconcile');
});
```

- [ ] **Step 8: Verify export format compliance**
  - Dates: `Y-m-d` text format (not serial Excel dates)
  - Decimals: `.` not `,`
  - Eartag numbers: forced as text (`036` not `36`)
  - `gdrive_folder_url` column exists in ANAKAN sheet
  - Empty cells: truly empty (not `NULL` or `0`)

- [ ] **Step 9: Commit**

```bash
git add app/Exports/ app/Http/Controllers/ExportController.php routes/web.php
git commit -m "feat: add multi-sheet animal master export with 12 sheets and format compliance"
```

---

### Task 1.2: Multi-Format Report Export with Unified Filter Panel

**Files:**
- Create: `app/Http/Controllers/ReportExportController.php`
- Create: `app/Exports/Reports/PopulationReport.php`
- Create: `app/Exports/Reports/BirthReport.php`
- Create: `app/Exports/Reports/GrowthReport.php`
- Create: `app/Exports/Reports/KpiReport.php`
- Create: `app/Exports/Reports/HppReport.php`
- Create: `app/Exports/Reports/SalesReport.php`
- Create: `app/Exports/Reports/ProfitShareReport.php`
- Create: `app/Exports/Reports/InventoryReport.php`
- Create: `app/Exports/Reports/HealthReport.php`
- Create: `app/Exports/Reports/AuditReport.php`
- Create: `resources/views/admin/export/report-filters.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Install required packages**

```bash
composer require barryvdh/laravel-dompdf phpoffice/phppresentation
```

- [ ] **Step 2: Create ReportExportController with multi-format dispatch**

```php
// app/Http/Controllers/ReportExportController.php
public function export(string $reportType, string $format, Request $request)
{
    $filters = $request->only(['period', 'from', 'to', 'partner_id', 'location_id', 'status', 'columns']);
    $reportClass = match($reportType) {
        'population' => \App\Exports\Reports\PopulationReport::class,
        'birth'      => \App\Exports\Reports\BirthReport::class,
        'growth'     => \App\Exports\Reports\GrowthReport::class,
        'kpi'        => \App\Exports\Reports\KpiReport::class,
        'hpp'        => \App\Exports\Reports\HppReport::class,
        'sales'      => \App\Exports\Reports\SalesReport::class,
        'profit'     => \App\Exports\Reports\ProfitShareReport::class,
        'inventory'  => \App\Exports\Reports\InventoryReport::class,
        'health'     => \App\Exports\Reports\HealthReport::class,
        'audit'      => \App\Exports\Reports\AuditReport::class,
        default      => throw new \InvalidArgumentException("Unknown report: $reportType"),
    };
    return match($format) {
        'pdf'   => $this->exportPdf($reportClass, $filters),
        'excel' => Excel::download(new $reportClass($filters), "{$reportType}_" . now()->format('Y-m-d') . '.xlsx'),
        'csv'   => Excel::download(new $reportClass($filters), "{$reportType}_" . now()->format('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV),
        'ppt'   => $this->exportPpt($reportClass, $filters),
        'png'   => view('admin.export.png-preview', ['reportType' => $reportType, 'filters' => $filters]),
        default => throw new \InvalidArgumentException("Unknown format: $format"),
    };
}
```

- [ ] **Step 3: Create unified filter Blade component** — period, date range, partner, location, status, column checkboxes, export buttons (PDF/Excel/PPT/PNG/CSV)

- [ ] **Step 4: Commit**

```bash
git add app/Exports/Reports/ app/Http/Controllers/ReportExportController.php resources/views/admin/export/ routes/web.php composer.json composer.lock
git commit -m "feat: add 10 report types with multi-format export (PDF/Excel/PPT/PNG/CSV)"
```

---

### Task 1.3: Two-Way Reconciliation Engine

**Files:**
- Create: `app/Services/ReconciliationService.php`
- Create: `app/Jobs/ProcessReconciliation.php`
- Create: `resources/views/admin/export/reconcile-diff.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create ReconciliationService**

```php
// app/Services/ReconciliationService.php
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
        $existingAnimals = Animal::whereIn('tag_id', $uploadedRows->pluck('tag_id'))
            ->get()->keyBy('tag_id');

        foreach ($uploadedRows as $row) {
            $tagId = $row['tag_id'];
            $existing = $existingAnimals->get($tagId);
            if (!$existing) {
                $diff[] = ['tag_id' => $tagId, 'action' => 'CREATE', 'changes' => $row];
                continue;
            }
            $changes = [];
            $fields = ['birth_date','gender','generation','ear_tag_color','necklace_color',
                       'purchase_price','sale_price','partner_id','current_location_id','breed_id','google_drive_link'];
            foreach ($fields as $field) {
                $newVal = $row[$field] ?? null;
                $oldVal = $existing->$field;
                if ($field === 'birth_date' && $newVal) {
                    $newVal = \Carbon\Carbon::parse($newVal)->format('Y-m-d');
                    $oldVal = $existing->birth_date?->format('Y-m-d');
                }
                if ((string)$newVal !== (string)$oldVal) {
                    $changes[$field] = ['old' => $oldVal, 'new' => $newVal];
                }
            }
            if (!empty($changes)) {
                $diff[] = ['tag_id' => $tagId, 'action' => 'UPDATE', 'changes' => $changes];
            }
        }
        return $diff;
    }

    public function applySelected(Collection $selectedChanges): void
    {
        DB::transaction(function () use ($selectedChanges) {
            foreach ($selectedChanges as $change) {
                $animal = Animal::where('tag_id', $change['tag_id'])->first();
                if ($change['action'] === 'CREATE') {
                    Animal::create($change['changes']);
                } elseif ($change['action'] === 'UPDATE' && $animal) {
                    $animal->update($change['changes']);
                    // Log to audit
                    foreach ($change['changes'] as $field => $values) {
                        \App\Models\AuditLog::create([
                            'auditable_type' => Animal::class,
                            'auditable_id'   => $animal->id,
                            'event'          => 'updated',
                            'old_values'     => [$field => $values['old']],
                            'new_values'     => [$field => $values['new']],
                            'user_id'        => auth()->id(),
                        ]);
                    }
                }
            }
        });
    }
}
```

- [ ] **Step 2: Create reconciliation Blade view** — table with checkboxes per row, accept/reject toggles, "Apply Selected" button

- [ ] **Step 3: Commit**

```bash
git add app/Services/ReconciliationService.php app/Jobs/ProcessReconciliation.php resources/views/admin/export/reconcile-diff.blade.php routes/web.php
git commit -m "feat: add two-way reconciliation engine with diff view and audit logging"
```

---

# PHASE 2 — CORE LOGIC FIXES

### Task 2.1: Generation Rules — Table + Resolver Service

**Files:**
- Create: `database/migrations/2026_07_21_000001_create_master_generation_rules_table.php`
- Create: `app/Models/MasterGenerationRule.php`
- Create: `app/Services/GenerationResolverService.php`
- Modify: `app/Http/Controllers/BirthController.php` (replace generation logic)
- Modify: `app/Http/Controllers/MasterDataController.php` (add CRUD for generation rules)

**Interfaces:**
- Consumes: `Animal` model (sire, dam with breed/generation), `MasterGenerationRule`
- Produces: `GenerationResolverService::resolve(?Animal $sire, Animal $dam): array` returning `['generation', 'breed_name', 'eartag_color', 'needs_review', 'reason']`

- [ ] **Step 1: Create migration for master_generation_rules**

```php
// database/migrations/2026_07_21_000001_create_master_generation_rules_table.php
Schema::create('master_generation_rules', function (Blueprint $table) {
    $table->id();
    $table->enum('sire_type', ['FULLBLOOD','NON_FULLBLOOD']);
    $table->string('dam_generation', 30);
    $table->string('result_generation', 30);
    $table->string('result_breed_name', 60);
    $table->string('result_eartag_color', 40);
    $table->unsignedSmallInteger('priority')->default(100);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// Seed default rules:
// FULLBLOOD + LOKAL/GARUT/TEKSEL/MERINO → F1 DORPER (Kuning)
// FULLBLOOD + F1 → F2 DORPER (Orange)
// FULLBLOOD + F2 → F3 DORPER (Biru)
// FULLBLOOD + F3 → F4 DORPER (Biru)
// FULLBLOOD + F4 → F5 DORPER (Biru)
// FULLBLOOD + F5 → F6 DORPER (Biru)
// FULLBLOOD + F6/PURE → FULLBLOOD DORPER (Original)
// NON_FULLBLOOD + * → CROSS DORPER (Hijau)
```

- [ ] **Step 2: Create MasterGenerationRule model**

```php
// app/Models/MasterGenerationRule.php
class MasterGenerationRule extends Model
{
    protected $guarded = [];
    public function scopeActive($q) { return $q->where('is_active', true); }
}
```

- [ ] **Step 3: Create GenerationResolverService**

```php
// app/Services/GenerationResolverService.php
class GenerationResolverService
{
    public function resolve(?Animal $sire, Animal $dam): array
    {
        if (!$sire) {
            return ['generation'=>null, 'breed_name'=>$dam->breed?->name, 'eartag_color'=>null,
                    'needs_review'=>true, 'reason'=>'Pejantan belum diketahui'];
        }
        $sireType = $this->isFullblood($sire) ? 'FULLBLOOD' : 'NON_FULLBLOOD';
        $damGen = $this->normalizeGeneration($dam);
        $rule = MasterGenerationRule::active()
            ->where('sire_type', $sireType)
            ->where(fn($q) => $q->where('dam_generation',$damGen)->orWhere('dam_generation','*'))
            ->orderByRaw("CASE WHEN dam_generation='*' THEN 999 ELSE priority END")
            ->first();
        if (!$rule) {
            return ['generation'=>null, 'breed_name'=>null, 'eartag_color'=>null,
                    'needs_review'=>true, 'reason'=>"No rule for sire_type=$sireType, dam_gen=$damGen"];
        }
        return ['generation'=>$rule->result_generation, 'breed_name'=>$rule->result_breed_name,
                'eartag_color'=>$rule->result_eartag_color,
                'needs_review'=>!$this->isSireConfirmed($sire), 'reason'=>null];
    }

    private function isFullblood(Animal $sire): bool
    {
        return in_array(strtoupper($sire->generation ?? ''), ['PURE','FULLBLOOD','FULLBLOOD DORPER','PUREBREED']);
    }

    private function normalizeGeneration(Animal $animal): string
    {
        $gen = strtoupper($animal->generation ?? '');
        if (in_array($gen, ['PURE','FULLBLOOD','FULLBLOOD DORPER','PUREBREED'])) return 'PURE';
        if (preg_match('/^F(\d+)$/', $gen, $m)) return 'F'.$m[1];
        $breed = strtoupper($animal->breed?->name ?? '');
        if (str_contains($breed, 'LOKAL') || str_contains($breed, 'KOMPOSIT')) return 'LOKAL';
        if (str_contains($breed, 'GARUT')) return 'GARUT';
        if (str_contains($breed, 'TEKSEL') || str_contains($breed, 'TEXEL')) return 'TEKSEL';
        if (str_contains($breed, 'MERINO')) return 'MERINO';
        return 'LOKAL';
    }

    private function isSireConfirmed(Animal $sire): bool
    {
        return $sire->sire_confidence === 'CONFIRMED';
    }
}
```

- [ ] **Step 4: Modify BirthController::store()** — replace the old `max(sire_num, dam_num) + 1` block with:

```php
$generationResolver = app(\App\Services\GenerationResolverService::class);
$resolution = $generationResolver->resolve($sire, $dam);

$animalData = array_merge($animalData, [
    'generation'            => $resolution['generation'],
    'generation_confidence' => $resolution['needs_review'] ? 'UNVERIFIED' : 'AUTO',
    'ear_tag_color'         => $resolution['eartag_color'],
    'breed_id'              => $resolution['breed_name']
        ? MasterBreed::firstOrCreate(['name' => $resolution['breed_name']])->id
        : ($animalData['breed_id'] ?? null),
]);
```

- [ ] **Step 5: Add CRUD routes for generation rules** in MasterDataController

- [ ] **Step 6: Commit**

```bash
git add database/migrations/ app/Models/MasterGenerationRule.php app/Services/GenerationResolverService.php app/Http/Controllers/BirthController.php app/Http/Controllers/MasterDataController.php routes/web.php
git commit -m "feat: add configurable generation rules table and resolver service, fix BirthController generation logic"
```

---

### Task 2.2: Configurable Age Categories

**Files:**
- Create: `database/migrations/2026_07_21_000002_create_master_age_categories_table.php`
- Create: `app/Models/MasterAgeCategory.php`
- Create: `app/Services/AgeCategoryService.php`
- Modify: `app/Http/Controllers/MasterDataController.php` (add CRUD)
- Modify: `app/Services/DashboardService.php` (use new categories)
- Create: `resources/views/admin/masters/age-categories.blade.php`

- [ ] **Step 1: Create migration**

```php
Schema::create('master_age_categories', function (Blueprint $table) {
    $table->id();
    $table->string('code', 30)->unique();
    $table->string('name_male', 50);        // Cempe, Bakalan, Jantan
    $table->string('name_female', 50);      // Cempe, Dara, Betina Indukan
    $table->decimal('age_from_months', 5, 2);
    $table->decimal('age_to_months', 5, 2)->nullable();
    $table->boolean('is_breedable')->default(false);
    $table->boolean('is_sellable')->default(true);
    $table->string('badge_color', 20)->nullable();
    $table->unsignedSmallInteger('sort_order');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// Seed default per masterplan:
// 1-3 bln: Cempe/Cempe
// 3-5 bln: Cempe Sapih/Cempe Sapih
// 5-8 bln: Dara/Bakalan
// >8 bln: Betina Indukan/Jantan
```

- [ ] **Step 2: Create AgeCategoryService** — method `categorize(Animal $animal): ?MasterAgeCategory` that calculates age in months from `birth_date` and matches against `master_age_categories` by gender

- [ ] **Step 3: Create inline-edit Blade view** — table with editable rows, validation for no overlapping ranges

- [ ] **Step 4: Commit**

```bash
git add database/migrations/ app/Models/MasterAgeCategory.php app/Services/AgeCategoryService.php app/Http/Controllers/MasterDataController.php resources/views/admin/masters/age-categories.blade.php
git commit -m "feat: add configurable age categories with inline edit UI, seed default per masterplan"
```

---

### Task 2.3: Fix HPP — Partner Separation + Metabolic Weight Allocation

**Files:**
- Create: `database/migrations/2026_07_21_000003_create_hpp_monthly_snapshots_table.php`
- Create: `database/migrations/2026_07_21_000004_create_hpp_allocation_logs_table.php`
- Create: `app/Models/HppMonthlySnapshot.php`
- Create: `app/Models/HppAllocationLog.php`
- Create: `app/Services/HppAllocationService.php`
- Modify: `app/Actions/Finance/CalculateDailyHpp.php` (add partner_id filter + metabolic weight)
- Create: `app/Console/Commands/CloseMonthlyHpp.php`
- Modify: `routes/console.php`

- [ ] **Step 1: Create hpp_monthly_snapshots migration**

```php
Schema::create('hpp_monthly_snapshots', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('animal_id');
    $table->unsignedBigInteger('partner_id')->nullable();
    $table->year('period_year');
    $table->unsignedTinyInteger('period_month');
    $table->decimal('opening_hpp', 12, 2);
    $table->decimal('feed_cost', 12, 2)->default(0);
    $table->decimal('medicine_cost', 12, 2)->default(0);
    $table->decimal('other_cost', 12, 2)->default(0);
    $table->decimal('overhead_cost', 12, 2)->default(0);
    $table->decimal('closing_hpp', 12, 2);
    $table->unsignedSmallInteger('active_days')->default(30);
    $table->timestamp('closed_at')->nullable();
    $table->timestamps();
    $table->unique(['animal_id','period_year','period_month'], 'hpp_snap_uniq');
});
```

- [ ] **Step 2: Create hpp_allocation_logs migration**

```php
Schema::create('hpp_allocation_logs', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('animal_id');
    $table->unsignedBigInteger('partner_id')->nullable();
    $table->string('cost_type', 30);  // FEED, MEDICINE, OTHER, OVERHEAD
    $table->decimal('amount', 12, 2);
    $table->string('basis', 30);      // METABOLIC_WEIGHT, EQUAL, MANUAL
    $table->decimal('allocation_unit', 10, 4)->nullable();
    $table->decimal('total_units', 10, 4)->nullable();
    $table->date('allocated_at');
    $table->timestamps();
});
```

- [ ] **Step 3: Create HppAllocationService**

```php
// app/Services/HppAllocationService.php
class HppAllocationService
{
    public function allocateFeedCost(float $totalCost, Collection $animalsInLocation): void
    {
        $allocationMethod = FarmSetting::get('hpp_allocation_method', 'METABOLIC_WEIGHT');

        if ($allocationMethod === 'EQUAL') {
            $share = $totalCost / max($animalsInLocation->count(), 1);
            foreach ($animalsInLocation as $animal) {
                $this->recordAllocation($animal, 'FEED', $share, 'EQUAL', 1, $animalsInLocation->count());
            }
            return;
        }

        // METABOLIC_WEIGHT: BB^0.75
        $totalUnits = $animalsInLocation->sum(fn($a) => pow(max($a->latestWeight()?->weight ?? 1, 1), 0.75));
        foreach ($animalsInLocation as $animal) {
            $unit = pow(max($animal->latestWeight()?->weight ?? 1, 1), 0.75);
            $share = $totalCost * ($unit / max($totalUnits, 1));
            $this->recordAllocation($animal, 'FEED', $share, 'METABOLIC_WEIGHT', $unit, $totalUnits);
        }
    }

    private function recordAllocation(Animal $animal, string $costType, float $amount, string $basis, ?float $unit, ?float $totalUnits): void
    {
        HppAllocationLog::create([
            'animal_id'       => $animal->id,
            'partner_id'      => $animal->partner_id,  // ← KEY: partner separation
            'cost_type'       => $costType,
            'amount'          => round($amount, 2),
            'basis'           => $basis,
            'allocation_unit' => $unit,
            'total_units'     => $totalUnits,
            'allocated_at'    => now(),
        ]);
        $animal->increment('accumulated_feed_cost', round($amount, 2));
        $animal->increment('current_hpp', round($amount, 2));
    }
}
```

- [ ] **Step 4: Modify CalculateDailyHpp** — add `->where('partner_id', $animal->partner_id)` filter when summing costs per location. Replace flat headcount division with `HppAllocationService::allocateFeedCost()`

- [ ] **Step 5: Create CloseMonthlyHpp command**

```php
// app/Console/Commands/CloseMonthlyHpp.php
// Schedule: 1st of month at 01:00
// 1. Collect all unallocated consumable_usages from last month
// 2. Allocate via HppAllocationService
// 3. Write hpp_monthly_snapshots for each active animal
// 4. Lock period to prevent double-counting
```

- [ ] **Step 6: Add to console routes**

```php
// routes/console.php
Schedule::command('hpp:close-monthly')->monthlyOn(1, '01:00');
```

- [ ] **Step 7: Commit**

```bash
git add database/migrations/ app/Models/HppMonthlySnapshot.php app/Models/HppAllocationLog.php app/Services/HppAllocationService.php app/Actions/Finance/CalculateDailyHpp.php app/Console/Commands/CloseMonthlyHpp.php routes/console.php
git commit -m "fix: HPP partner separation with metabolic weight allocation and monthly snapshots"
```

---

### Task 2.4: Fix Gestation Period (60 → 150 days) + Auto-Infer Sire from Colony

**Files:**
- Create: `app/Services/SireInferenceService.php`
- Modify: `app/Console/Commands/ColonyScheduler.php` (fix gestation from 60 to 150)
- Modify: `app/Http/Controllers/MasterDataController.php` (add gestation_period_days to settings)
- Modify: `app/Http/Controllers/BirthController.php` (auto-infer sire on birth creation)

- [ ] **Step 1: Create SireInferenceService**

```php
// app/Services/SireInferenceService.php
class SireInferenceService
{
    public function inferSireFromColony(Animal $dam, Carbon $birthDate): ?Animal
    {
        $gestation = (int) FarmSetting::get('gestation_period_days', 150);
        $tol = (int) FarmSetting::get('gestation_tolerance_days', 15);

        $colonyMember = MatingColonyMember::where('animal_id', $dam->id)
            ->whereHas('colony', fn($q) => $q
                ->where('start_date', '<=', $birthDate->copy()->subDays($gestation - $tol))
                ->where(fn($x) => $x->whereNull('end_date')
                    ->orWhere('end_date', '>=', $birthDate->copy()->subDays($gestation + $tol))))
            ->with('colony.sire')
            ->latest()
            ->first();

        return $colonyMember?->colony?->sire;
    }
}
```

- [ ] **Step 2: Fix ColonyScheduler** — change `FarmSetting::get('mating_colony_days', 60)` to `FarmSetting::get('gestation_period_days', 150)`. Add `gestation_tolerance_days` setting (default 15).

- [ ] **Step 3: Modify BirthController::store()** — if `sire_id` is null in request, call `SireInferenceService::inferSireFromColony($dam, $birthDate)` and auto-fill

- [ ] **Step 4: Commit**

```bash
git add app/Services/SireInferenceService.php app/Console/Commands/ColonyScheduler.php app/Http/Controllers/BirthController.php app/Http/Controllers/MasterDataController.php
git commit -m "fix: gestation period 60→150 days, add sire auto-inference from mating colony"
```

---

### Task 2.5: Pending Tag Assignments for Unnumbered Animals

**Files:**
- Create: `database/migrations/2026_07_21_000005_create_pending_tag_assignments_table.php`
- Create: `app/Models/PendingTagAssignment.php`
- Create: `app/Services/PendingTagService.php`
- Create: `app/Http/Controllers/PendingTagController.php`
- Create: `resources/views/admin/animals/pending-tags.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create migration**

```php
Schema::create('pending_tag_assignments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('animal_id');
    $table->string('temp_tag', 40);
    $table->date('birth_date');
    $table->string('dam_tag', 40)->nullable();
    $table->string('prev_sibling_tag', 40)->nullable();
    $table->string('next_sibling_tag', 40)->nullable();
    $table->text('tracking_hint')->nullable();
    $table->enum('status', ['PENDING','ASSIGNED','SKIPPED'])->default('PENDING');
    $table->string('assigned_tag', 40)->nullable();
    $table->timestamp('assigned_at')->nullable();
    $table->timestamps();
});
```

- [ ] **Step 2: Create PendingTagService** — method `generateHint(Animal $animal): string` that builds tracking hint from birth date, dam tag, sibling tags

- [ ] **Step 3: Create PendingTagController** — index (list pending), assign (update tag_id + log to ear_tag_logs), skip

- [ ] **Step 4: Create Blade view** — dashboard badge "⚠️ X ternak belum bernomor", list with tracking hints, "Tetapkan Nomor" button

- [ ] **Step 5: Commit**

```bash
git add database/migrations/ app/Models/PendingTagAssignment.php app/Services/PendingTagService.php app/Http/Controllers/PendingTagController.php resources/views/admin/animals/pending-tags.blade.php routes/web.php
git commit -m "feat: add pending tag assignments for unnumbered animals with tracking hints"
```

---

# PHASE 3 — FRONTEND PARAMETERIZATION

### Task 3.1: Dynamic RBAC (Replace Hardcoded 4-Role Enum)

**Files:**
- Create: `database/migrations/2026_07_21_000006_create_roles_table.php`
- Create: `database/migrations/2026_07_21_000007_create_permissions_table.php`
- Create: `database/migrations/2026_07_21_000008_create_role_permission_table.php`
- Create: `app/Models/Role.php`
- Create: `app/Models/Permission.php`
- Create: `app/Services/RbacService.php`
- Create: `app/Http/Controllers/RoleController.php`
- Create: `resources/views/admin/roles/index.blade.php`
- Create: `resources/views/admin/roles/permission-matrix.blade.php`
- Modify: `app/Models/User.php` (add role relationship)
- Modify: `app/Http/Middleware/CheckRole.php` (use new RBAC)
- Modify: `routes/web.php`

- [ ] **Step 1: Create roles migration**

```php
Schema::create('roles', function (Blueprint $t) {
    $t->id(); $t->string('name',50)->unique(); $t->string('display_name',80);
    $t->text('description')->nullable(); $t->boolean('is_system')->default(false);
    $t->timestamps();
});
// Seed: PEMILIK, PETERNAK, STAF, MITRA
```

- [ ] **Step 2: Create permissions migration**

```php
Schema::create('permissions', function (Blueprint $t) {
    $t->id(); $t->string('module',60); $t->string('action',20);
    $t->enum('scope',['ALL','OWN','PARTNER'])->default('ALL');
    $t->string('display_name',100); $t->timestamps();
    $t->unique(['module','action','scope']);
});
// Seed: all module×action combinations from masterplan matrix
```

- [ ] **Step 3: Create role_permission pivot migration**

```php
Schema::create('role_permission', function (Blueprint $t) {
    $t->foreignId('role_id')->constrained()->cascadeOnDelete();
    $t->foreignId('permission_id')->constrained()->cascadeOnDelete();
    $t->primary(['role_id','permission_id']);
});
```

- [ ] **Step 4: Create RoleController** — CRUD for roles, permission matrix view with checkboxes

- [ ] **Step 5: Modify User model** — add `role()` BelongsTo relationship, keep `users.role` column as fallback

- [ ] **Step 6: Modify CheckRole middleware** — check against new permission table instead of hardcoded enum

- [ ] **Step 7: Commit**

```bash
git add database/migrations/ app/Models/Role.php app/Models/Permission.php app/Services/RbacService.php app/Http/Controllers/RoleController.php resources/views/admin/roles/ app/Models/User.php app/Http/Middleware/CheckRole.php routes/web.php
git commit -m "feat: dynamic RBAC with roles, permissions, and permission matrix UI"
```

---

### Task 3.2: Site Settings (Branding, Hero, Catalog, Contact)

**Files:**
- Create: `database/migrations/2026_07_21_000009_create_site_settings_table.php`
- Create: `app/Models/SiteSetting.php`
- Create: `app/Http/Controllers/SiteSettingController.php`
- Create: `resources/views/admin/settings/site.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create migration**

```php
Schema::create('site_settings', function (Blueprint $t) {
    $t->id(); $t->string('key',80)->unique(); $t->text('value')->nullable();
    $t->enum('type',['TEXT','TEXTAREA','IMAGE','COLOR','BOOLEAN','NUMBER','SELECT','JSON']);
    $t->string('group',40); $t->string('label',120); $t->text('help_text')->nullable();
    $t->json('options')->nullable(); $t->unsignedSmallInteger('sort_order')->default(0);
    $t->timestamps();
});
// Seed: logo, hero image, site title, theme color, catalog text, contact info
```

- [ ] **Step 2: Create SiteSettingController** — index (grouped by group), update (with image upload for IMAGE type)

- [ ] **Step 3: Create Blade view** — form with fields rendered by type, image preview with upload

- [ ] **Step 4: Commit**

```bash
git add database/migrations/ app/Models/SiteSetting.php app/Http/Controllers/SiteSettingController.php resources/views/admin/settings/site.blade.php routes/web.php
git commit -m "feat: site settings with image upload, branding, hero, catalog, contact config"
```

---

### Task 3.3: Consumable Types (Configurable Feed/Vitamin/Medicine Items)

**Files:**
- Create: `database/migrations/2026_07_21_000010_create_master_consumable_types_table.php`
- Create: `app/Models/MasterConsumableType.php`
- Modify: `app/Http/Controllers/MasterDataController.php` (add CRUD)
- Create: `resources/views/admin/masters/consumable-types.blade.php`

- [ ] **Step 1: Create migration**

```php
Schema::create('master_consumable_types', function (Blueprint $table) {
    $table->id();
    $table->string('code', 30)->unique();
    $table->string('name', 80);
    $table->string('unit', 20);
    $table->boolean('affects_hpp')->default(true);
    $table->enum('allocation_method', ['EQUAL','METABOLIC_WEIGHT','BY_LOCATION','MANUAL'])->default('EQUAL');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
// Seed: PAKAN (kg), VITAMIN (liter), OBAT (sachet), SUPLEMEN (kg)
```

- [ ] **Step 2: Create Blade view** — table with inline edit, add new type button

- [ ] **Step 3: Commit**

```bash
git add database/migrations/ app/Models/MasterConsumableType.php app/Http/Controllers/MasterDataController.php resources/views/admin/masters/consumable-types.blade.php
git commit -m "feat: configurable consumable types for feed/vitamin/medicine with allocation method"
```

---

### Task 3.4: Audit Trail (Install laravel-auditing)

**Files:**
- Modify: `composer.json`
- Modify: `app/Models/Animal.php` (add Auditable trait)
- Create: `app/Models/AuditLog.php` (if not auto-published)
- Create: `resources/views/admin/audit/index.blade.php`
- Create: `app/Http/Controllers/AuditController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Install package**

```bash
composer require owen-it/laravel-auditing
php artisan vendor:publish --provider="OwenIt\Auditing\AuditingServiceProvider"
php artisan migrate
```

- [ ] **Step 2: Add Auditable trait to Animal model**

```php
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Animal extends Model implements AuditableContract
{
    use Auditable;
    protected $auditInclude = ['tag_id','gender','breed_id','generation','birth_date',
        'birth_weight','partner_id','current_location_id','phys_status_id',
        'current_hpp','is_active','sire_id','dam_id','google_drive_link'];
}
```

- [ ] **Step 3: Create AuditController** — index with filters (date range, user, module, field)

- [ ] **Step 4: Create Blade view** — table: time · user · entity · field · old → new, with search

- [ ] **Step 5: Commit**

```bash
git add composer.json composer.lock app/Models/Animal.php app/Models/AuditLog.php app/Http/Controllers/AuditController.php resources/views/admin/audit/ routes/web.php
git commit -m "feat: add comprehensive audit trail with laravel-auditing for all animal field changes"
```

---

# PHASE 4 — NEW MODULES

### Task 4.1: Full Sales Module (Proforma Invoice → Payment → Exit)

**Files:**
- Create: `database/migrations/2026_07_21_000011_create_proforma_invoices_table.php`
- Create: `database/migrations/2026_07_21_000012_create_proforma_invoice_items_table.php`
- Create: `app/Models/ProformaInvoice.php`
- Create: `app/Models/ProformaInvoiceItem.php`
- Create: `app/Services/SalesWorkflowService.php`
- Create: `app/Http/Controllers/ProformaInvoiceController.php`
- Create: `resources/views/admin/sales/`
- Modify: `app/Models/Animal.php` (add `is_reserved` field)
- Modify: `routes/web.php`

- [ ] **Step 1: Create proforma_invoices migration**

```php
Schema::create('proforma_invoices', function (Blueprint $t) {
    $t->uuid('id')->primary();
    $t->string('proforma_number',40)->unique();
    $t->unsignedBigInteger('customer_id')->nullable();
    $t->string('customer_name',150);
    $t->string('customer_phone',30)->nullable();
    $t->text('customer_address')->nullable();
    $t->date('issue_date'); $t->date('valid_until')->nullable();
    $t->enum('price_source',['HPP','HPP_MARGIN','MANUAL'])->default('MANUAL');
    $t->decimal('margin_pct',5,2)->nullable();
    $t->decimal('subtotal',14,2)->default(0);
    $t->decimal('discount',14,2)->default(0);
    $t->decimal('tax',14,2)->default(0);
    $t->decimal('total',14,2)->default(0);
    $t->decimal('dp_amount',14,2)->default(0);
    $t->date('dp_date')->nullable();
    $t->decimal('paid_amount',14,2)->default(0);
    $t->enum('status',['DRAFT','SENT','DP_PAID','PAID','CANCELLED','EXPIRED'])->default('DRAFT');
    $t->uuid('invoice_id')->nullable();
    $t->text('notes')->nullable();
    $t->text('cancel_reason')->nullable();
    $t->timestamps();
});
```

- [ ] **Step 2: Create proforma_invoice_items migration**

```php
Schema::create('proforma_invoice_items', function (Blueprint $t) {
    $t->uuid('id')->primary();
    $t->uuid('proforma_invoice_id');
    $t->uuid('animal_id');
    $t->string('tag_id_snapshot',40);
    $t->decimal('weight_snapshot',6,2)->nullable();
    $t->decimal('hpp_snapshot',12,2)->nullable();
    $t->decimal('unit_price',12,2);
    $t->decimal('line_total',12,2);
    $t->timestamps();
    $t->unique(['proforma_invoice_id','animal_id']);
});
```

- [ ] **Step 3: Add `is_reserved` to animals table** — migration adding boolean default false

- [ ] **Step 4: Create SalesWorkflowService** — methods: `createProforma()`, `sendToCustomer()`, `recordDp()`, `recordPayment()`, `cancel()`, `expireOverdue()`

- [ ] **Step 5: Create ProformaInvoiceController** — index, create (select animals with filters), show, edit, payment, cancel

- [ ] **Step 6: Create Blade views** — animal selector with filters, proforma form, invoice preview, payment form

- [ ] **Step 7: Commit**

```bash
git add database/migrations/ app/Models/ProformaInvoice.php app/Models/ProformaInvoiceItem.php app/Services/SalesWorkflowService.php app/Http/Controllers/ProformaInvoiceController.php resources/views/admin/sales/ routes/web.php
git commit -m "feat: full sales module with proforma invoice, DP, payment, cancellation workflow"
```

---

### Task 4.2: Feed & Vitamin Module (Purchases + Usage + Allocation)

**Files:**
- Create: `database/migrations/2026_07_21_000013_create_consumable_purchases_table.php`
- Create: `database/migrations/2026_07_21_000014_create_consumable_usages_table.php`
- Create: `app/Models/ConsumablePurchase.php`
- Create: `app/Models/ConsumableUsage.php`
- Create: `app/Http/Controllers/ConsumableController.php`
- Create: `resources/views/admin/inventory/consumable/`
- Modify: `routes/web.php`

- [ ] **Step 1: Create consumable_purchases migration**

```php
Schema::create('consumable_purchases', function (Blueprint $t) {
    $t->uuid('id')->primary();
    $t->foreignId('consumable_type_id')->constrained('master_consumable_types');
    $t->string('item_name',120);
    $t->decimal('quantity',12,3);
    $t->string('unit',20);
    $t->decimal('unit_price',12,2);
    $t->decimal('total_price',14,2);
    $t->date('purchase_date');
    $t->unsignedBigInteger('supplier_id')->nullable();
    $t->decimal('remaining_qty',12,3);
    $t->timestamps();
});
```

- [ ] **Step 2: Create consumable_usages migration**

```php
Schema::create('consumable_usages', function (Blueprint $t) {
    $t->uuid('id')->primary();
    $t->foreignId('consumable_type_id')->constrained('master_consumable_types');
    $t->unsignedBigInteger('location_id')->nullable();
    $t->decimal('quantity',12,3);
    $t->decimal('unit_cost',12,2);
    $t->decimal('total_cost',14,2);
    $t->date('usage_date');
    $t->enum('allocation_method',['EQUAL','METABOLIC_WEIGHT','BY_LOCATION']);
    $t->boolean('is_allocated')->default(false);
    $t->timestamp('allocated_at')->nullable();
    $t->timestamps();
});
```

- [ ] **Step 3: Create ConsumableController** — purchase CRUD, usage CRUD, stock report

- [ ] **Step 4: Create Blade views** — purchase form, usage form, stock card, allocation preview

- [ ] **Step 5: Commit**

```bash
git add database/migrations/ app/Models/ConsumablePurchase.php app/Models/ConsumableUsage.php app/Http/Controllers/ConsumableController.php resources/views/admin/inventory/consumable/ routes/web.php
git commit -m "feat: feed and vitamin module with purchases, usage tracking, and allocation"
```

---

### Task 4.3: Reports with Full Filters (10 Report Types)

**Files:**
- Create: `app/Http/Controllers/ReportViewController.php`
- Create: `resources/views/admin/reports/`
- Modify: `routes/web.php`

- [ ] **Step 1: Create ReportViewController** — 10 report views, each with filter panel and export buttons

- [ ] **Step 2: Create Blade views** — population, birth, growth (ADG), KPI reproduction, HPP, sales, profit share, inventory, health, audit history

- [ ] **Step 3: Add routes**

```php
Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
    Route::get('/{type}', [ReportViewController::class, 'show'])->name('show');
    Route::get('/{type}/data', [ReportViewController::class, 'data'])->name('data');
});
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/ReportViewController.php resources/views/admin/reports/ routes/web.php
git commit -m "feat: 10 report types with unified filter panel and export buttons"
```

---

# PHASE 5 — RESET & CLEAN IMPORT

**Prerequisite:** Phase 1-4 complete, owner has downloaded and curated data.

### Task 5.1: Import Template with gdrive_folder_url Column

**Files:**
- Modify: `app/Imports/AnimalsImport.php` (add `google_drive_link` column)
- Create: `app/Imports/IndukanImport.php`
- Create: `app/Imports/AnakanImport.php`
- Create: `app/Imports/WeightHistoryImport.php`
- Create: `app/Imports/TreatmentHistoryImport.php`
- Create: `app/Imports/EarTagHistoryImport.php`
- Create: `app/Imports/OwnershipHistoryImport.php`
- Create: `app/Imports/HppHistoryImport.php`
- Create: `app/Imports/MatingColonyImport.php`
- Create: `app/Imports/BirthEventImport.php`
- Create: `app/Imports/SalesHistoryImport.php`
- Create: `app/Http/Controllers/ImportController.php`
- Create: `resources/views/admin/import/dry-run.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Modify AnimalsImport** — add `google_drive_link` to `$fillable` equivalent in import logic

- [ ] **Step 2: Create ImportController** — `dryRun()` (preview changes without saving), `execute()` (apply after approval)

- [ ] **Step 3: Create dry-run Blade view** — table showing: will create X, update Y, error Z, with accept/reject

- [ ] **Step 4: Commit**

```bash
git add app/Imports/ app/Http/Controllers/ImportController.php resources/views/admin/import/ routes/web.php
git commit -m "feat: import with gdrive_folder_url column and dry-run preview"
```

---

### Task 5.2: Reset Procedure + Validation Checklist

**Files:**
- Create: `app/Console/Commands/ResetTransactionalData.php`
- Create: `app/Services/PostImportValidationService.php`

- [ ] **Step 1: Create ResetTransactionalData command**

```php
// app/Console/Commands/ResetTransactionalData.php
// 1. Backup full DB
// 2. Truncate: animals, weight_logs, treatment_logs, breeding_events,
//    mating_colonies, mating_colony_members, animal_ear_tag_logs,
//    animal_ownership_logs, invoices, invoice_items, exit_logs,
//    consumable_purchases, consumable_usages, hpp_allocation_logs,
//    hpp_monthly_snapshots, pending_tag_assignments
// 3. DO NOT truncate: master_*, farm_settings, site_settings, users, roles, permissions
```

- [ ] **Step 2: Create PostImportValidationService**

```php
// app/Services/PostImportValidationService.php
class PostImportValidationService
{
    public function validate(): array
    {
        return [
            'total_active' => Animal::where('is_active', true)->count(),
            'orphans' => Animal::where('acquisition_type', 'HASIL_TERNAK')->whereNull('dam_id')->count(),
            'duplicate_tags' => Animal::select('tag_id')->groupBy('tag_id')->havingRaw('COUNT(*) > 1')->get(),
            'partner_distribution' => Animal::where('is_active', true)
                ->select('partner_id', DB::raw('COUNT(*) as count'))
                ->groupBy('partner_id')->get()->toArray(),
            'pending_tags' => PendingTagAssignment::where('status', 'PENDING')->count(),
            'total_purchase_price' => Animal::where('is_active', true)->sum('purchase_price'),
        ];
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Console/Commands/ResetTransactionalData.php app/Services/PostImportValidationService.php
git commit -m "feat: reset procedure and post-import validation checklist"
```

---

# PHASE 6 — ADVANCED FEATURES

### Task 6.1: Anti-Inbreeding Check

**Files:**
- Create: `app/Services/InbreedingService.php`
- Create: `database/migrations/2026_07_21_000015_add_sire_confidence_to_animals.php`

- [ ] **Step 1: Add `sire_confidence` column to animals**

```php
Schema::table('animals', function (Blueprint $table) {
    $table->string('sire_confidence', 30)->nullable()->after('sire_id');
    // Values: CONFIRMED, INFERRED, UNKNOWN
});
```

- [ ] **Step 2: Create InbreedingService**

```php
// app/Services/InbreedingService.php
class InbreedingService
{
    // Calculate Wright's coefficient with max 10-generation depth
    public function calculateCoefficient(Animal $animal1, Animal $animal2, int $maxGen = 10): float
    {
        $pedigree1 = $this->buildPedigree($animal1, $maxGen);
        $pedigree2 = $this->buildPedigree($animal2, $maxGen);
        // Find common ancestors and calculate coefficient
        // Block if coefficient > 6.25%
    }

    public function isMatingAllowed(Animal $sire, Animal $dam): array
    {
        $threshold = (float) FarmSetting::get('inbreeding_threshold', 6.25);
        $coeff = $this->calculateCoefficient($sire, $dam);
        return [
            'allowed' => $coeff <= $threshold,
            'coefficient' => $coeff,
            'threshold' => $threshold,
            'reason' => $coeff > $threshold ? "Koefisien inbreeding {$coeff}% melebihi ambang {$threshold}%" : null,
        ];
    }

    private function buildPedigree(Animal $animal, int $maxGen): array
    {
        if ($maxGen <= 0 || !$animal) return [];
        $pedigree = ['id' => $animal->id, 'tag' => $animal->tag_id];
        if ($animal->sire) $pedigree['sire'] = $this->buildPedigree($animal->sire, $maxGen - 1);
        if ($animal->dam) $pedigree['dam'] = $this->buildPedigree($animal->dam, $maxGen - 1);
        return $pedigree;
    }
}
```

- [ ] **Step 3: Integrate with BreedingController** — check before allowing mating

- [ ] **Step 4: Commit**

```bash
git add app/Services/InbreedingService.php database/migrations/ app/Http/Controllers/BreedingController.php
git commit -m "feat: anti-inbreeding check with Wright coefficient and 6.25% threshold"
```

---

### Task 6.2: KPI Reproduction Dashboard

**Files:**
- Create: `app/Services/KpiService.php`
- Modify: `app/Http/Controllers/DashboardController.php`
- Create: `resources/views/admin/dashboard/kpi-cards.blade.php`

- [ ] **Step 1: Create KpiService**

```php
// app/Services/KpiService.php
class KpiService
{
    public function calculate(): array
    {
        return [
            'lambing_rate' => $this->lambingRate(),
            'fertility_rate' => $this->fertilityRate(),
            'pre_weaning_mortality' => $this->preWeaningMortality(),
            'lambing_interval' => $this->lambingInterval(),
            'prolificacy' => $this->prolificacy(),
            'ewe_efficiency' => $this->eweEfficiency(),
        ];
    }

    private function lambingRate(): float
    {
        // Total lambs born / Total ewes exposed to rams × 100
        $totalLambs = BreedingEvent::whereIn('event_type', ['LAHIR', 'LAHIR_TUNGGAL', 'LAHIR_KEMBAR'])->count();
        $totalEwes = MatingColonyMember::whereHas('colony', fn($q) => $q->whereNotNull('sire_id'))->count();
        return $totalEwes > 0 ? round(($totalLambs / $totalEwes) * 100, 1) : 0;
    }

    private function fertilityRate(): float
    {
        $pregnant = BreedingEvent::where('event_type', 'BUNTING')->count();
        $exposed = MatingColonyMember::count();
        return $exposed > 0 ? round(($pregnant / $exposed) * 100, 1) : 0;
    }

    private function preWeaningMortality(): float
    {
        $died = ExitLog::where('exit_reason', 'MATI')
            ->whereHas('animal', fn($q) => $q->whereRaw('TIMESTAMPDIFF(MONTH, birth_date, ?) < 3', [now()]))
            ->count();
        $total = Animal::where('acquisition_type', 'HASIL_TERNAK')->count();
        return $total > 0 ? round(($died / $total) * 100, 1) : 0;
    }

    private function lambingInterval(): float
    {
        // Average days between consecutive births per dam
        $intervals = [];
        $dams = Animal::where('gender', 'BETINA')->whereHas('breedingEvents', '>=', 2)->get();
        foreach ($dams as $dam) {
            $events = $dam->breedingEvents()->where('event_type', 'LAHIR')->orderBy('event_date')->get();
            for ($i = 1; $i < $events->count(); $i++) {
                $intervals[] = $events[$i]->event_date->diffInDays($events[$i-1]->event_date);
            }
        }
        return count($intervals) > 0 ? round(array_sum($intervals) / count($intervals), 1) : 0;
    }

    private function prolificacy(): float
    {
        $totalLambs = BreedingEvent::whereIn('event_type', ['LAHIR_TUNGGAL', 'LAHIR_KEMBAR'])
            ->sum('offspring_count');
        $totalBirths = BreedingEvent::whereIn('event_type', ['LAHIR', 'LAHIR_TUNGGAL', 'LAHIR_KEMBAR'])->count();
        return $totalBirths > 0 ? round($totalLambs / $totalBirths, 2) : 0;
    }

    private function eweEfficiency(): float
    {
        $totalWeanedWeight = WeightLog::whereHas('animal', fn($q) => $q->where('gender', 'BETINA'))
            ->where('weight_type', 'SAPIH')->sum('weight');
        $totalEwes = Animal::where('gender', 'BETINA')->where('is_active', true)->count();
        return $totalEwes > 0 ? round($totalWeanedWeight / $totalEwes, 2) : 0;
    }
}
```

- [ ] **Step 2: Add KPI cards to dashboard** — 7 KPI cards with SFI value vs benchmark, color-coded (green=good, yellow=warning, red=bad)

- [ ] **Step 3: Commit**

```bash
git add app/Services/KpiService.php app/Http/Controllers/DashboardController.php resources/views/admin/dashboard/kpi-cards.blade.php
git commit -m "feat: KPI reproduction dashboard with 7 metrics and benchmark comparison"
```

---

### Task 6.3: Withdrawal Period Check

**Files:**
- Create: `app/Services/WithdrawalPeriodService.php`
- Modify: `app/Http/Controllers/ProformaInvoiceController.php` (block animals in withdrawal)

- [ ] **Step 1: Create WithdrawalPeriodService**

```php
// app/Services/WithdrawalPeriodService.php
class WithdrawalPeriodService
{
    public function isInWithdrawal(Animal $animal): bool
    {
        $latestTreatment = $animal->treatmentLogs()
            ->whereNotNull('withdrawal_days')
            ->latest('treatment_date')
            ->first();

        if (!$latestTreatment || !$latestTreatment->withdrawal_days) {
            return false;
        }

        $withdrawalEnd = $latestTreatment->treatment_date->addDays($latestTreatment->withdrawal_days);
        return now()->lt($withdrawalEnd);
    }

    public function getWithdrawalEndDate(Animal $animal): ?Carbon
    {
        $latestTreatment = $animal->treatmentLogs()
            ->whereNotNull('withdrawal_days')
            ->latest('treatment_date')
            ->first();

        return $latestTreatment ? $latestTreatment->treatment_date->addDays($latestTreatment->withdrawal_days) : null;
    }
}
```

- [ ] **Step 2: Integrate with sales module** — block animals in withdrawal period from being added to proforma invoice

- [ ] **Step 3: Commit**

```bash
git add app/Services/WithdrawalPeriodService.php app/Http/Controllers/ProformaInvoiceController.php
git commit -m "feat: withdrawal period check to block medicated animals from sale"
```

---

### Task 6.4: WhatsApp Notifications

**Files:**
- Create: `app/Notifications/WhatsAppNotification.php`
- Create: `app/Services/WhatsAppService.php`
- Create: `app/Console/Commands/SendWhatsAppReminders.php`
- Modify: `routes/console.php`

- [ ] **Step 1: Create WhatsAppService** — integrate with WhatsApp API (e.g., Fonnte, Wabox, or direct API)

```php
// app/Services/WhatsAppService.php
class WhatsAppService
{
    public function send(string $to, string $message): bool
    {
        $apiKey = FarmSetting::get('whatsapp_api_key');
        $apiUrl = FarmSetting::get('whatsapp_api_url', 'https://api.fonnte.com/send');
        // Send via configured provider
        // Log to whatsapp_logs table
    }
}
```

- [ ] **Step 2: Create SendWhatsAppReminders command** — daily reminders for: birth alerts, vaccination due, low stock, unnumbered animals, profit share reports

- [ ] **Step 3: Commit**

```bash
git add app/Notifications/WhatsAppNotification.php app/Services/WhatsAppService.php app/Console/Commands/SendWhatsAppReminders.php routes/console.php
git commit -m "feat: WhatsApp notifications for birth alerts, vaccination, low stock, reminders"
```

---

### Task 6.5: Bulk Operations (Batch Weighing)

**Files:**
- Create: `app/Http/Controllers/BulkOperationController.php`
- Create: `resources/views/admin/animals/bulk-weigh.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create BulkOperationController**

```php
// app/Http/Controllers/BulkOperationController.php
class BulkOperationController extends Controller
{
    public function weighForm()
    {
        $locations = MasterLocation::all();
        return view('admin.animals.bulk-weigh', compact('locations'));
    }

    public function getAnimalsForLocation(Request $request)
    {
        $animals = Animal::where('current_location_id', $request->location_id)
            ->where('is_active', true)
            ->get(['id', 'tag_id', 'gender', 'breed_id']);
        return response()->json($animals);
    }

    public function storeWeights(Request $request)
    {
        $request->validate(['weights' => 'required|array']);
        DB::transaction(function () use ($request) {
            foreach ($request->weights as $data) {
                $animal = Animal::findOrFail($data['animal_id']);
                $animal->weightLogs()->create([
                    'weight' => $data['weight'],
                    'weighed_at' => now(),
                    'recorded_by' => auth()->id(),
                ]);
            }
        });
        return redirect()->back()->with('success', 'Berat badan berhasil disimpan');
    }
}
```

- [ ] **Step 2: Create Blade view** — select location → list animals → input weights sequentially → save all

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/BulkOperationController.php resources/views/admin/animals/bulk-weigh.blade.php routes/web.php
git commit -m "feat: bulk weighing operation for batch weight input"
```

---

### Task 6.6: Digital Certificate per Animal

**Files:**
- Create: `app/Http/Controllers/CertificateController.php`
- Create: `resources/views/admin/animals/certificate-pdf.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create CertificateController**

```php
// app/Http/Controllers/CertificateController.php
class CertificateController extends Controller
{
    public function generate(Animal $animal)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.animals.certificate-pdf', [
            'animal' => $animal->load(['sire', 'dam', 'breed', 'partner', 'weightLogs', 'treatmentLogs']),
        ]);
        return $pdf->download("SERTIFIKAT_{$animal->tag_id}.pdf");
    }
}
```

- [ ] **Step 2: Create PDF Blade view** — pedigree tree, weight history, health history, photo, QR code with verification URL

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/CertificateController.php resources/views/admin/animals/certificate-pdf.blade.php routes/web.php
git commit -m "feat: digital certificate per animal with pedigree, history, and QR verification"
```

---

### Task 6.7: Automatic Backup Schedule

**Files:**
- Create: `app/Console/Commands/AutoBackup.php`
- Modify: `routes/console.php`

- [ ] **Step 1: Create AutoBackup command**

```php
// app/Console/Commands/AutoBackup.php
class AutoBackup extends Command
{
    protected $signature = 'backup:auto';
    protected $description = 'Daily automated backup of database and storage';

    public function handle()
    {
        $timestamp = now()->format('Y-m-d_His');
        $dbFile = storage_path("backups/db_{$timestamp}.sql");
        $storageFile = storage_path("backups/storage_{$timestamp}.tar.gz");

        // DB backup via mysqldump
        $dbName = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        exec("mysqldump --user={$user} --password={$pass} {$dbName} > {$dbFile}");

        // Storage backup
        exec("tar -czf {$storageFile} -C " . storage_path('app/public') . " .");

        // Cleanup old backups (retain 30 days)
        $retention = (int) FarmSetting::get('backup_retention_days', 30);
        $cutoff = now()->subDays($retention);
        foreach (glob(storage_path('backups/*')) as $file) {
            if (filemtime($file) < $cutoff->timestamp) {
                unlink($file);
            }
        }

        $this->info("Backup completed: {$dbFile}");
    }
}
```

- [ ] **Step 2: Add to schedule**

```php
// routes/console.php
Schedule::command('backup:auto')->dailyAt('02:00');
```

- [ ] **Step 3: Commit**

```bash
git add app/Console/Commands/AutoBackup.php routes/console.php
git commit -m "feat: automatic daily backup with 30-day retention"
```

---

# GOVERNANCE OUTPUTS

After each phase, produce 3 files in `/docs/sfi-progress/`:

### `TAHAP-{n}-LAPORAN.md`
- What was done, verification results, problems found, what's incomplete, deploy instructions, rollback commands

### `TAHAP-{n}-SOURCECODE.md`
- Complete source code of all new/modified files (full content, not summaries)
- Migration files in full
- SQL queries executed

### `TAHAP-{n}-FEEDBACK.md`
- Technical decisions made, unexpected findings, technical debt, recommendations for next phase, questions for owner, data impact assessment

---

# EXECUTION ORDER

```
Phase 1 (Export & Backup) → BLOCKER, must finish first
  ├── Task 1.1: Multi-sheet export
  ├── Task 1.2: Report export
  └── Task 1.3: Reconciliation

Phase 2 (Core Logic Fixes) → After Phase 1 verified
  ├── Task 2.1: Generation rules
  ├── Task 2.2: Age categories
  ├── Task 2.3: HPP fix
  ├── Task 2.4: Gestation + sire inference
  └── Task 2.5: Pending tags

Phase 3 (Frontend Parameterization) → After Phase 2
  ├── Task 3.1: Dynamic RBAC
  ├── Task 3.2: Site settings
  ├── Task 3.3: Consumable types
  └── Task 3.4: Audit trail

Phase 4 (New Modules) → After Phase 3
  ├── Task 4.1: Sales module
  ├── Task 4.2: Feed module
  └── Task 4.3: Reports

Phase 5 (Reset & Import) → After Phase 1-4
  ├── Task 5.1: Import with gdrive column
  └── Task 5.2: Reset procedure

Phase 6 (Advanced) → After Phase 5
  ├── Task 6.1: Anti-inbreeding
  ├── Task 6.2: KPI dashboard
  ├── Task 6.3: Withdrawal check
  ├── Task 6.4: WhatsApp notifications
  ├── Task 6.5: Bulk operations
  ├── Task 6.6: Digital certificates
  └── Task 6.7: Auto backup