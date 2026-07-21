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
        return ['colony_name', 'sire_tag_id', 'start_date', 'end_date', 'member_tags', 'notes'];
    }

    public function map($colony): array
    {
        return [
            $colony->name,
            $colony->sire?->tag_id,
            $colony->start_date?->format('Y-m-d') ?: '',
            $colony->end_date?->format('Y-m-d') ?: '',
            $colony->members?->pluck('animal.tag_id')->implode(', '),
            $colony->notes,
        ];
    }

    public function query()
    {
        return MatingColony::query()
            ->with(['sire', 'members.animal'])
            ->orderBy('start_date');
    }

    public function columnFormats(): array
    {
        return ['B' => NumberFormat::FORMAT_TEXT];
    }

    private function forceText($value): string
    {
        return "=\"{$value}\"";
    }
}