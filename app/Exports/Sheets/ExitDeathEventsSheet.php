<?php

namespace App\Exports\Sheets;

use App\Models\ExitLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ExitDeathEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
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
            'final_hpp',
            'created_at',
        ];
    }

    public function map($log): array
    {
        return [
            (string) $log->id,
            (string) $log->animal_id,
            (string) ($log->animal?->tag_id ?? ''),
            (string) ($log->exit_type ?? 'MATI'),
            $log->exit_date ? date('Y-m-d', strtotime($log->exit_date)) : '',
            (float) ($log->price ?? 0),
            (float) ($log->final_hpp ?? 0),
            $log->created_at ? date('Y-m-d H:i:s', strtotime($log->created_at)) : '',
        ];
    }

    public function columnFormats(): array
    {
        return ['C' => NumberFormat::FORMAT_TEXT];
    }
}
