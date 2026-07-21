<?php

namespace App\Exports\Sheets;

use App\Models\HppManualCost;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class HppHistorySheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'RIWAYAT HPP'; }

    public function headings(): array
    {
        return [
            'id', 'animal_id', 'tag_id', 'cost_type', 'amount',
            'description', 'input_date', 'created_at',
        ];
    }

    public function map($cost): array
    {
        return [
            $cost->id,
            $cost->animal_id,
            $cost->animal?->tag_id,
            $cost->cost_type,
            $cost->amount,
            $cost->description,
            $cost->input_date?->format('Y-m-d') ?: '',
            $cost->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        return HppManualCost::query()
            ->with('animal')
            ->when($this->filters['from'] ?? null, fn($q, $d) => $q->whereDate('input_date', '>=', $d))
            ->when($this->filters['to'] ?? null, fn($q, $d) => $q->whereDate('input_date', '<=', $d))
            ->orderBy('input_date');
    }

    public function columnFormats(): array
    {
        return ['C' => NumberFormat::FORMAT_TEXT];
    }
}