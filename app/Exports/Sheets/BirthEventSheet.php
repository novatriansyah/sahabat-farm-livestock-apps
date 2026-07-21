<?php

namespace App\Exports\Sheets;

use App\Models\BreedingEvent;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class BirthEventSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'KELAHIRAN'; }

    public function headings(): array
    {
        return ['dam_tag_id', 'birth_date', 'event_type', 'offspring_count', 'breed_id', 'sire_tag_id', 'litter_size', 'notes'];
    }

    public function map($event): array
    {
        return [
            $this->forceText($event->animal?->tag_id),
            $event->event_date?->format('Y-m-d') ?: '',
            $event->event_type,
            $event->offspring_count,
            $event->breed_id,
            $event->sire_tag_id,
            $event->litter_size,
            $event->notes,
        ];
    }

    public function query()
    {
        return BreedingEvent::query()
            ->whereIn('event_type', ['LAHIR', 'LAHIR_TUNGGAL', 'LAHIR_KEMBAR'])
            ->with('animal')
            ->orderBy('event_date');
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