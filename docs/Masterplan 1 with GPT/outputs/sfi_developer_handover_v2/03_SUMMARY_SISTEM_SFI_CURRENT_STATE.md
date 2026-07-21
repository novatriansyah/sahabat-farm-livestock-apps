# Summary Sistem SFI — Current State untuk Handover

**Tanggal ringkasan:** 21 Juli 2026  
**Status verifikasi:** arsitektur/code di bawah ini `[TERLAPOR]` dari project summary; data workbook yang disebut `[PASTI]`; developer wajib memperbarui file ini dari repository/database aktual.

## Tujuan bisnis

Web SFI ditargetkan menjadi satu-satunya source of truth yang dapat dipakai melalui HP dan laptop untuk recording ternak, breeding, kelahiran, bobot, kesehatan, inventory, pakan/vitamin, HPP, penjualan, pembayaran, laporan, mitra, pengguna, CMS, dan media. Excel tetap digunakan sebagai export/audit/analisis, bukan master paralel.

## Arsitektur terlapor

- Laravel 12.x, PHP 8.2/8.3, MySQL.
- Blade, Tailwind CSS 4, Alpine.js 3, Flowbite 3, Axios, Vite 6.
- Packages penting: Intervention Image Laravel, Maatwebsite Excel, Simple QR Code.
- 28 Eloquent models dengan UUID untuk `Animal`.
- Pola controller/service/action/observer; scheduler pada `routes/console.php`.
- Hosting/deployment: Hostinger; detail versi, cron, queue, storage, backup, dan commit aktif belum terverifikasi.

## Role terlapor

| Role | Kode | Scope utama |
|---|---|---|
| Owner | `PEMILIK` | administrasi penuh, master/settings/CMS/finance |
| Breeder | `PETERNAK` | ternak, breeding, birth/exit, inventory, invoice/HPP |
| Staff | `STAF` | mobile operation: scan, timbang, treatment, movement, usage |
| Partner | `MITRA` | read-only dan terbatas `partner_id` |

Permission aktual seluruh endpoint dan risiko IDOR harus diuji; menu tersembunyi bukan authorization.

## Domain model terlapor

- Livestock: `Animal`, `BreedingEvent`, `MatingColony`, `MatingColonyMember`, `WeightLog`, `TreatmentLog`, ear-tag/ownership logs, exit logs.
- Inventory/finance: `InventoryItem`, `InventoryPurchase`, `InventoryUsageLog`, `HppManualCost`, `Invoice`, `InvoiceItem`.
- Master/workflow/CMS: `FarmSetting`, `MasterSop`, `AnimalTask`, `Article`, breeds, category, location, physical status, disease, partner.

`Animal` terlapor menyimpan tag, gender, breed/generation, location/status, owner/partner, sire/dam, dates, acquisition/purchase/sale, sale flag, active flag, HPP components, ADG, necklace, dan ear-tag color.

## Business logic terlapor

1. **ADG:** `WeightLogObserver` menghitung selisih bobot dibagi hari antar dua log terakhir.
2. **Ear-tag color:** `AnimalObserver` membaca `FarmSetting` dan fallback breed/generation. Summary lama menyebut fallback F3 berbeda dari aturan SFI terbaru sehingga harus diverifikasi.
3. **Birth:** anak mewarisi partner dari dam, breed dari sire/fallback dam, generasi dihitung, dam menjadi menyusui. Koneksi aktual sire dari mating colony ke birth perlu diuji.
4. **Mating:** age, latest weight, postpartum/nifas, dan physical status divalidasi oleh `BreedingService`.
5. **HPP:** feed terlapor dibagi rata per head pada kandang/farm, medicine direct ke ternak, manual cost dibagi seluruh active animals; purchase price terpisah. Klaim ini wajib diuji pada action/controller aktual.
6. **Sale:** terlapor `final_hpp` disalin saat exit dan profit = sale − purchase − final HPP.
7. **Dashboard fallback:** jika transaksi real tidak ada, sistem menampilkan estimasi monthly cost; label estimate vs actual harus jelas.

## Scheduler terlapor

- 00:00 daily HPP untuk hari sebelumnya.
- 01:00 `animal:auto-status` untuk weaning/dam reversion/colony separation.
- 02:00 notification sync untuk stock, weaning, vaccine, mating alert.
- Monthly cleanup old photos/logs.

Timezone, idempotency, locking, retry, failure alert, dan data-loss risk cleanup belum terverifikasi.

## Frontend/public terlapor

- Public catalog `/katalog`, filter/search dan WhatsApp inquiry.
- CMS `/admin/site-content`, about page, blog/article dengan Quill.
- Central WhatsApp setting.
- Belum semua business setting/backend constant tersedia di frontend.

## Data workbook yang pasti

- 166 record: 64 `BELI` dan 102 `HASIL_TERNAK`.
- 165 ternak hidup; `B43` mati.
- Ownership: SFI 98; FAHRI 18; OKI 7; LETA 11; AGENG 10; VINA 22.
- Gender: 110 betina dan 56 jantan.
- Generasi: F1 74; F2 46; CROSS 29; F3 15; PURE 2.
- 11 temporary tags; 57 rows mempunyai catatan koreksi/asumsi.
- Tidak ada link Google Drive pada master/template yang diperiksa.
- Template import 12 field tidak membawa pedigree, event, death, history, quality metadata, atau media.

## Risiko/koreksi utama

- Formula usia/status `ANAKAN` workbook memakai referensi tanggal acuan yang salah; nilai turunannya bukan source of truth.
- Klaim interval kelahiran 133 hari/2,74 siklus per tahun tidak layak dijadikan KPI tanpa koreksi event conflict dan cohort denominator.
- Age category harus dipisahkan dari reproductive status, health status, dan inventory status.
- Generation tidak boleh ditebak saat sire tidak diketahui.
- Filter `partner_id` saja tidak membuktikan HPP adil; perlu economic bearer, recipient, cost type, basis, eligibility, period, posting, dan audit ledger.
- Business-safe settings boleh ke frontend; secret, raw SQL/code, dan infra control tetap terlindungi.

## Update wajib oleh developer

Ganti seluruh label `[TERLAPOR]` menjadi `[PASTI]`, `[BERBEDA]`, atau `[TIDAK ADA]` setelah menautkan file/path/symbol/query/test. Tambahkan commit, migration state, full module/route/table map, deployment topology, current issue, release status, dan link artefak handover.

