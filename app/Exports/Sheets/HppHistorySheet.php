<?php

namespace App\Exports\Sheets;

use App\Models\HppMonthlySnapshot;
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
        return ['tag_id', 'period_year', 'period_month', 'opening_hpp', 'feed_cost', 'medicine_cost', 'other_cost', 'overhead_cost', 'closing_hpp', 'active_days'];
    }

    public function map($snap): array
    {
        return [
            $this->forceText($snap->animal?->tag_id),
            $snap->period_year,
            $snap->period_month,
            $snap->opening_hpp,
            $snap->feed_cost,
            $snap->medicine_cost,
            $snap->other_cost,
            $snap->overhead_cost,
            $snap->closing_hpp,
            $snap->active_days,
        ];
    }

    public function query()
    {
        return HppMonthlySnapshot::query()
            ->with('animal')
            ->orderBy('animal_id')
            ->orderBy('period_year')
            ->orderBy('period_month');
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