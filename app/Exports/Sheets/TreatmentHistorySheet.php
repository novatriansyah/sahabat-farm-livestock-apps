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
        return [
            'id', 'animal_id', 'tag_id', 'treatment_date', 'type',
            'disease_name', 'notes', 'next_due_date',
            'created_at', 'updated_at',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->animal_id,
            $log->animal?->tag_id,
            $log->treatment_date?->format('Y-m-d') ?: '',
            $log->type,
            $log->disease?->name,
            $log->notes,
            $log->next_due_date?->format('Y-m-d') ?: '',
            $log->created_at?->format('Y-m-d H:i:s'),
            $log->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        return TreatmentLog::query()
            ->with(['animal', 'disease'])
            ->when($this->filters['from'] ?? null, fn($q, $d) => $q->whereDate('treatment_date', '>=', $d))
            ->when($this->filters['to'] ?? null, fn($q, $d) => $q->whereDate('treatment_date', '<=', $d))
            ->orderBy('treatment_date');
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,   // tag_id
        ];
    }
}