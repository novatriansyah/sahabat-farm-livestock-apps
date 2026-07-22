<?php

namespace App\Exports;

use App\Schemas\AnimalTemplateSchema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BlankImportTemplate implements WithMultipleSheets
{
    public function __construct(
        private array $referenceData = []
    ) {}

    public function sheets(): array
    {
        return [
            'PETUNJUK'         => new PetunjukSheet(),
            'DATA_TERNAK'      => new DataTernakBlankSheet(),
            'ANIMALS_CURRENT'  => new AnimalsCurrentBlankSheet(),
            'INDUKAN'          => new IndukanBlankSheet(),
            'ANAKAN'           => new AnakanBlankSheet(),
            'REFERENSI'        => new ReferensiSheet($this->referenceData),
        ];
    }
}

class PetunjukSheet implements WithTitle, WithEvents
{
    public function title(): string
    {
        return 'PETUNJUK';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'PETUNJUK PENGISIAN TEMPLATE CANONICAL IMPORT SFI');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $instructions = [
                    ['', ''],
                    ['A. CARA PENGISIAN TEMPLATE', ''],
                    ['1. Sheet DATA_TERNAK / ANIMALS_CURRENT:', 'Digunakan untuk import master ternak lengkap (rekomendasi utama)'],
                    ['2. Sheet INDUKAN / ANAKAN:', 'Gunakan untuk klasifikasi laporan atau import parsial'],
                    ['3. Baris Contoh [CONTOH]:', 'Baris yang diawali [CONTOH] adalah contoh data dan WAJIB dihapus sebelum di-upload!'],
                    ['4. Kolom bertanda *:', 'Wajib diisi (misal tag_id*, gender*, breed*, location*, partner*)'],
                    ['', ''],
                    ['B. FORMAT DATA & ATURAN VALIDASI', ''],
                    ['Tag ID (Ear Tag):', 'Wajib diketik sebagai TEKS agar nomor seperti 036 atau 010 tidak hilang angka nol terdepan'],
                    ['Tanggal (birth_date):', 'Format YYYY-MM-DD (contoh: 2024-05-15)'],
                    ['Gender / Jenis Kelamin:', 'JANTAN atau BETINA'],
                    ['Status Fisik:', 'SEHAT, SAKIT, KARANTINA, AFKIR, MATI, TERJUAL'],
                    ['Angka Desimal:', 'Gunakan TITIK sebagai pemisah desimal (contoh: 4.15), BUKAN koma'],
                    ['Harga / Rupiah:', 'Angka bulat tanpa titik/koma (contoh: 2500000)'],
                    ['', ''],
                    ['C. SHIFTING REFERENSI', ''],
                    ['Lihat sheet REFERENSI', 'Untuk daftar Breed, Lokasi Kandang, Partner, dan Status Fisik yang valid.'],
                ];

                $row = 3;
                foreach ($instructions as $line) {
                    $sheet->setCellValue("A{$row}", $line[0]);
                    $sheet->setCellValue("B{$row}", $line[1]);
                    if (!empty($line[0]) && preg_match('/^[A-Z]\./', $line[0])) {
                        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    }
                    $row++;
                }

                $sheet->getColumnDimension('A')->setWidth(35);
                $sheet->getColumnDimension('B')->setWidth(65);
            },
        ];
    }
}

class DataTernakBlankSheet implements WithTitle, WithHeadings, FromArray, WithColumnFormatting, ShouldAutoSize
{
    public function title(): string
    {
        return 'DATA_TERNAK';
    }

    public function headings(): array
    {
        return AnimalTemplateSchema::getHeaders();
    }

    public function array(): array
    {
        return [
            [
                '[CONTOH] uuid-sample-1234',
                '036',
                'OLD-036',
                'BETINA',
                'Garut',
                'F1',
                'Putih Kepala Hitam',
                'SEHAT',
                '1',
                '0',
                '2024-05-15',
                '0',
                '2024-06-01',
                'HASIL_TERNAK',
                2500000,
                3.5,
                28.4,
                '2026-07-20',
                'Kandang B - Blok 2',
                'Mitra Berkah',
                '010',
                '099',
                '[CONTOH] Catatan ternak',
                'https://drive.google.com/drive/folders/1a2b3c4d5e',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
            'V' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

class AnimalsCurrentBlankSheet implements WithTitle, WithHeadings, FromArray, WithColumnFormatting, ShouldAutoSize
{
    public function title(): string
    {
        return 'ANIMALS_CURRENT';
    }

    public function headings(): array
    {
        return AnimalTemplateSchema::getHeaders();
    }

    public function array(): array
    {
        return [
            [
                '[CONTOH] uuid-sample-1234',
                '036',
                'OLD-036',
                'BETINA',
                'Garut',
                'F1',
                'Putih Kepala Hitam',
                'SEHAT',
                '1',
                '0',
                '2024-05-15',
                '0',
                '2024-06-01',
                'HASIL_TERNAK',
                2500000,
                3.5,
                28.4,
                '2026-07-20',
                'Kandang B - Blok 2',
                'Mitra Berkah',
                '010',
                '099',
                '[CONTOH] Catatan ternak',
                'https://drive.google.com/drive/folders/1a2b3c4d5e',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
            'V' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

class IndukanBlankSheet implements WithTitle, WithHeadings, FromArray, WithColumnFormatting, ShouldAutoSize
{
    public function title(): string
    {
        return 'INDUKAN';
    }

    public function headings(): array
    {
        return [
            'id',
            'tag_id',
            'legacy_tag_id',
            'gender',
            'breed',
            'declared_generation',
            'colors',
            'birth_date',
            'physical_status',
            'is_active',
            'location',
            'partner',
            'notes',
        ];
    }

    public function array(): array
    {
        return [
            [
                '[CONTOH] uuid-indukan-01',
                '099',
                'OLD-099',
                'BETINA',
                'Garut',
                'PUREBRED',
                'Hitam',
                '2023-01-15',
                'SEHAT',
                '1',
                'Kandang A',
                'Mitra Berkah',
                '[CONTOH] Indukan utama',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

class AnakanBlankSheet implements WithTitle, WithHeadings, FromArray, WithColumnFormatting, ShouldAutoSize
{
    public function title(): string
    {
        return 'ANAKAN';
    }

    public function headings(): array
    {
        return [
            'id',
            'tag_id',
            'legacy_tag_id',
            'dam_tag_id',
            'sire_tag_id',
            'gender',
            'breed',
            'declared_generation',
            'birth_date',
            'initial_weight',
            'physical_status',
            'is_active',
            'location',
            'partner',
            'notes',
        ];
    }

    public function array(): array
    {
        return [
            [
                '[CONTOH] uuid-anakan-01',
                '010',
                'OLD-010',
                '099',
                '036',
                'JANTAN',
                'Garut',
                'F1',
                '2025-02-01',
                3.8,
                'SEHAT',
                '1',
                'Kandang B',
                'Mitra Berkah',
                '[CONTOH] Anakan cempe',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

class ReferensiSheet implements WithTitle, FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(
        private array $referenceData = []
    ) {}

    public function title(): string
    {
        return 'REFERENSI';
    }

    public function array(): array
    {
        if (!empty($this->referenceData)) {
            return $this->referenceData;
        }

        return [
            ['BREED / RAS', 'Garut, Dorper, Merino, Texel, Composite, Lokal, Cross'],
            ['STATUS KONDISI FISIK', 'SEHAT, SAKIT, KARANTINA, AFKIR, MATI, TERJUAL'],
            ['GENERASI', 'F1, F2, F3, F4, PUREBRED, CROSS, UNKNOWN'],
            ['JENIS KELAMIN', 'JANTAN, BETINA'],
            ['TIPE PEROLEHAN', 'HASIL_TERNAK, PEMBELIAN, MITRA, HIBAH'],
            ['CATATAN IMPORTANT', 'Tag ID wajib berupa teks dengan angka 0 di depan jika ada (misal 010, 036, 099).'],
        ];
    }

    public function headings(): array
    {
        return ['KATEGORI REFERENSI', 'NILAI STANDAR'];
    }
}