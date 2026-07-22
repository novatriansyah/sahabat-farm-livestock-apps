<?php

namespace App\Exports\Sheets;

use App\Models\ExitLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExitDeathEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function title(): string
    {
        return 'EXIT_DEATH_EVENTS';
    }

    public function query()
    {
        return ExitLog::query()->with('animal');
    }

    public function headings(): array
    {
        return [
            'id',
            'animal_id',
            'tag_id',
            'exit_type',
            'exit_date',
            'price',
            'reason',
            'destination',
            'notes',
            'created_at',
        ];
    }

    public function map($log): array
    {
        return [
            (string) $log->id,
            (string) $log->animal_id,
            '="' . (string) $log->animal?->tag_id . '"',
            $log->exit_type,
            $log->exit_date?->format('Y-m-d'),
            $log->price,
            $log->reason,
            $log->destination,
            $log->notes,
            $log->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
