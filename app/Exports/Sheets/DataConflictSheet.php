<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class DataConflictSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'KONFLIK DATA'; }

    public function headings(): array
    {
        return [
            'tag_id', 'issue_type', 'description', 'field', 'current_value',
            'expected_value', 'severity', 'needs_review',
        ];
    }

    public function map($animal): array
    {
        $issues = [];
        if (!$animal->sire_id && $animal->dam_id) {
            $issues[] = [
                $animal->tag_id,
                'MISSING_SIRE',
                'Pejantan tidak tercatat',
                'sire_id',
                '',
                'Diharapkan terisi',
                'HIGH',
                'Ya',
            ];
        }
        if ($animal->needs_review) {
            $issues[] = [
                $animal->tag_id,
                'NEEDS_REVIEW',
                $animal->notes ?? 'Menunggu verifikasi',
                'needs_review',
                'Ya',
                'Tidak',
                'MEDIUM',
                'Ya',
            ];
        }
        return $issues;
    }

    public function query()
    {
        return Animal::query()
            ->where(function ($q) {
                $q->whereNull('sire_id')->whereNotNull('dam_id')
                  ->orWhere('needs_review', true);
            })
            ->orderBy('tag_id');
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT];
    }
}