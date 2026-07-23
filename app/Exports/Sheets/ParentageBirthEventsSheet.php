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

class ParentageBirthEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function title(): string
    {
        return 'PARENTAGE_BIRTH_EVENTS';
    }

    public function query()
    {
        return BreedingEvent::query()->with(['dam', 'sire']);
    }

    public function headings(): array
    {
        return [
            'id',
            'dam_id',
            'dam_tag_id',
            'sire_id',
            'sire_tag_id',
            'mating_date',
            'est_birth_date',
            'status',
            'notes',
            'created_at',
        ];
    }

    public function map($event): array
    {
        return [
            (string) $event->id,
            (string) $event->dam_id,
            (string) ($event->dam?->tag_id ?? ''),
            (string) ($event->sire_id ?? ''),
            (string) ($event->sire?->tag_id ?? ''),
            $event->mating_date ? date('Y-m-d', strtotime($event->mating_date)) : '',
            $event->est_birth_date ? date('Y-m-d', strtotime($event->est_birth_date)) : '',
            (string) ($event->status ?? 'MENUNGGU'),
            (string) ($event->notes ?? ''),
            $event->created_at ? date('Y-m-d H:i:s', strtotime($event->created_at)) : '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
