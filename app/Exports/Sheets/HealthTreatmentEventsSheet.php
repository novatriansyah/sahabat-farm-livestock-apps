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

class HealthTreatmentEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function title(): string
    {
        return 'HEALTH_TREATMENT_EVENTS';
    }

    public function query()
    {
        return TreatmentLog::query()->with('animal');
    }

    public function headings(): array
    {
        return [
            'id',
            'animal_id',
            'tag_id',
            'treatment_date',
            'type',
            'notes',
            'created_at',
        ];
    }

    public function map($log): array
    {
        return [
            (string) $log->id,
            (string) $log->animal_id,
            (string) ($log->animal?->tag_id ?? ''),
            $log->treatment_date ? date('Y-m-d', strtotime($log->treatment_date)) : '',
            (string) ($log->type ?? 'Vitamin / Obat Kembung'),
            (string) ($log->notes ?? ''),
            $log->created_at ? date('Y-m-d H:i:s', strtotime($log->created_at)) : '',
        ];
    }

    public function columnFormats(): array
    {
        return ['C' => NumberFormat::FORMAT_TEXT];
    }
}
