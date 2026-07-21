# Audit `IMPORT_TERNAK_SFI_siap_upload.xlsx`

## Kesimpulan

[PASTI] File tepat memuat 166 tag yang sama dengan master dan tidak memiliki tag duplikat, tetapi belum aman untuk migrasi/reset karena kehilangan data penting.

## Struktur file

- 1 sheet: `Template Import Ternak`.
- 167 baris termasuk header; 166 record.
- Used range `A1:AG167` karena reference list untuk data validation berada di Z:AG.
- 12 upload fields: `tag_id`, `gender`, `breed_name`, `birth_date`, `initial_weight_kg`, `physical_status`, `acquisition_type`, `purchase_price`, `location_name`, `partner_name`, `generation`, `necklace_color`.
- Kolom N–O berisi catatan/asosiasi dan diberi instruksi untuk dihapus.
- Tidak ada field/link Google Drive, parent, event, health/death, active flag, atau history.

## Rekonsiliasi populasi

| Dimensi | Hasil |
|---|---:|
| Record | 166 |
| Tag match master | 166/166 |
| Duplicate tag | 0 |
| BELI | 64 |
| HASIL_TERNAK | 102 |
| Betina | 110 |
| Jantan | 56 |
| Temporary tag | 11 |
| Rows dengan catatan koreksi | 57 |
| Rows dengan “anakan terkait” | 6 |

## Data yang hilang bila langsung upload

1. Dam/sire dan 102 relasi anak ke induk.
2. Birth event/litter dan data pejantan/confidence.
3. `B43` mati; tanpa death/exit/is_active akan berpotensi menjadi aktif.
4. Ear-tag history, ownership/location/status history.
5. Source/confidence/estimated flags dan data-quality notes.
6. Ciri fisik, kondisi, media/Google Drive, dan evidence.
7. HPP/valuation semantics dan historical ledger.

## Asumsi yang tidak boleh dihapus

| Issue | Jumlah |
|---|---:|
| Tanggal lahir asumsi | 39 |
| Nomor eartag belum final | 11 |
| Bobot asumsi | 12 |
| Gender asumsi | 5 |
| Jenis asumsi | 1 |

Jumlah kategori dapat overlap pada satu record. Catatan harus diubah menjadi structured issue, bukan dibuang.

## Risiko field semantic

- `purchase_price`: master menggunakan `HARGA` untuk nilai aset; 102 ternak hasil kelahiran bukan transaksi beli. Pisahkan `acquisition_cost`, `accumulated_hpp`, `estimated_asset_value`, `list_price`, dan `sale_price`.
- `initial_weight_kg`: indukan memakai `BB (KG)`, sedangkan anakan memakai `BB LAHIR`; gabungan ini kehilangan tipe dan tanggal ukur. Gunakan weight event.
- `physical_status`: template menggabungkan kategori umur dengan status fisik dan tidak membawa bunting/menyusui/karantina secara historis.
- `generation`: tanpa sire dan rule version, nilai hanya deklaratif dan tidak dapat diaudit.

## Pre-reset gate

Semua kondisi berikut harus `LULUS`:

- Full production export + history + stable IDs.
- Restore test di staging dan clean-room build.
- Canonical import v2 dry-run 166 record.
- `B43` tetap mati/nonaktif.
- 102 offspring terhubung ke valid birth event/dam atau masuk issue queue, bukan hilang diam-diam.
- 11 temporary tag tetap terlacak pada UUID yang sama.
- 57 issue termigrasi.
- Financial/valuation reconciliation disetujui.
- No orphan, duplicate, silent overwrite, atau secret exposure.
- Rollback test lulus dan pemilik memberikan token cutover.

