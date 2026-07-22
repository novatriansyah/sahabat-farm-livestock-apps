<?php

namespace App\Schemas;

use PhpOffice\PhpSpreadsheet\Cell\DataType;

class AnimalTemplateSchema
{
    public const SCHEMA_VERSION = '1.1.0';
    public const PRIMARY_SHEET_NAME = 'DATA_TERNAK';
    public const CANONICAL_GDRIVE_FIELD = 'gdrive_folder_url';

    /**
     * Complete column definitions for animal import/export.
     * Order, header name, data type, required flag, enum options, and descriptions.
     */
    public static function getColumns(): array
    {
        return [
            'id' => [
                'header'       => 'id',
                'label'        => 'UUID Ternak',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'ID UUID unik ternak di sistem (Kosongkan jika ternak baru)',
                'example'      => '9a8b7c6d-5e4f-3a2b-1c0d-9e8f7a6b5c4d',
            ],
            'tag_id' => [
                'header'       => 'tag_id',
                'label'        => 'Ear Tag / ID Ternak*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'system_only'  => false,
                'description'  => 'Nomor Ear Tag utama (Teks, mendukung angka seperti 010, 036, 099)',
                'example'      => '036',
            ],
            'legacy_tag_id' => [
                'header'       => 'legacy_tag_id',
                'label'        => 'Tag Lama / Kode Fisik',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Kode ear tag lama atau identitas fisik sekunder',
                'example'      => 'OLD-036',
            ],
            'gender' => [
                'header'       => 'gender',
                'label'        => 'Jenis Kelamin*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'enums'        => ['JANTAN', 'BETINA'],
                'system_only'  => false,
                'description'  => 'Jenis kelamin ternak (JANTAN atau BETINA)',
                'example'      => 'BETINA',
            ],
            'breed' => [
                'header'       => 'breed',
                'label'        => 'Ras / Rumpun*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'system_only'  => false,
                'description'  => 'Nama ras/rumpun ternak (contoh: Dorper, Garut, Cross)',
                'example'      => 'Garut',
            ],
            'declared_generation' => [
                'header'       => 'declared_generation',
                'label'        => 'Generasi (F1/F2/F3/F4/PUREBRED)',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'enums'        => ['F1', 'F2', 'F3', 'F4', 'PUREBRED', 'CROSS', 'UNKNOWN'],
                'system_only'  => false,
                'description'  => 'Tingkat kemurnian / generasi ternak',
                'example'      => 'F1',
            ],
            'colors' => [
                'header'       => 'colors',
                'label'        => 'Warna / Ciri Fisik',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Warna bulu atau deskripsi fisik spesifik',
                'example'      => 'Putih Kepala Hitam',
            ],
            'physical_status' => [
                'header'       => 'physical_status',
                'label'        => 'Status Kondisi Fisik*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'enums'        => ['SEHAT', 'SAKIT', 'KARANTINA', 'AFKIR', 'MATI', 'TERJUAL'],
                'system_only'  => false,
                'description'  => 'Status fisik ternak saat ini',
                'example'      => 'SEHAT',
            ],
            'is_active' => [
                'header'       => 'is_active',
                'label'        => 'Aktif (1/0)*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'enums'        => ['1', '0', 'YA', 'TIDAK', 'TRUE', 'FALSE'],
                'system_only'  => false,
                'description'  => 'Status keaktifan populasi (1 = Aktif, 0 = Nonaktif/Mati/Terjual)',
                'example'      => '1',
            ],
            'is_for_sale' => [
                'header'       => 'is_for_sale',
                'label'        => 'Siap Jual (1/0)',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'enums'        => ['1', '0', 'YA', 'TIDAK', 'TRUE', 'FALSE'],
                'system_only'  => false,
                'description'  => 'Apakah ternak masuk ke katalog penjualan (1 = Ya, 0 = Tidak)',
                'example'      => '0',
            ],
            'birth_date' => [
                'header'       => 'birth_date',
                'label'        => 'Tanggal Lahir (YYYY-MM-DD)',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Tanggal lahir ternak format YYYY-MM-DD',
                'example'      => '2024-05-15',
            ],
            'birth_date_estimated' => [
                'header'       => 'birth_date_estimated',
                'label'        => 'Tanggal Lahir Taksiran (1/0)',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => '1 jika tanggal lahir merupakan hasil estimasi/perkiraan',
                'example'      => '0',
            ],
            'entry_date' => [
                'header'       => 'entry_date',
                'label'        => 'Tanggal Masuk Kandang (YYYY-MM-DD)',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Tanggal ternak masuk ke peternakan/kandang',
                'example'      => '2024-06-01',
            ],
            'acquisition_type' => [
                'header'       => 'acquisition_type',
                'label'        => 'Tipe Perolehan',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'enums'        => ['HASIL_TERNAK', 'PEMBELIAN', 'MITRA', 'HIBAH'],
                'system_only'  => false,
                'description'  => 'Asal perolehan ternak (HASIL_TERNAK, PEMBELIAN, MITRA)',
                'example'      => 'HASIL_TERNAK',
            ],
            'acquisition_cost' => [
                'header'       => 'acquisition_cost',
                'label'        => 'Biaya Perolehan / Harga Beli',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Nominal pembelian awal ternak (Rp)',
                'example'      => 2500000,
            ],
            'initial_weight' => [
                'header'       => 'initial_weight',
                'label'        => 'Bobot Awal / Lahir (kg)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Bobot saat lahir atau saat pertama masuk (kg)',
                'example'      => 3.5,
            ],
            'current_weight' => [
                'header'       => 'current_weight',
                'label'        => 'Bobot Terakhir (kg)',
                'type'         => DataType::TYPE_NUMERIC,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Bobot timbangan terbaru (kg)',
                'example'      => 28.4,
            ],
            'last_weighed_at' => [
                'header'       => 'last_weighed_at',
                'label'        => 'Tanggal Penimbangan Terakhir',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Tanggal penimbangan terakhir YYYY-MM-DD',
                'example'      => '2026-07-20',
            ],
            'location' => [
                'header'       => 'location',
                'label'        => 'Lokasi / Kandang*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'system_only'  => false,
                'description'  => 'Nama kandang atau blok lokasi ternak',
                'example'      => 'Kandang B - Blok 2',
            ],
            'partner' => [
                'header'       => 'partner',
                'label'        => 'Nama Mitra / Pemilik*',
                'type'         => DataType::TYPE_STRING,
                'required'     => true,
                'system_only'  => false,
                'description'  => 'Nama mitra pemilik ternak atau SFI Internal',
                'example'      => 'Mitra Berkah',
            ],
            'sire_tag_id' => [
                'header'       => 'sire_tag_id',
                'label'        => 'Tag Pejantan / Sire',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Tag_id dari pejantan/bapak',
                'example'      => '010',
            ],
            'dam_tag_id' => [
                'header'       => 'dam_tag_id',
                'label'        => 'Tag Induk / Dam',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Tag_id dari induk/ibu',
                'example'      => '099',
            ],
            'notes' => [
                'header'       => 'notes',
                'label'        => 'Catatan Tambahan',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'Catatan khusus mengenai kondisi atau riwayat ternak',
                'example'      => 'Kondisi sangat bagus, bulu tebal',
            ],
            'gdrive_folder_url' => [
                'header'       => 'gdrive_folder_url',
                'label'        => 'Link Folder Google Drive',
                'type'         => DataType::TYPE_STRING,
                'required'     => false,
                'system_only'  => false,
                'description'  => 'URL folder Google Drive berisi dokumentasi foto/video/dokumen',
                'example'      => 'https://drive.google.com/drive/folders/1a2b3c4d5e',
            ],
        ];
    }

    /**
     * Returns array of exact header strings for primary sheet DATA_TERNAK.
     */
    public static function getHeaders(): array
    {
        return array_keys(static::getColumns());
    }

    /**
     * Normalize ear tag strings ensuring leading zeros are preserved.
     */
    public static function normalizeTag(mixed $val): ?string
    {
        if ($val === null) {
            return null;
        }

        $str = trim((string) $val);

        // Strip Excel formula wrapping if present
        if (str_starts_with($str, '="') && str_ends_with($str, '"')) {
            $str = substr($str, 2, -1);
        } elseif (str_starts_with($str, "='") && str_ends_with($str, "'")) {
            $str = substr($str, 2, -1);
        }

        $str = trim($str);
        return $str !== '' ? $str : null;
    }

    /**
     * Map GDrive field from record array or model, checking fallback aliases.
     */
    public static function extractGDriveUrl(array|object $item): ?string
    {
        if (is_array($item)) {
            return $item['gdrive_folder_url'] 
                ?? $item['google_drive_link'] 
                ?? $item['gdrive_link'] 
                ?? null;
        }

        return $item->gdrive_folder_url 
            ?? $item->google_drive_link 
            ?? $item->gdrive_link 
            ?? null;
    }
}
