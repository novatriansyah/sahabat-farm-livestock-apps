<?php

namespace App\Exports\Sheets;

use App\Models\WeightLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class WeightEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
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
            'weight_kg',
            'notes',
            'created_at',
        ];
    }

    public function map($log): array
    {
        return [
            (string) $log->id,
            (string) $log->animal_id,
            (string) ($log->animal?->tag_id ?? ''),
            $log->weigh_date ? date('Y-m-d', strtotime($log->weigh_date)) : '',
            (float) ($log->weight_kg ?? 0),
            (string) ($log->notes ?? ''),
            $log->created_at ? date('Y-m-d H:i:s', strtotime($log->created_at)) : '',
        ];
    }

    public function columnFormats(): array
    {
        return ['C' => NumberFormat::FORMAT_TEXT];
    }
}
