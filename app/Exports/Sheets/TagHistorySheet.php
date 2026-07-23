<?php

namespace App\Exports\Sheets;

use App\Models\AnimalEarTagLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TagHistorySheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function title(): string
    {
        return 'TAG_HISTORY';
    }

    public function query()
    {
        return AnimalEarTagLog::query()->with(['animal', 'user']);
    }

    public function headings(): array
    {
        return [
            'id',
            'animal_id',
            'current_tag_id',
            'old_tag_id',
            'new_tag_id',
            'reason',
            'changed_by',
            'created_at',
        ];
    }

    public function map($log): array
    {
        return [
            (string) $log->id,
            (string) $log->animal_id,
            (string) ($log->animal?->tag_id ?? ''),
            (string) ($log->old_tag_id ?? ''),
            (string) ($log->new_tag_id ?? ''),
            (string) ($log->reason ?? ''),
            (string) ($log->user?->name ?? 'System'),
            $log->created_at ? date('Y-m-d H:i:s', strtotime($log->created_at)) : '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
