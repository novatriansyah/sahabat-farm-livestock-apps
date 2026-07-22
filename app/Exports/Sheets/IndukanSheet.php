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

class IndukanSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
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
            'notes',
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
            $animal->latestWeightLog?->weight,
            $animal->physStatus?->name,
            $animal->is_active ? 'Ya' : 'Tidak',
            $animal->necklace_color,
            $animal->location?->name,
            $animal->partner?->name,
            $animal->current_hpp,
            $animal->offspring()->count(),
            $animal->breedingEvents()?->latest()?->first()?->created_at?->format('Y-m-d'),
            $animal->lambing_interval_days,
            $animal->google_drive_link,
            $animal->photos()->first()?->url,
            '',
            $animal->notes,
            $animal->created_at?->format('Y-m-d H:i:s'),
            $animal->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        return Animal::query()
            ->with(['breed', 'physStatus', 'location', 'partner', 'offspring', 'photos', 'latestWeightLog'])
            ->when($this->filters['partner_id'] ?? null, fn($q, $id) => $q->where('partner_id', $id))
            ->when($this->filters['location_id'] ?? null, fn($q, $id) => $q->where('current_location_id', $id))
            ->orderBy('tag_id');
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,   // tag_id
            'B' => NumberFormat::FORMAT_TEXT,   // legacy_tag_number
        ];
    }

    private function forceText($value): string
    {
        return (string) $value;
    }
}