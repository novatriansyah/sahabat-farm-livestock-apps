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
        return [
            'event_id', 'dam_tag_id', 'sire_tag_id', 'mating_date',
            'est_birth_date', 'event_type', 'status', 'offspring_count',
            'notes', 'created_at',
        ];
    }

    public function map($event): array
    {
        return [
            $event->id,
            $event->dam?->tag_id,
            $event->sire?->tag_id,
            $event->mating_date?->format('Y-m-d') ?: '',
            $event->est_birth_date?->format('Y-m-d') ?: '',
            $event->event_type,
            $event->status,
            $event->offspring_count,
            $event->notes,
            $event->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        return BreedingEvent::query()
            ->with(['dam', 'sire'])
            ->when($this->filters['from'] ?? null, fn($q, $d) => $q->whereDate('mating_date', '>=', $d))
            ->when($this->filters['to'] ?? null, fn($q, $d) => $q->whereDate('mating_date', '<=', $d))
            ->orderBy('mating_date');
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,   // dam_tag_id
            'C' => NumberFormat::FORMAT_TEXT,   // sire_tag_id
        ];
    }
}