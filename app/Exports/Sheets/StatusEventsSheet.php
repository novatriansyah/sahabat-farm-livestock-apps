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

class StatusEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function title(): string
    {
        return 'STATUS_CURRENT_SNAPSHOT';
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
            (string) $animal->tag_id,
            (string) ($animal->physStatus?->name ?? 'SEHAT'),
            $animal->is_active ? '1' : '0',
            $animal->is_for_sale ? '1' : '0',
            $animal->updated_at ? date('Y-m-d H:i:s', strtotime($animal->updated_at)) : '',
        ];
    }

    public function columnFormats(): array
    {
        return ['B' => NumberFormat::FORMAT_TEXT];
    }
}
