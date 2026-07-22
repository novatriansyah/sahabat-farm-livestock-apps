<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StatusEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function title(): string
    {
        return 'STATUS_EVENTS';
    }

    public function query()
    {
        return Animal::query()->with('physStatus');
    }

    public function headings(): array
    {
        return [
            'animal_id',
            'tag_id',
            'physical_status',
            'is_active',
            'is_for_sale',
            'updated_at',
        ];
    }

    public function map($animal): array
    {
        return [
            (string) $animal->id,
            '="' . (string) $animal->tag_id . '"',
            $animal->physStatus?->name,
            $animal->is_active ? '1' : '0',
            $animal->is_for_sale ? '1' : '0',
            $animal->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
