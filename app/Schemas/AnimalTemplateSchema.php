<?php

namespace App\Schemas;

use PhpOffice\PhpSpreadsheet\Cell\DataType;

class AnimalTemplateSchema
{
    public const SCHEMA_VERSION = '2.0.0';
    public const PRIMARY_SHEET_NAME = 'DATA_TERNAK';
    public const CANONICAL_GDRIVE_FIELD = 'gdrive_folder_url';

    /**
     * Complete column definitions for animal import/export v2.0.0.
     */
    public static function getColumns(): array
    {
        return [
            'id' => [
                'header'       => 'id',
                'label'        => 'UUID Ternak',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'ID UUID unik ternak di sistem',
                'example'      => '9a8b7c6d-5e4f-3a2b-1c0d-9e8f7a6b5c4d',
            ],
            'tag_id' => [
                'header'       => 'tag_id',
                'label'        => 'Ear Tag / ID Ternak*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'description'  => 'Nomor Ear Tag utama (Teks, e.g., 010, 036, 099)',
                'example'      => '036',
            ],
            'legacy_tag_id' => [
                'header'       => 'legacy_tag_id',
                'label'        => 'Tag Lama / Kode Fisik',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Kode ear tag lama atau identitas fisik sekunder',
                'example'      => 'OLD-036',
            ],
            'gender' => [
                'header'       => 'gender',
                'label'        => 'Jenis Kelamin*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'enums'        => ['JANTAN', 'BETINA'],
                'description'  => 'Jenis kelamin (JANTAN / BETINA)',
                'example'      => 'BETINA',
            ],
            'breed' => [
                'header'       => 'breed',
                'label'        => 'Ras / Rumpun*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'description'  => 'Nama ras ternak (e.g. Garut, Dorper, Cross)',
                'example'      => 'Garut',
            ],
            'declared_generation' => [
                'header'       => 'declared_generation',
                'label'        => 'Generasi',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'enums'        => ['F1', 'F2', 'F3', 'F4', 'PUREBRED', 'CROSS'],
                'description'  => 'Tingkat generasi persilangan',
                'example'      => 'F1',
            ],
            'ear_tag_color' => [
                'header'       => 'ear_tag_color',
                'label'        => 'Warna Ear Tag',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Warna fisik ear tag',
                'example'      => 'Kuning',
            ],
            'necklace_color' => [
                'header'       => 'necklace_color',
                'label'        => 'Warna Kalung',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Warna fisik kalung leher',
                'example'      => 'Merah',
            ],
            'physical_characteristics' => [
                'header'       => 'physical_characteristics',
                'label'        => 'Ciri Fisik',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Ciri-ciri fisik spesifik / bercak warna',
                'example'      => 'Hitam Kepala Putih',
            ],
            'physical_status' => [
                'header'       => 'physical_status',
                'label'        => 'Kondisi Kesehatan*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'enums'        => ['SEHAT', 'SAKIT', 'KARANTINA', 'AFKIR', 'DEAD', 'TERJUAL'],
                'description'  => 'Status fisik/kesehatan ternak saat ini',
                'example'      => 'SEHAT',
            ],
            'current_inventory_status' => [
                'header'       => 'current_inventory_status',
                'label'        => 'Status Inventaris',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'enums'        => ['TERSEDIA', 'TERPESAN', 'KELUAR', 'MUTASI'],
                'description'  => 'Status keberadaan ternak di lokasi inventaris',
                'example'      => 'TERSEDIA',
            ],
            'is_active' => [
                'header'       => 'is_active',
                'label'        => 'Status Aktif (1/0)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => '1 = Aktif di kandang, 0 = Nonaktif (Mati/Keluar)',
                'example'      => '1',
            ],
            'is_for_sale' => [
                'header'       => 'is_for_sale',
                'label'        => 'Dijual (1/0)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => '1 = Masuk katalog jual, 0 = Tidak dijual',
                'example'      => '0',
            ],
            'birth_date' => [
                'header'       => 'birth_date',
                'label'        => 'Tanggal Lahir',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Format YYYY-MM-DD',
                'example'      => '2025-01-15',
            ],
            'birth_date_estimated' => [
                'header'       => 'birth_date_estimated',
                'label'        => 'Est. Lahir (1/0)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => '1 jika tanggal lahir estimasi, 0 jika pasti',
                'example'      => '0',
            ],
            'birth_weight' => [
                'header'       => 'birth_weight',
                'label'        => 'BB Lahir (kg)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => 'Bobot awal saat lahir dalam kg',
                'example'      => '3.6',
            ],
            'entry_date' => [
                'header'       => 'entry_date',
                'label'        => 'Tanggal Masuk',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Format YYYY-MM-DD',
                'example'      => '2025-02-01',
            ],
            'acquisition_type' => [
                'header'       => 'acquisition_type',
                'label'        => 'Cara Perolehan*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'enums'        => ['HASIL_TERNAK', 'BELI', 'MITRA'],
                'description'  => 'Metode ternak masuk ke farm',
                'example'      => 'HASIL_TERNAK',
            ],
            'acquisition_cost' => [
                'header'       => 'acquisition_cost',
                'label'        => 'Biaya Perolehan (Rp)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => 'Harga beli / biaya awal perolehan',
                'example'      => '3500000',
            ],
            'valuation' => [
                'header'       => 'valuation',
                'label'        => 'Estimasi Nilai Pasar (Rp)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => 'Nilai estimasi aset pasar terpisah dari HPP',
                'example'      => '4200000',
            ],
            'current_weight' => [
                'header'       => 'current_weight',
                'label'        => 'Bobot Saat Ini (kg)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => 'Penimbangan bobot badan terkini dalam kg',
                'example'      => '45.5',
            ],
            'weight_type' => [
                'header'       => 'weight_type',
                'label'        => 'Metode Bobot',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'enums'        => ['TIMBANGAN_AKTUAL', 'BB_ASUMSI'],
                'description'  => 'Timbangan aktual / asumsi pita ukur',
                'example'      => 'TIMBANGAN_AKTUAL',
            ],
            'weight_estimated' => [
                'header'       => 'weight_estimated',
                'label'        => 'Est. Bobot (1/0)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => '1 jika bobot estimasi, 0 jika riil',
                'example'      => '0',
            ],
            'litter_size' => [
                'header'       => 'litter_size',
                'label'        => 'Jumlah Kembar',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'enums'        => ['TUNGGAL', 'KEMBAR_2', 'KEMBAR_3', 'KEMBAR_4'],
                'description'  => 'Tipe kelahiran (Tunggal / Kembar)',
                'example'      => 'KEMBAR_2',
            ],
            'total_cycles' => [
                'header'       => 'total_cycles',
                'label'        => 'Total Siklus Melahirkan',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => 'Jumlah siklus beranak indukan',
                'example'      => '2',
            ],
            'location' => [
                'header'       => 'location',
                'label'        => 'Kandang / Lokasi*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'description'  => 'Nama Kandang lokasi ternak berada',
                'example'      => 'Kandang A - Utama',
            ],
            'partner' => [
                'header'       => 'partner',
                'label'        => 'Mitra / Kepemilikan',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Nama Mitra pemilik ternak (Kosong jika milik SFI Internal)',
                'example'      => 'Mitra Berkah',
            ],
            'sire_tag_id' => [
                'header'       => 'sire_tag_id',
                'label'        => 'Tag Pejantan / Bapak',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Ear Tag pejantan pemacek',
                'example'      => 'SIRE-010',
            ],
            'dam_tag_id' => [
                'header'       => 'dam_tag_id',
                'label'        => 'Tag Indukan / Ibu',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Ear Tag indukan melahirkan',
                'example'      => 'DAM-001',
            ],
            'birth_event_ref' => [
                'header'       => 'birth_event_ref',
                'label'        => 'Ref Perkawinan / Kelahiran',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'ID referensi event perkawinan',
                'example'      => 'EVT-2026-001',
            ],
            'data_source' => [
                'header'       => 'data_source',
                'label'        => 'Sumber Data',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Catatan Peternakan / File Mitra',
                'example'      => 'Pencatatan Kandang Utama',
            ],
            'confidence' => [
                'header'       => 'confidence',
                'label'        => 'Keyakinan Data',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'enums'        => ['TINGGI', 'SEDANG', 'RENDAH'],
                'description'  => 'Tingkat keyakinan asal-usul ternak',
                'example'      => 'TINGGI',
            ],
            'in_partner_file' => [
                'header'       => 'in_partner_file',
                'label'        => 'Di File Mitra (1/0)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'description'  => '1 jika tercatat di spreadsheet mitra',
                'example'      => '1',
            ],
            'notes' => [
                'header'       => 'notes',
                'label'        => 'Catatan Tambahan',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'Keterangan atau catatan medis/rekam sejarah',
                'example'      => 'Indukan utama baseline',
            ],
            'gdrive_folder_url' => [
                'header'       => 'gdrive_folder_url',
                'label'        => 'Link Google Drive',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'description'  => 'URL Folder Media Google Drive (Canonical Field)',
                'example'      => 'https://drive.google.com/folder/dam_001',
            ],
        ];
    }

    public static function getHeaders(): array
    {
        return array_values(array_map(fn($col) => $col['header'], static::getColumns()));
    }

    public static function getLabels(): array
    {
        return array_values(array_map(fn($col) => $col['label'], static::getColumns()));
    }

    public static function extractGDriveUrl($animal): ?string
    {
        return $animal->google_drive_link ?? $animal->gdrive_folder_url ?? null;
    }
}
