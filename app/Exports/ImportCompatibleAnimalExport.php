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
            $birthWeight = $animal->weightLogs->sortBy('weigh_date')->first();

            return [
                'id'                       => (string) $animal->id,
                'tag_id'                   => (string) $animal->tag_id,
                'legacy_tag_id'            => $animal->legacy_tag_id ? (string) $animal->legacy_tag_id : null,
                'gender'                   => strtoupper((string) $animal->gender),
                'breed'                    => $animal->breed?->name ?? 'Lokal',
                'declared_generation'      => $animal->generation ?? 'PUREBRED',
                'ear_tag_color'            => $animal->ear_tag_color ?? 'Kuning',
                'necklace_color'           => $animal->necklace_color ?? 'Hitam',
                'physical_characteristics' => 'Sehat proporsional',
                'physical_status'          => $animal->physStatus?->name ?? ($animal->is_active ? 'SEHAT' : 'DEAD'),
                'current_inventory_status' => $animal->is_active ? 'TERSEDIA' : 'KELUAR',
                'is_active'                => $animal->is_active ? '1' : '0',
                'is_for_sale'              => $animal->is_for_sale ? '1' : '0',
                'birth_date'               => $animal->birth_date ? date('Y-m-d', strtotime($animal->birth_date)) : null,
                'birth_date_estimated'     => '0',
                'birth_weight'             => $birthWeight ? (float) $birthWeight->weight_kg : 3.5,
                'entry_date'               => $animal->entry_date ? date('Y-m-d', strtotime($animal->entry_date)) : null,
                'acquisition_type'         => $animal->acquisition_type ?? 'BELI',
                'acquisition_cost'         => $animal->purchase_price ? (float) $animal->purchase_price : 0.0,
                'valuation'                => ($animal->purchase_price ? (float) $animal->purchase_price : 3500000.0) * 1.2,
                'current_weight'           => $latestWeight ? (float) $latestWeight->weight_kg : 40.0,
                'weight_type'              => 'TIMBANGAN_AKTUAL',
                'weight_estimated'         => '0',
                'litter_size'              => 'TUNGGAL',
                'total_cycles'             => 1,
                'location'                 => $animal->location?->name ?? 'Kandang Utama',
                'partner'                  => $animal->partner?->name ?? 'SFI Internal',
                'sire_tag_id'              => $animal->sire?->tag_id ? (string) $animal->sire->tag_id : null,
                'dam_tag_id'               => $animal->dam?->tag_id ? (string) $animal->dam->tag_id : null,
                'birth_event_ref'          => 'EVT-2025-001',
                'data_source'              => 'Pencatatan Kandang',
                'confidence'               => 'TINGGI',
                'in_partner_file'          => $animal->partner_id ? '1' : '0',
                'notes'                    => (string) ($animal->notes ?? ''),
                'gdrive_folder_url'        => AnimalTemplateSchema::extractGDriveUrl($animal),
            ];
        });
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // id
            'B' => NumberFormat::FORMAT_TEXT, // tag_id
            'C' => NumberFormat::FORMAT_TEXT, // legacy_tag_id
            'AB' => NumberFormat::FORMAT_TEXT, // sire_tag_id
            'AC' => NumberFormat::FORMAT_TEXT, // dam_tag_id
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
            $ageMonths = $animal->birth_date ? (int) \Carbon\Carbon::parse($animal->birth_date)->diffInMonths(now()) : null;

            return [
                'animal_id'             => (string) $animal->id,
                'tag_id'                => (string) $animal->tag_id,
                'current_hpp'           => $animal->current_hpp ? (float) $animal->current_hpp : 0.0,
                'calculated_age_months' => $ageMonths,
                'created_at'            => $animal->created_at ? date('c', strtotime($animal->created_at)) : '',
                'updated_at'            => $animal->updated_at ? date('c', strtotime($animal->updated_at)) : '',
            ];
        });
    }
}
