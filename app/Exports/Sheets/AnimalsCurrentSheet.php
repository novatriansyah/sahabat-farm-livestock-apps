<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use App\Schemas\AnimalTemplateSchema;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AnimalsCurrentSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function title(): string
    {
        return 'ANIMALS_CURRENT';
    }

    public function query()
    {
        // Unfiltered query including active, inactive, dead (like B43), and sold animals
        return Animal::query()
            ->with(['breed', 'location', 'partner', 'physStatus', 'sire', 'dam', 'latestWeightLog']);
    }

    public function headings(): array
    {
        return [
            'id',
            'tag_id',
            'legacy_tag_id',
            'gender',
            'breed_name',
            'generation',
            'ear_tag_color',
            'necklace_color',
            'physical_status',
            'is_active',
            'is_for_sale',
            'birth_date',
            'entry_date',
            'acquisition_type',
            'purchase_price',
            'sale_price',
            'current_weight',
            'current_hpp',
            'location_name',
            'partner_name',
            'sire_id',
            'sire_tag_id',
            'dam_id',
            'dam_tag_id',
            'gdrive_folder_url',
            'photo_url',
            'created_at',
            'updated_at',
        ];
    }

    public function map($animal): array
    {
        return [
            (string) $animal->id,
            (string) $animal->tag_id,
            (string) ($animal->legacy_tag_id ?? ''),
            (string) ($animal->gender ?? ''),
            (string) ($animal->breed?->name ?? ''),
            (string) ($animal->generation ?? ''),
            (string) ($animal->ear_tag_color ?? ''),
            (string) ($animal->necklace_color ?? ''),
            (string) ($animal->physStatus?->name ?? 'SEHAT'),
            $animal->is_active ? '1' : '0',
            $animal->is_for_sale ? '1' : '0',
            $animal->birth_date ? date('Y-m-d', strtotime($animal->birth_date)) : '',
            $animal->entry_date ? date('Y-m-d', strtotime($animal->entry_date)) : '',
            (string) ($animal->acquisition_type ?? ''),
            $animal->purchase_price ?? 0,
            $animal->sale_price ?? 0,
            $animal->latestWeightLog?->weight_kg ?? 0,
            $animal->current_hpp ?? 0,
            (string) ($animal->location?->name ?? ''),
            (string) ($animal->partner?->name ?? 'SFI Internal'),
            (string) ($animal->sire_id ?? ''),
            (string) ($animal->sire?->tag_id ?? ''),
            (string) ($animal->dam_id ?? ''),
            (string) ($animal->dam?->tag_id ?? ''),
            (string) AnimalTemplateSchema::extractGDriveUrl($animal),
            (string) ($animal->photo_url ?? ''),
            $animal->created_at ? date('Y-m-d H:i:s', strtotime($animal->created_at)) : '',
            $animal->updated_at ? date('Y-m-d H:i:s', strtotime($animal->updated_at)) : '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // tag_id
            'C' => NumberFormat::FORMAT_TEXT, // legacy_tag_id
            'V' => NumberFormat::FORMAT_TEXT, // sire_tag_id
            'X' => NumberFormat::FORMAT_TEXT, // dam_tag_id
        ];
    }
}
