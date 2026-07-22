<?php

namespace App\Exports\Sheets;

use App\Models\BreedingEvent;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ParentageBirthEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
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
            'pregnancy_check_date',
            'pregnancy_status',
            'est_birth_date',
            'actual_birth_date',
            'offspring_count',
            'notes',
            'created_at',
        ];
    }

    public function map($event): array
    {
        return [
            (string) $event->id,
            (string) $event->dam_id,
            '="' . (string) $event->dam?->tag_id . '"',
            (string) $event->sire_id,
            '="' . (string) $event->sire?->tag_id . '"',
            $event->mating_date?->format('Y-m-d'),
            $event->pregnancy_check_date?->format('Y-m-d'),
            $event->pregnancy_status,
            $event->est_birth_date?->format('Y-m-d'),
            $event->actual_birth_date?->format('Y-m-d'),
            $event->offspring_count,
            $event->notes,
            $event->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
