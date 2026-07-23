<?php

namespace App\Exports;

use App\Services\ReconciliationService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReconciliationExport implements WithMultipleSheets
{
    public function __construct(
        private array $reconciliationData
    ) {}

    public function sheets(): array
    {
        return [
            'RINGKASAN_REKONSILIASI' => new ReconciliationSummarySheet($this->reconciliationData),
            'DETAIL_REKONSILIASI'    => new ReconciliationDetailSheet($this->reconciliationData),
        ];
    }
}

class ReconciliationSummarySheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(private array $data) {}

    public function title(): string { return 'RINGKASAN_REKONSILIASI'; }

    public function headings(): array
    {
        return ['status_key', 'count', 'description'];
    }

    public function collection()
    {
        $summary = $this->data['summary'] ?? [];

        return collect([
            ['status' => 'SAME', 'count' => $summary['same_count'] ?? 0, 'desc' => 'Ternak cocok sempurna antara database dan file Excel'],
            ['status' => 'WEB_ONLY', 'count' => $summary['web_only_count'] ?? 0, 'desc' => 'Ternak hanya ada di database web SFI'],
            ['status' => 'EXCEL_ONLY', 'count' => $summary['excel_only_count'] ?? 0, 'desc' => 'Ternak hanya ada di file Excel mitra'],
            ['status' => 'CONFLICT', 'count' => $summary['conflict_count'] ?? 0, 'desc' => 'Ada perbedaan nilai field antar sumber data'],
            ['status' => 'UNCERTAIN', 'count' => $summary['uncertain_count'] ?? 0, 'desc' => 'Kandidat ambiguous / tag_id ganda'],
            ['status' => 'TOTAL_UNION', 'count' => $summary['total_unique_union'] ?? 0, 'desc' => 'Persamaan Union: SAME + WEB_ONLY + EXCEL_ONLY + CONFLICT + UNCERTAIN'],
        ]);
    }
}

class ReconciliationDetailSheet implements WithTitle, WithHeadings, FromCollection, WithColumnFormatting, ShouldAutoSize
{
    public function __construct(private array $data) {}

    public function title(): string { return 'DETAIL_REKONSILIASI'; }

    public function headings(): array
    {
        return ['entity_id', 'tag_id', 'status', 'matched_by', 'conflicts_count', 'notes'];
    }

    public function collection()
    {
        $results = collect($this->data['results'] ?? []);

        return $results->map(function ($item) {
            return [
                'entity_id'       => (string) ($item['entity_id'] ?? ''),
                'tag_id'          => (string) ($item['tag_id'] ?? ''),
                'status'          => (string) ($item['status'] ?? 'SAME'),
                'matched_by'      => (string) ($item['matched_by'] ?? 'tag_id'),
                'conflicts_count' => count($item['conflicts'] ?? []),
                'notes'           => (string) ($item['notes'] ?? ''),
            ];
        });
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
