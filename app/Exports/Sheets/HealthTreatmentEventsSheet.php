<?php

namespace App\Exports\Sheets;

use App\Models\TreatmentLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HealthTreatmentEventsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function title(): string
    {
        return 'HEALTH_TREATMENT_EVENTS';
    }

    public function query()
    {
        return TreatmentLog::query()->with(['animal', 'disease']);
    }

    public function headings(): array
    {
        return [
            'id',
            'animal_id',
            'tag_id',
            'treatment_date',
            'disease_name',
            'diagnosis',
            'treatment',
            'cost',
            'status',
            'notes',
            'created_at',
        ];
    }

    public function map($log): array
    {
        return [
            (string) $log->id,
            (string) $log->animal_id,
            '="' . (string) $log->animal?->tag_id . '"',
            $log->treatment_date?->format('Y-m-d'),
            $log->disease?->name,
            $log->diagnosis,
            $log->treatment,
            $log->cost,
            $log->status,
            $log->notes,
            $log->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
