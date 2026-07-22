<?php

namespace App\Exports\Sheets;

use App\Models\WeightLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WeightEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function title(): string
    {
        return 'WEIGHT_EVENTS';
    }

    public function query()
    {
        return WeightLog::query()->with('animal');
    }

    public function headings(): array
    {
        return [
            'id',
            'animal_id',
            'tag_id',
            'weigh_date',
            'weight',
            'adg',
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
            $log->weigh_date?->format('Y-m-d'),
            $log->weight,
            $log->adg,
            $log->notes,
            $log->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
