<?php

namespace App\Exports\Sheets;

use App\Models\AnimalOwnershipLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class OwnershipHistorySheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function title(): string { return 'RIWAYAT PEMILIK'; }

    public function headings(): array
    {
        return ['tag_id', 'partner_name', 'start_date', 'end_date', 'is_current', 'notes'];
    }

    public function map($log): array
    {
        return [
            $this->forceText($log->animal?->tag_id),
            $log->partner?->name,
            $log->start_date?->format('Y-m-d') ?: '',
            $log->end_date?->format('Y-m-d') ?: '',
            $log->is_current ? 'Ya' : 'Tidak',
            $log->notes,
        ];
    }

    public function query()
    {
        return AnimalOwnershipLog::query()
            ->with(['animal', 'partner'])
            ->orderBy('animal_id')
            ->orderBy('start_date');
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