<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LocationHistorySheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function title(): string
    {
        return 'LOCATION_HISTORY';
    }

    public function query()
    {
        return Animal::query()->with('location');
    }

    public function headings(): array
    {
        return [
            'animal_id',
            'tag_id',
            'location_id',
            'location_name',
            'updated_at',
        ];
    }

    public function map($animal): array
    {
        return [
            (string) $animal->id,
            '="' . (string) $animal->tag_id . '"',
            $animal->current_location_id,
            $animal->location?->name,
            $animal->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
