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

class WeightHistorySheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'RIWAYAT BOBOT'; }

    public function headings(): array
    {
        return ['tag_id', 'weight', 'weight_type', 'weighed_at', 'notes', 'created_by', 'created_at'];
    }

    public function map($log): array
    {
        return [
            $this->forceText($log->animal?->tag_id),
            $log->weight,
            $log->weight_type,
            $log->weighed_at?->format('Y-m-d H:i:s') ?: '',
            $log->notes,
            $log->created_by,
            $log->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        return WeightLog::query()
            ->with('animal')
            ->orderBy('animal_id')
            ->orderBy('weighed_at');
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT];
    }

    private function forceText($value): string
    {
        return "=\"{$value}\"";
    }
}