<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use App\Models\WeightLog;
use App\Models\TreatmentLog;
use App\Models\BreedingEvent;
use App\Models\MatingColony;
use App\Models\Invoice;
use App\Models\ExitLog;
use App\Models\AnimalEarTagLog;
use App\Models\AnimalOwnershipLog;
use App\Models\HppManualCost;
use App\Models\MasterPartner;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ManifestSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(
        private string $schemaVersion = '1.0.0',
        private string $commitHash = '',
        private string $environment = 'local',
    ) {}

    public function title(): string { return 'MANIFEST'; }

    public function headings(): array
    {
        return ['key', 'value'];
    }

    public function array(): array
    {
        $now = now();
        return [
            ['schema_version', $this->schemaVersion],
            ['exported_at', $now->toIso8601String()],
            ['exported_at_local', $now->format('Y-m-d H:i:s')],
            ['environment', $this->environment],
            ['commit_hash', $this->commitHash],
            ['app_version', config('app.version', 'unknown')],
            ['php_version', PHP_VERSION],
            ['laravel_version', app()->version()],
            ['', ''],
            ['RECORD COUNTS', ''],
            ['animals_total', Animal::count()],
            ['animals_active', Animal::where('is_active', true)->count()],
            ['animals_inactive', Animal::where('is_active', false)->count()],
            ['animals_male', Animal::where('gender', 'JANTAN')->count()],
            ['animals_female', Animal::where('gender', 'BETINA')->count()],
            ['animals_with_sire', Animal::whereNotNull('sire_id')->count()],
            ['animals_with_dam', Animal::whereNotNull('dam_id')->count()],
            ['weight_logs', WeightLog::count()],
            ['treatment_logs', TreatmentLog::count()],
            ['breeding_events', BreedingEvent::count()],
            ['mating_colonies', MatingColony::count()],
            ['invoices', Invoice::count()],
            ['exit_logs', ExitLog::count()],
            ['ear_tag_logs', AnimalEarTagLog::count()],
            ['ownership_logs', AnimalOwnershipLog::count()],
            ['hpp_manual_costs', HppManualCost::count()],
            ['partners', MasterPartner::count()],
            ['', ''],
            ['CHECKSUM', ''],
            ['sha256', hash('sha256', $now->toIso8601String())],
        ];
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT, 'B' => NumberFormat::FORMAT_TEXT];
    }
}