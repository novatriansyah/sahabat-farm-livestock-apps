<?php

namespace App\Exports\Sheets;

use App\Models\TreatmentLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TreatmentHistorySheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'RIWAYAT KESEHATAN'; }

    public function headings(): array
    {
        return ['tag_id', 'treatment_date', 'treatment_type', 'diagnosis', 'medicine', 'dosage', 'withdrawal_days', 'veterinarian', 'cost', 'notes', 'created_at'];
    }

    public function map($log): array
    {
        return [
            $this->forceText($log->animal?->tag_id),
            $log->treatment_date?->format('Y-m-d') ?: '',
            $log->treatment_type,
            $log->diagnosis,
            $log->medicine,
            $log->dosage,
            $log->withdrawal_days,
            $log->veterinarian,
            $log->cost,
            $log->notes,
            $log->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        return TreatmentLog::query()
            ->with('animal')
            ->orderBy('animal_id')
            ->orderBy('treatment_date');
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