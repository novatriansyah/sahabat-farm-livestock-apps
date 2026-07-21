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

class EarTagHistorySheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'RIWAYAT EARTAG'; }

    public function headings(): array
    {
        return ['tag_id', 'old_tag_id', 'new_tag_id', 'reason', 'changed_by', 'changed_at', 'notes'];
    }

    public function map($log): array
    {
        return [
            $log->animal?->tag_id,
            $log->old_tag_id,
            $log->new_tag_id,
            $log->reason,
            $log->changed_by,
            $log->changed_at?->format('Y-m-d H:i:s') ?: '',
            $log->notes,
        ];
    }

    public function query()
    {
        return AnimalEarTagLog::query()
            ->with('animal')
            ->orderBy('animal_id')
            ->orderBy('changed_at');
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,   // tag_id
            'B' => NumberFormat::FORMAT_TEXT,   // old_tag_id
            'C' => NumberFormat::FORMAT_TEXT,   // new_tag_id
        ];
    }
}