<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
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
            '="' . str_replace('"', '""', (string) $animal->tag_id) . '"',
            $animal->legacy_tag_id,
            $animal->gender,
            $animal->breed?->name,
            $animal->generation,
            $animal->ear_tag_color,
            $animal->necklace_color,
            $animal->physStatus?->name,
            $animal->is_active ? '1' : '0',
            $animal->is_for_sale ? '1' : '0',
            $animal->birth_date?->format('Y-m-d') ?: '',
            $animal->entry_date?->format('Y-m-d') ?: '',
            $animal->acquisition_type,
            $animal->purchase_price,
            $animal->sale_price,
            $animal->latestWeightLog?->weight,
            $animal->current_hpp,
            $animal->location?->name,
            $animal->partner?->name,
            $animal->sire_id,
            $animal->sire ? '="' . str_replace('"', '""', (string) $animal->sire->tag_id) . '"' : '',
            $animal->dam_id,
            $animal->dam ? '="' . str_replace('"', '""', (string) $animal->dam->tag_id) . '"' : '',
            $animal->gdrive_folder_url,
            $animal->photo_url,
            $animal->created_at?->format('Y-m-d H:i:s'),
            $animal->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // tag_id
            'V' => NumberFormat::FORMAT_TEXT, // sire_tag_id
            'X' => NumberFormat::FORMAT_TEXT, // dam_tag_id
        ];
    }
}
