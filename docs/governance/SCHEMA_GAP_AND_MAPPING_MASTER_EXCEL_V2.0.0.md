# SCHEMA GAP AND MAPPING MATRIX — MASTER EXCEL V2.0.0

**Schema Version**: `2.0.0`  
**Reference File**: `SFI_MASTER_TERNAK_v3.xlsx`  
**Unified Class**: `App\Schemas\AnimalTemplateSchema`

---

## Field-Level Mapping Table (35 Columns)

| Index | Column Header | Label / Excel Name | Data Type | Required | Enum Options / Allowed Values | Importer & Reconciliation Mapping |
|---|---|---|---|---|---|---|
| 1 | `id` | UUID Ternak | String (UUID) | Optional | 36-char UUID | `animals.id` |
| 2 | `tag_id` | Ear Tag / ID Ternak* | String | **Required** | Text (e.g. `010`, `036`, `099`, `B43`) | `animals.tag_id` |
| 3 | `legacy_tag_id` | Tag Lama / Kode Fisik | String | Optional | Text | `animals.legacy_tag_id` |
| 4 | `gender` | Jenis Kelamin* | String | **Required** | `JANTAN`, `BETINA` | `animals.gender` |
| 5 | `breed` | Ras / Rumpun* | String | **Required** | Garut, Dorper, Merino, Cross | `master_breeds.name` |
| 6 | `declared_generation` | Generasi | String | Optional | F1, F2, F3, F4, PUREBRED, CROSS | `animals.generation` |
| 7 | `ear_tag_color` | Warna Ear Tag | String | Optional | Kuning, Merah, Hijau | `animals.ear_tag_color` |
| 8 | `necklace_color` | Warna Kalung | String | Optional | Hitam, Merah, Biru | `animals.necklace_color` |
| 9 | `physical_characteristics` | Ciri Fisik | String | Optional | Free text description | Virtual physical notes |
| 10 | `physical_status` | Kondisi Kesehatan* | String | **Required** | SEHAT, SAKIT, KARANTINA, AFKIR, DEAD, TERJUAL | `master_phys_statuses.name` |
| 11 | `current_inventory_status` | Status Inventaris | String | Optional | TERSEDIA, TERPESAN, KELUAR, MUTASI | Derived location status |
| 12 | `is_active` | Status Aktif (1/0) | Numeric | Optional | 1, 0 | `animals.is_active` |
| 13 | `is_for_sale` | Dijual (1/0) | Numeric | Optional | 1, 0 | `animals.is_for_sale` |
| 14 | `birth_date` | Tanggal Lahir | Date String | Optional | YYYY-MM-DD | `animals.birth_date` |
| 15 | `birth_date_estimated` | Est. Lahir (1/0) | Numeric | Optional | 1, 0 | `animals.birth_date_estimated` |
| 16 | `birth_weight` | BB Lahir (kg) | Numeric | Optional | Decimal (e.g. 3.6) | `weight_logs.weight_kg` at birth |
| 17 | `entry_date` | Tanggal Masuk | Date String | Optional | YYYY-MM-DD | `animals.entry_date` |
| 18 | `acquisition_type` | Cara Perolehan* | String | **Required** | HASIL_TERNAK, BELI, MITRA | `animals.acquisition_type` |
| 19 | `acquisition_cost` | Biaya Perolehan (Rp) | Numeric | Optional | Decimal (e.g. 3500000) | `animals.purchase_price` |
| 20 | `valuation` | Estimasi Nilai Pasar (Rp) | Numeric | Optional | Decimal | Virtual asset valuation |
| 21 | `current_weight` | Bobot Saat Ini (kg) | Numeric | Optional | Decimal (e.g. 45.5) | Latest `weight_logs.weight_kg` |
| 22 | `weight_type` | Metode Bobot | String | Optional | TIMBANGAN_AKTUAL, BB_ASUMSI | Weight log method |
| 23 | `weight_estimated` | Est. Bobot (1/0) | Numeric | Optional | 1, 0 | Weight log flag |
| 24 | `litter_size` | Jumlah Kembar | String | Optional | TUNGGAL, KEMBAR_2, KEMBAR_3, KEMBAR_4 | Birth event litter size |
| 25 | `total_cycles` | Total Siklus Melahirkan | Numeric | Optional | Integer | Total breeding cycles count |
| 26 | `location` | Kandang / Lokasi* | String | **Required** | Nama Kandang | `master_locations.name` |
| 27 | `partner` | Mitra / Kepemilikan | String | Optional | Nama Mitra / SFI Internal | `master_partners.name` |
| 28 | `sire_tag_id` | Tag Pejantan / Bapak | String | Optional | Tag ID Pejantan | `animals.sire_id` via tag |
| 29 | `dam_tag_id` | Tag Indukan / Ibu | String | Optional | Tag ID Indukan | `animals.dam_id` via tag |
| 30 | `birth_event_ref` | Ref Perkawinan | String | Optional | Event ID | `breeding_events.id` |
| 31 | `data_source` | Sumber Data | String | Optional | Pencatatan Kandang / File Mitra | Source metadata |
| 32 | `confidence` | Keyakinan Data | String | Optional | TINGGI, SEDANG, RENDAH | Confidence metadata |
| 33 | `in_partner_file` | Di File Mitra (1/0) | Numeric | Optional | 1, 0 | Reconciliation flag |
| 34 | `notes` | Catatan Tambahan | String | Optional | Text | Notes |
| 35 | `gdrive_folder_url` | Link Google Drive | String | Optional | URL | `animals.google_drive_link` (Canonical) |
