<?php

namespace App\Exports;

use App\Models\Animal;
use App\Schemas\AnimalTemplateSchema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ImportCompatibleAnimalExport implements WithMultipleSheets
{
    public function __construct(
        private ?string $partnerId = null
    ) {}

    public function sheets(): array
    {
        return [
            'DATA_TERNAK'   => new DataTernakExportSheet($this->partnerId),
            'SYSTEM_FIELDS' => new SystemFieldsExportSheet($this->partnerId),
        ];
    }
}

class DataTernakExportSheet implements WithTitle, WithHeadings, FromCollection, WithColumnFormatting, ShouldAutoSize
{
    public function __construct(
        private ?string $partnerId = null
    ) {}

    public function title(): string
    {
        return 'DATA_TERNAK';
    }

    public function headings(): array
    {
        return AnimalTemplateSchema::getHeaders();
    }

    public function collection()
    {
        $query = Animal::with(['breed', 'location', 'partner', 'physStatus', 'sire', 'dam', 'weightLogs']);

        if (!empty($this->partnerId)) {
            $query->where('partner_id', $this->partnerId);
        }

        return $query->get()->map(function (Animal $animal) {
            $latestWeight = $animal->weightLogs->sortByDesc('weigh_date')->first();

            return [
                'id'                   => (string) $animal->id,
                'tag_id'               => (string) $animal->tag_id,
                'legacy_tag_id'        => $animal->legacy_tag_id ? (string) $animal->legacy_tag_id : null,
                'gender'               => strtoupper((string) $animal->gender),
                'breed'                => $animal->breed?->name ?? 'Lokal',
                'declared_generation'  => $animal->declared_generation ?? 'UNKNOWN',
                'colors'               => $animal->colors ?? null,
                'physical_status'      => $animal->physStatus?->name ?? ($animal->is_active ? 'SEHAT' : 'DEAD'),
                'is_active'            => $animal->is_active ? '1' : '0',
                'is_for_sale'          => $animal->is_for_sale ? '1' : '0',
                'birth_date'           => $animal->birth_date?->format('Y-m-d'),
                'birth_date_estimated' => $animal->birth_date_estimated ? '1' : '0',
                'entry_date'           => $animal->entry_date?->format('Y-m-d'),
                'acquisition_type'     => $animal->acquisition_type ?? 'HASIL_TERNAK',
                'acquisition_cost'     => $animal->acquisition_cost ? (float) $animal->acquisition_cost : null,
                'initial_weight'       => $animal->initial_weight ? (float) $animal->initial_weight : null,
                'current_weight'       => $latestWeight ? (float) $latestWeight->weight_kg : ($animal->current_weight ? (float) $animal->current_weight : null),
                'last_weighed_at'      => $latestWeight?->weigh_date?->format('Y-m-d'),
                'location'             => $animal->location?->name ?? 'Kandang Utama',
                'partner'              => $animal->partner?->name ?? 'SFI Internal',
                'sire_tag_id'          => $animal->sire?->tag_id ? (string) $animal->sire->tag_id : null,
                'dam_tag_id'           => $animal->dam?->tag_id ? (string) $animal->dam->tag_id : null,
                'notes'                => $animal->notes ?? null,
                'gdrive_folder_url'    => AnimalTemplateSchema::extractGDriveUrl($animal),
            ];
        });
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
            'V' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

class SystemFieldsExportSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(
        private ?string $partnerId = null
    ) {}

    public function title(): string
    {
        return 'SYSTEM_FIELDS';
    }

    public function headings(): array
    {
        return [
            'animal_id',
            'tag_id',
            'current_hpp',
            'calculated_age_months',
            'created_at',
            'updated_at',
        ];
    }

    public function collection()
    {
        $query = Animal::query();
        if (!empty($this->partnerId)) {
            $query->where('partner_id', $this->partnerId);
        }

        return $query->get()->map(function (Animal $animal) {
            $ageMonths = $animal->birth_date ? (int) $animal->birth_date->diffInMonths(now()) : null;

            return [
                'animal_id'             => (string) $animal->id,
                'tag_id'                => (string) $animal->tag_id,
                'current_hpp'           => $animal->current_hpp ? (float) $animal->current_hpp : 0.0,
                'calculated_age_months' => $ageMonths,
                'created_at'            => $animal->created_at?->toIso8601String(),
                'updated_at'            => $animal->updated_at?->toIso8601String(),
            ];
        });
    }
}
