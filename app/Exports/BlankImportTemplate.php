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

    public static function getExampleRow(): array
    {
        return [
            '[CONTOH] 9a8b7c6d-5e4f-3a2b-1c0d-9e8f7a6b5c4d', // 1. id
            '036',                                         // 2. tag_id
            'OLD-036',                                     // 3. legacy_tag_id
            'BETINA',                                      // 4. gender
            'Garut',                                       // 5. breed
            'PUREBRED',                                    // 6. declared_generation
            'Kuning',                                      // 7. ear_tag_color
            'Hitam',                                       // 8. necklace_color
            'Sehat proporsional',                          // 9. physical_characteristics
            'SEHAT',                                       // 10. physical_status
            'TERSEDIA',                                    // 11. current_inventory_status
            '1',                                           // 12. is_active
            '0',                                           // 13. is_for_sale
            '2025-01-15',                                  // 14. birth_date
            '0',                                           // 15. birth_date_estimated
            '3.6',                                         // 16. birth_weight
            '2025-02-01',                                  // 17. entry_date
            'BELI',                                        // 18. acquisition_type
            '3500000',                                     // 19. acquisition_cost
            '4200000',                                     // 20. valuation
            '45.5',                                        // 21. current_weight
            'TIMBANGAN_AKTUAL',                             // 22. weight_type
            '0',                                           // 23. weight_estimated
            'TUNGGAL',                                     // 24. litter_size
            '1',                                           // 25. total_cycles
            'Kandang A - Utama',                           // 26. location
            'Mitra VINA',                                  // 27. partner
            'SIRE-001',                                    // 28. sire_tag_id
            'DAM-001',                                     // 29. dam_tag_id
            'EVT-2025-001',                                // 30. birth_event_ref
            'Pencatatan Kandang',                          // 31. data_source
            'TINGGI',                                      // 32. confidence
            '1',                                           // 33. in_partner_file
            'Contoh ternak indukan',                       // 34. notes
            'https://drive.google.com/folder/036',          // 35. gdrive_folder_url
        ];
    }

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
                $sheet->setCellValue('A1', 'PETUNJUK PENGISIAN TEMPLATE CANONICAL IMPORT SFI (v2.0.0)');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $instructions = [
                    ['', ''],
                    ['A. CARA PENGISIAN TEMPLATE', ''],
                    ['1. Sheet DATA_TERNAK / ANIMALS_CURRENT:', 'Digunakan untuk import master ternak lengkap 35 kolom (rekomendasi utama)'],
                    ['2. Sheet INDUKAN / ANAKAN:', 'Gunakan untuk klasifikasi laporan atau import parsial'],
                    ['3. Baris Contoh [CONTOH]:', 'Baris yang diawali [CONTOH] adalah contoh data dan WAJIB dihapus sebelum di-upload!'],
                    ['4. Kolom bertanda *:', 'Wajib diisi (misal tag_id*, gender*, breed*, location*, physical_status*, acquisition_type*)'],
                    ['', ''],
                    ['B. FORMAT DATA & ATURAN VALIDASI', ''],
                    ['Tag ID (Ear Tag):', 'Wajib diketik sebagai TEKS agar nomor seperti 036 atau 010 tidak hilang angka nol terdepan'],
                    ['Tanggal (birth_date):', 'Format YYYY-MM-DD (contoh: 2025-01-15)'],
                    ['Gender / Jenis Kelamin:', 'JANTAN atau BETINA'],
                    ['Status Fisik:', 'SEHAT, SAKIT, KARANTINA, AFKIR, DEAD, TERJUAL'],
                    ['Angka Desimal:', 'Gunakan TITIK sebagai pemisah desimal (contoh: 4.5), BUKAN koma'],
                    ['Harga / Rupiah:', 'Angka bulat tanpa titik/koma (contoh: 3500000)'],
                    ['', ''],
                    ['C. REFERENSI ENUM & SHIFTING', ''],
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
        return [BlankImportTemplate::getExampleRow()];
    }

    public function columnFormats(): array
    {
        return [
            'A'  => NumberFormat::FORMAT_TEXT,
            'B'  => NumberFormat::FORMAT_TEXT,
            'C'  => NumberFormat::FORMAT_TEXT,
            'AB' => NumberFormat::FORMAT_TEXT,
            'AC' => NumberFormat::FORMAT_TEXT,
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
        return [BlankImportTemplate::getExampleRow()];
    }

    public function columnFormats(): array
    {
        return [
            'A'  => NumberFormat::FORMAT_TEXT,
            'B'  => NumberFormat::FORMAT_TEXT,
            'C'  => NumberFormat::FORMAT_TEXT,
            'AB' => NumberFormat::FORMAT_TEXT,
            'AC' => NumberFormat::FORMAT_TEXT,
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
        return AnimalTemplateSchema::getHeaders();
    }

    public function array(): array
    {
        $row = BlankImportTemplate::getExampleRow();
        $row[3] = 'BETINA';
        return [$row];
    }

    public function columnFormats(): array
    {
        return [
            'A'  => NumberFormat::FORMAT_TEXT,
            'B'  => NumberFormat::FORMAT_TEXT,
            'C'  => NumberFormat::FORMAT_TEXT,
            'AB' => NumberFormat::FORMAT_TEXT,
            'AC' => NumberFormat::FORMAT_TEXT,
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
        return AnimalTemplateSchema::getHeaders();
    }

    public function array(): array
    {
        $row = BlankImportTemplate::getExampleRow();
        $row[17] = 'HASIL_TERNAK';
        return [$row];
    }

    public function columnFormats(): array
    {
        return [
            'A'  => NumberFormat::FORMAT_TEXT,
            'B'  => NumberFormat::FORMAT_TEXT,
            'C'  => NumberFormat::FORMAT_TEXT,
            'AB' => NumberFormat::FORMAT_TEXT,
            'AC' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

class ReferensiSheet implements WithTitle, WithHeadings, FromArray, ShouldAutoSize
{
    public function __construct(private array $data = []) {}

    public function title(): string
    {
        return 'REFERENSI';
    }

    public function headings(): array
    {
        return ['KATEGORI_REFERENSI', 'KODE_NAMA_VAL', 'KETERANGAN'];
    }

    public function array(): array
    {
        return [
            ['JENIS_KELIMAN', 'JANTAN', 'Ternak Pejantan'],
            ['JENIS_KELIMAN', 'BETINA', 'Ternak Betina / Indukan / Dara'],
            ['KONDISI_FISIK', 'SEHAT', 'Ternak sehat di kandang'],
            ['KONDISI_FISIK', 'SAKIT', 'Ternak sakit / perawatan'],
            ['KONDISI_FISIK', 'KARANTINA', 'Karantina isolasi'],
            ['KONDISI_FISIK', 'AFKIR', 'Ternak afkir / culling'],
            ['KONDISI_FISIK', 'DEAD', 'Ternak mati / nonaktif'],
            ['KONDISI_FISIK', 'TERJUAL', 'Ternak sudah dijual'],
            ['CARA_PEROLEHAN', 'BELI', 'Pembelian dari luar'],
            ['CARA_PEROLEHAN', 'HASIL_TERNAK', 'Kelahiran internal SFI'],
            ['CARA_PEROLEHAN', 'MITRA', 'Ternak bawaan mitra'],
            ['GENERASI', 'PUREBRED', 'Fullblood / Murni'],
            ['GENERASI', 'F1 DORPER', 'Silangan Generasi F1'],
            ['GENERASI', 'F2 DORPER', 'Silangan Generasi F2'],
            ['GENERASI', 'F3 DORPER', 'Silangan Generasi F3'],
            ['WARNA_EAR_TAG', 'Hijau', 'Lokal / Komposit'],
            ['WARNA_EAR_TAG', 'Kuning', 'F1 Dorper'],
            ['WARNA_EAR_TAG', 'Orange', 'F2 Dorper'],
            ['WARNA_EAR_TAG', 'Biru', 'F3 Dorper'],
        ];
    }
}