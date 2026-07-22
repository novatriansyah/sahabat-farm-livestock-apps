<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class DataQualityIssuesSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function title(): string
    {
        return 'DATA_QUALITY_ISSUES';
    }

    public function collection(): Collection
    {
        $issues = collect([]);

        // Audit animals for data quality issues
        $animals = Animal::with(['sire', 'dam', 'weightLogs'])->get();
        foreach ($animals as $animal) {
            // Missing birth date
            if (!$animal->birth_date) {
                $issues->push([
                    'animal_id' => (string) $animal->id,
                    'tag_id' => (string) $animal->tag_id,
                    'issue_type' => 'MISSING_BIRTH_DATE',
                    'severity' => 'WARNING',
                    'description' => 'Tanggal lahir belum diisi',
                ]);
            }
            // Missing breed
            if (!$animal->breed_id) {
                $issues->push([
                    'animal_id' => (string) $animal->id,
                    'tag_id' => (string) $animal->tag_id,
                    'issue_type' => 'MISSING_BREED',
                    'severity' => 'WARNING',
                    'description' => 'Breed/ras belum diset',
                ]);
            }
            // Missing weight
            if ($animal->weightLogs->isEmpty()) {
                $issues->push([
                    'animal_id' => (string) $animal->id,
                    'tag_id' => (string) $animal->tag_id,
                    'issue_type' => 'MISSING_WEIGHT_LOG',
                    'severity' => 'INFO',
                    'description' => 'Belum ada riwayat penimbangan bobot',
                ]);
            }
        }

        return $issues;
    }

    public function headings(): array
    {
        return [
            'animal_id',
            'tag_id',
            'issue_type',
            'severity',
            'description',
        ];
    }

    public function map($row): array
    {
        return [
            $row['animal_id'],
            '="' . $row['tag_id'] . '"',
            $row['issue_type'],
            $row['severity'],
            $row['description'],
        ];
    }
}
