<?php

namespace App\Exports\Sheets;

use App\Models\MatingColony;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class MatingColonySheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'KOLONI KAWIN'; }

    public function headings(): array
    {
        return [
            'colony_id', 'sire_tag_id', 'sire_name', 'start_date', 'end_date',
            'status', 'member_count', 'notes',
        ];
    }

    public function map($colony): array
    {
        return [
            $colony->id,
            $colony->sire?->tag_id,
            $colony->sire?->tag_id . ' - ' . ($colony->sire?->breed?->name ?? ''),
            $colony->start_date?->format('Y-m-d') ?: '',
            $colony->end_date?->format('Y-m-d') ?: '',
            $colony->status,
            $colony->members?->count() ?? 0,
            $colony->notes,
        ];
    }

    public function query()
    {
        return MatingColony::query()
            ->with(['sire.breed', 'members'])
            ->orderBy('start_date');
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,   // sire_tag_id
        ];
    }
}