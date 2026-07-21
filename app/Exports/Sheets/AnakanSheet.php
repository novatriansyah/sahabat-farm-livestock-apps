<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AnakanSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'ANAKAN'; }

    public function headings(): array
    {
        return [
            'tag_id', 'legacy_tag_number', 'old_tag_id',
            'dam_tag_id', 'sire_tag_id', 'sire_confidence',
            'gender', 'breed_name', 'generation', 'generation_confidence', 'ear_tag_color',
            'birth_date', 'birth_weight', 'is_birth_weight_estimated', 'litter_size',
            'current_weight', 'adg', 'weaning_weight', 'weaning_date',
            'physical_status', 'is_active', 'necklace_color',
            'location_name', 'partner_name',
            'current_hpp', 'purchase_price', 'sale_price',
            'gdrive_folder_url', 'photo_url', 'video_url',
            'confidence_level', 'data_source', 'notes', 'needs_review',
            'created_at', 'updated_at', 'created_by', 'last_modified_by',
        ];
    }

    public function map($animal): array
    {
        return [
            $this->forceText($animal->tag_id),
            $animal->legacy_tag_id,
            $animal->old_tag_id,
            $animal->dam?->tag_id,
            $animal->sire?->tag_id,
            $animal->sire_confidence,
            $animal->gender,
            $animal->breed?->name,
            $animal->generation,
            $animal->generation_confidence,
            $animal->ear_tag_color,
            $animal->birth_date?->format('Y-m-d') ?: '',
            $animal->birth_weight,
            $animal->is_birth_weight_estimated ? 'Ya' : 'Tidak',
            $animal->litter_size,
            $animal->latestWeight()?->weight,
            $animal->daily_adg,
            $animal->weaning_weight,
            $animal->weaning_date?->format('Y-m-d') ?: '',
            $animal->physStatus?->name,
            $animal->is_active ? 'Ya' : 'Tidak',
            $animal->necklace_color,
            $animal->location?->name,
            $animal->partner?->name,
            $animal->current_hpp,
            $animal->purchase_price,
            $animal->sale_price,
            $animal->google_drive_link,
            $animal->photos()->first()?->url,
            $animal->videos()->first()?->url,
            $animal->confidence_level,
            $animal->data_source,
            $animal->notes,
            $animal->needs_review ? 'Ya' : '',
            $animal->created_at?->format('Y-m-d H:i:s'),
            $animal->updated_at?->format('Y-m-d H:i:s'),
            $animal->created_by,
            $animal->last_modified_by,
        ];
    }

    public function query()
    {
        return Animal::query()
            ->with(['sire', 'dam', 'breed', 'physStatus', 'location', 'partner', 'photos', 'videos'])
            ->when($this->filters['partner_id'] ?? null, fn($q, $id) => $q->where('partner_id', $id))
            ->when($this->filters['location_id'] ?? null, fn($q, $id) => $q->where('current_location_id', $id))
            ->orderBy('tag_id');
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,   // tag_id
            'B' => NumberFormat::FORMAT_TEXT,   // legacy_tag_number
            'C' => NumberFormat::FORMAT_TEXT,   // old_tag_id
            'D' => NumberFormat::FORMAT_TEXT,   // dam_tag_id
            'E' => NumberFormat::FORMAT_TEXT,   // sire_tag_id
        ];
    }

    private function forceText($value): string
    {
        return (string) $value;
    }
}