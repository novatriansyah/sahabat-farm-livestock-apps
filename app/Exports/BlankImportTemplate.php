<?php

namespace App\Exports;

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
                    ['1. Sheet ANIMALS_CURRENT:', 'Digunakan untuk import master ternak lengkap (rekomendasi utama)'],
                    ['2. Sheet INDUKAN / ANAKAN:', 'Gunakan untuk klasifikasi laporan atau import parsial'],
                    ['3. Baris Contoh [CONTOH]:', 'Baris yang diawali [CONTOH] adalah contoh data dan WAJIB dihapus sebelum di-upload!'],
                    ['4. Kolom bertanda *:', 'Wajib diisi (misal tag_id*, gender*, birth_date*)'],
                    ['', ''],
                    ['B. FORMAT DATA & ATURAN VALIDASI', ''],
                    ['Tag ID (Ear Tag):', 'Wajib diketik sebagai TEKS agar nomor seperti 036 atau 010 tidak hilang angka nol terdepan'],
                    ['Tanggal (birth_date):', 'Format YYYY-MM-DD (contoh: 2025-11-24)'],
                    ['Gender / Jenis Kelamin:', 'JANTAN atau BETINA'],
                    ['Status Fisik:', 'SEHAT, BUNTING, MENYUSUI, ISOLASI, AFKIR, DEAD'],
                    ['Angka Desimal:', 'Gunakan TITIK sebagai pemisah desimal (contoh: 4.15), BUKAN koma'],
                    ['Harga / Rupiah:', 'Angka bulat tanpa titik/koma (contoh: 5500000)'],
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

class AnimalsCurrentBlankSheet implements WithTitle, WithHeadings, FromArray, WithColumnFormatting, ShouldAutoSize
{
    public function title(): string
    {
        return 'ANIMALS_CURRENT';
    }

    public function headings(): array
    {
        return [
            'id',
            'tag_id*',
            'legacy_tag_id',
            'gender*',
            'breed_name*',
            'generation*',
            'ear_tag_color',
            'necklace_color',
            'physical_status*',
            'is_active',
            'is_for_sale',
            'birth_date*',
            'entry_date',
            'acquisition_type',
            'purchase_price',
            'sale_price',
            'current_weight',
            'current_hpp',
            'location_name',
            'partner_name',
            'sire_tag_id',
            'dam_tag_id',
            'gdrive_folder_url',
            'photo_url',
            'notes',
        ];
    }

    public function array(): array
    {
        return [
            [
                '[CONTOH] uuid-sample-1234',
                '036',
                'LEGACY-036',
                'BETINA',
                'DORPER',
                'F1',
                'KUNING',
                'MERAH',
                'SEHAT',
                '1',
                '0',
                '2024-05-10',
                '2024-06-01',
                'BELI',
                '4500000',
                '0',
                '45.5',
                '4500000',
                'Kandang A',
                'Mitra Utama',
                '010',
                '099',
                'https://drive.google.com/folder/example',
                'https://example.com/photo.jpg',
                '[CONTOH] Hapus baris contoh ini sebelum upload',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
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
            'tag_id*',
            'legacy_tag_id',
            'gender*',
            'breed_name*',
            'generation*',
            'ear_tag_color',
            'birth_date*',
            'physical_status*',
            'is_active',
            'location_name',
            'partner_name',
            'notes',
        ];
    }

    public function array(): array
    {
        return [
            [
                '[CONTOH] uuid-indukan-01',
                '099',
                'LEGACY-099',
                'BETINA',
                'CROSS',
                'LOKAL',
                'HIJAU',
                '2023-01-15',
                'SEHAT',
                '1',
                'Kandang Utama',
                'Mitra A',
                '[CONTOH] Hapus baris contoh ini',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
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
            'tag_id*',
            'legacy_tag_id',
            'dam_tag_id',
            'sire_tag_id',
            'gender*',
            'breed_name*',
            'generation*',
            'birth_date*',
            'birth_weight',
            'physical_status*',
            'is_active',
            'location_name',
            'partner_name',
            'notes',
        ];
    }

    public function array(): array
    {
        return [
            [
                '[CONTOH] uuid-anakan-01',
                '010',
                'LEGACY-010',
                '099',
                '036',
                'JANTAN',
                'DORPER',
                'F1',
                '2025-02-01',
                '3.8',
                'SEHAT',
                '1',
                'Kandang Cempe',
                'Mitra A',
                '[CONTOH] Hapus baris contoh ini',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
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

        // Static fallback reference mapping without DB queries
        return [
            ['BREED / RAS', 'DORPER, MERINO, TEXEL, GARUT, COMPOSITE, LOKAL, CROSS'],
            ['STATUS FISIK', 'SEHAT, BUNTING, MENYUSUI, ISOLASI, AFKIR, DEAD'],
            ['GENERASI', 'LOKAL, F1, F2, F3, FULLBLOOD, CROSS, PURE'],
            ['GENDER', 'JANTAN, BETINA'],
            ['AKUISISI', 'HASIL_TERNAK, BELI'],
            ['CATATAN', 'Lihat master data sistem di web untuk ID lokasi dan mitra.'],
        ];
    }

    public function headings(): array
    {
        return ['KATEGORI REFERENSI', 'NILAI STANDAR'];
    }
}