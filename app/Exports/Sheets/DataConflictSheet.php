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
    public function title(): string { return 'KONFLIK DATA'; }

    public function headings(): array
    {
        return ['tag_id', 'issue_type', 'description', 'suggested_action', 'reported_at'];
    }

    public function map($animal): array
    {
        $issues = [];
        if ($animal->needs_review) {
            $issues[] = ['issue_type' => 'PERLU_TINJAUAN', 'description' => 'Ternak perlu ditinjau ulang', 'suggested_action' => 'Periksa data dan perbarui'];
        }
        if ($animal->acquisition_type === 'HASIL_TERNAK' && !$animal->sire_id) {
            $issues[] = ['issue_type' => 'PEJANTAN_KOSONG', 'description' => 'Anakan tanpa data pejantan', 'suggested_action' => 'Isi sire_id dari koloni kawin'];
        }
        return [
            $this->forceText($animal->tag_id),
            $issues[0]['issue_type'] ?? 'LID',
            $issues[0]['description'] ?? 'Tidak ada masalah',
            $issues[0]['suggested_action'] ?? 'Tidak ada tindakan',
            $animal->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        return Animal::query()
            ->where('needs_review', true)
            ->orWhere(function ($q) {
                $q->where('acquisition_type', 'HASIL_TERNAK')
                  ->whereNull('sire_id');
            })
            ->orderBy('tag_id');
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