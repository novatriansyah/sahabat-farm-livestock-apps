<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $animals;

    public function __construct($animals)
    {
        $this->animals = $animals;
    }

    public function collection()
    {
        return $this->animals;
    }

    public function title(): string
    {
        return 'Laporan Stok Ternak';
    }

    public function headings(): array
    {
        return [
            'Tag ID',
            'Jenis Kelamin',
            'Ras / Breed',
            'Usia (Bulan)',
            'Lokasi / Kandang',
            'Status Fisik',
            'Pemilik / Mitra',
            'Berat Badan Terakhir (kg)',
            'ADG (kg/hari)',
            'HPP (Rp)'
        ];
    }

    public function map($animal): array
    {
        return [
            $animal->tag_id,
            $animal->gender === 'JANTAN' ? 'Jantan' : 'Betina',
            $animal->breed->name ?? '-',
            number_format($animal->birth_date->diffInMonths(now()), 1),
            $animal->location->name ?? '-',
            $animal->physStatus->name ?? '-',
            $animal->partner->name ?? '-',
            $animal->latestWeightLog->weight_kg ?? '-',
            number_format($animal->daily_adg, 3),
            number_format($animal->current_hpp, 0, ',', '.')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
