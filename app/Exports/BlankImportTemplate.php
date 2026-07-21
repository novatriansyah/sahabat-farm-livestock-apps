<?php

namespace App\Exports;

use App\Models\MasterBreed;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use App\Models\FarmSetting;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BlankImportTemplate implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'PETUNJUK'  => new PetunjukSheet(),
            'INDUKAN'   => new IndukanBlankSheet(),
            'ANAKAN'    => new AnakanBlankSheet(),
            'REFERENSI' => new ReferensiSheet(),
        ];
    }
}

class PetunjukSheet implements WithTitle, WithEvents
{
    public function title(): string { return 'PETUNJUK'; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'PETUNJUK PENGISIAN TEMPLATE IMPORT TERNAK SFI');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $instructions = [
                    ['', ''],
                    ['A. CARA PENGISIAN', ''],
                    ['1. Pilih sheet yang sesuai:', 'INDUKAN untuk ternak indukan betina dewasa, ANAKAN untuk anakan/cempe'],
                    ['2. Isi data per baris — satu baris = satu ekor ternak', ''],
                    ['3. Kolom bertanda * adalah WAJIB diisi', ''],
                    ['4. Kolom tanpa tanda * boleh dikosongkan jika data belum tersedia', ''],
                    ['', ''],
                    ['B. FORMAT DATA', ''],
                    ['Tanggal lahir:', 'YYYY-MM-DD (contoh: 2025-11-24) — tulis sebagai TEKS, bukan format tanggal Excel'],
                    ['Angka desimal:', 'Gunakan TITIK sebagai pemisah desimal (contoh: 3.45), BUKAN koma'],
                    ['Nomor Eartag:', 'Tulis sebagai TEKS agar nomor seperti 036 tidak berubah menjadi 36'],
                    ['Bobot lahir:', 'Dalam kilogram (contoh: 4.15)'],
                    ['Harga:', 'Dalam Rupiah tanpa titik (contoh: 5500000)'],
                    ['', ''],
                    ['C. KOLOM WAJIB (*)', ''],
                    ['tag_id', 'Nomor eartag/pengenal ternak'],
                    ['gender', 'JANTAN atau BETINA'],
                    ['birth_date', 'Tanggal lahir format YYYY-MM-DD'],
                    ['breed_name', 'Nama breed/ras (lihat sheet REFERENSI)'],
                    ['generation', 'Generasi (LOKAL, F1, F2, F3, FULLBLOOD, CROSS)'],
                    ['physical_status', 'Status fisik (lihat sheet REFERENSI)'],
                    ['', ''],
                    ['D. KOLOM REFERENSI', ''],
                    ['Gunakan sheet REFERENSI untuk melihat nilai yang tersedia untuk:', ''],
                    ['- Breed / Ras', '- Lokasi Kandang', '- Mitra/Partner', '- Status Fisik', '- Pengaturan Farm', ''],
                    ['', ''],
                    ['E. LINK GOOGLE DRIVE', ''],
                    ['Kolom gdrive_folder_url:', 'Isi dengan URL folder Google Drive dokumentasi ternak'],
                    ['Format:', 'https://drive.google.com/drive/folders/...'],
                    ['', ''],
                    ['F. SETELAH MENGISI', ''],
                    ['1. Simpan file dalam format .xlsx', ''],
                    ['2. Upload melalui menu: Export → Upload Hasil Edit', ''],
                    ['3. Sistem akan menampilkan perbandingan (diff) sebelum diterapkan', ''],
                ];

                $row = 3;
                foreach ($instructions as $line) {
                    $sheet->setCellValue("A{$row}", $line[0]);
                    $sheet->setCellValue("B{$row}", $line[1]);
                    if (!empty($line[0]) && str_starts_with($line[0], 'A.') || str_starts_with($line[0], 'B.') || str_starts_with($line[0], 'C.') || str_starts_with($line[0], 'D.') || str_starts_with($line[0], 'E.') || str_starts_with($line[0], 'F.')) {
                        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    }
                    $row++;
                }

                $sheet->getColumnDimension('A')->setWidth(40);
                $sheet->getColumnDimension('B')->setWidth(60);
            },
        ];
    }
}

class IndukanBlankSheet implements WithTitle, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    public function title(): string { return 'INDUKAN'; }

    public function headings(): array
    {
        return [
            'id', 'tag_id*', 'legacy_tag_number', 'old_tag_id',
            'dam_tag_id', 'sire_tag_id', 'sire_confidence',
            'gender*', 'breed_name*', 'generation*', 'generation_confidence',
            'ear_tag_color', 'birth_date*', 'birth_weight', 'is_birth_weight_estimated',
            'litter_size', 'current_weight', 'adg', 'weaning_weight', 'weaning_date',
            'physical_status*', 'is_active', 'necklace_color',
            'location_name', 'partner_name',
            'current_hpp', 'purchase_price', 'sale_price',
            'gdrive_folder_url', 'photo_url', 'video_url',
            'confidence_level', 'data_source', 'notes', 'needs_review',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,  // tag_id
            'D' => NumberFormat::FORMAT_TEXT,  // old_tag_id
            'E' => NumberFormat::FORMAT_TEXT,  // dam_tag_id
            'F' => NumberFormat::FORMAT_TEXT,  // sire_tag_id
            'M' => NumberFormat::FORMAT_TEXT,  // birth_date
            'T' => NumberFormat::FORMAT_TEXT,  // weaning_date
            'U' => NumberFormat::FORMAT_TEXT,  // physical_status
            'AD' => NumberFormat::FORMAT_TEXT, // gdrive_folder_url
            'AE' => NumberFormat::FORMAT_TEXT, // photo_url
            'AF' => NumberFormat::FORMAT_TEXT, // video_url
        ];
    }
}

class AnakanBlankSheet implements WithTitle, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    public function title(): string { return 'ANAKAN'; }

    public function headings(): array
    {
        return [
            'id', 'tag_id*', 'legacy_tag_number', 'old_tag_id',
            'dam_tag_id', 'sire_tag_id', 'sire_confidence',
            'gender*', 'breed_name*', 'generation*', 'generation_confidence',
            'ear_tag_color', 'birth_date*', 'birth_weight', 'is_birth_weight_estimated',
            'litter_size', 'current_weight', 'adg', 'weaning_weight', 'weaning_date',
            'physical_status*', 'is_active', 'necklace_color',
            'location_name', 'partner_name',
            'current_hpp', 'purchase_price', 'sale_price',
            'gdrive_folder_url', 'photo_url', 'video_url',
            'confidence_level', 'data_source', 'notes', 'needs_review',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'T' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
            'AD' => NumberFormat::FORMAT_TEXT,
            'AE' => NumberFormat::FORMAT_TEXT,
            'AF' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

class ReferensiSheet implements WithTitle, FromArray, WithHeadings, ShouldAutoSize
{
    public function title(): string { return 'REFERENSI'; }

    public function array(): array
    {
        $data = [];

        // Breeds
        $data[] = ['BREED / RAS', '', '', ''];
        $data[] = ['ID', 'NAMA', '', ''];
        foreach (MasterBreed::all() as $b) {
            $data[] = [$b->id, $b->name, '', ''];
        }
        $data[] = ['', '', '', ''];

        // Locations
        $data[] = ['LOKASI KANDANG', '', '', ''];
        $data[] = ['ID', 'NAMA', '', ''];
        foreach (MasterLocation::all() as $l) {
            $data[] = [$l->id, $l->name, '', ''];
        }
        $data[] = ['', '', '', ''];

        // Partners
        $data[] = ['MITRA / PARTNER', '', '', ''];
        $data[] = ['ID', 'NAMA', '', ''];
        foreach (MasterPartner::all() as $p) {
            $data[] = [$p->id, $p->name, '', ''];
        }
        $data[] = ['', '', '', ''];

        // Phys Statuses
        $data[] = ['STATUS FISIK', '', '', ''];
        $data[] = ['ID', 'NAMA', '', ''];
        foreach (MasterPhysStatus::all() as $s) {
            $data[] = [$s->id, $s->name, '', ''];
        }
        $data[] = ['', '', '', ''];

        // Farm Settings
        $data[] = ['PENGATURAN FARM', '', '', ''];
        $data[] = ['KEY', 'VALUE', '', ''];
        foreach (FarmSetting::all() as $fs) {
            $data[] = [$fs->key, $fs->value, '', ''];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['REFERENSI', '', '', ''];
    }
}