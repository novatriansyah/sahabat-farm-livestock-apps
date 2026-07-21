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
        return [
            'id', 'animal_id', 'tag_id', 'weigh_date', 'weight_kg',
            'created_at', 'updated_at',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->animal_id,
            $log->animal?->tag_id,
            $log->weigh_date?->format('Y-m-d') ?: '',
            $log->weight_kg,
            $log->created_at?->format('Y-m-d H:i:s'),
            $log->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        return WeightLog::query()
            ->with('animal')
            ->when($this->filters['from'] ?? null, fn($q, $d) => $q->whereDate('weigh_date', '>=', $d))
            ->when($this->filters['to'] ?? null, fn($q, $d) => $q->whereDate('weigh_date', '<=', $d))
            ->orderBy('weigh_date');
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,   // tag_id
        ];
    }
}