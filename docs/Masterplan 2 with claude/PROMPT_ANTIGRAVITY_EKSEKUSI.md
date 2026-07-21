# PROMPT EKSEKUSI — GEMINI ANTIGRAVITY
## Perbaikan Sistem SFI (Laravel 12 · MySQL · Hostinger)

**Cara pakai:** salin seluruh dokumen ini ke Antigravity, lampirkan file yang disebut
di Bagian 0, lalu jalankan. Agent akan bekerja bertahap dengan checkpoint.

---

# ⚠️ ATURAN PALING PENTING — BACA SEBELUM MULAI

Pekerjaan ini **TIDAK BOLEH** dikerjakan sekaligus dalam satu sesi. Cakupannya setara
6 sprint. Bila dipaksakan, hasilnya akan setengah jadi dan tidak terintegrasi.

**Karena itu:**

1. Kerjakan **SATU TAHAP** per sesi. Selesaikan, verifikasi, hasilkan laporan, baru berhenti.
2. Setelah tiap tahap, **WAJIB** hasilkan 3 file di folder `/docs/sfi-progress/`:
   - `TAHAP-{n}-LAPORAN.md` — apa yang dikerjakan, hasil verifikasi, masalah ditemukan
   - `TAHAP-{n}-SOURCECODE.md` — seluruh kode yang dibuat/diubah (lengkap, bukan ringkasan)
   - `TAHAP-{n}-FEEDBACK.md` — temuan, keputusan teknis, rekomendasi tahap berikutnya
3. **JANGAN** lanjut ke tahap berikutnya tanpa perintah eksplisit dari pemilik.
4. Bila konteks hampir habis, **hentikan pekerjaan**, tulis laporan sampai titik itu,
   dan catat persis di mana harus dilanjutkan.

---

# BAGIAN 0 — LAMPIRAN YANG HARUS ADA

Pastikan file ini tersedia sebelum mulai:

| File | Fungsi |
| :--- | :--- |
| `MASTER_PROMPT_PERBAIKAN_SISTEM.md` | Spesifikasi teknis lengkap 6 tahap |
| `REKOMENDASI_SISTEM_SFI.md` | Analisa mendalam + alasan tiap perbaikan |
| `2026_07_20_000001_sfi_critical_fixes.php` | Migrasi siap pakai |
| `IMPORT_01_INDUKAN.csv` | 64 indukan format database |
| `IMPORT_02_ANAKAN.csv` | 102 anakan format database |
| `IMPORT_03_RIWAYAT_EARTAG.csv` | 46 riwayat penggantian nomor |
| `IMPORT_04_KONFLIK_DATA.csv` | 37 item perlu konfirmasi |

Bila ada yang hilang, **berhenti dan minta file tersebut** — jangan menebak isinya.

---

# BAGIAN 1 — LANGKAH PERTAMA WAJIB: AUDIT SISTEM

**Sebelum mengubah apa pun**, lakukan audit dan hasilkan
`/docs/sfi-progress/TAHAP-0-AUDIT-SISTEM.md`.

Ini penting karena pemilik tidak selalu bisa mengakses developer. Semua informasi
harus terekam dalam satu file agar bisa dibaca ulang nanti.

## 1.1 Yang harus diekstrak

### A. Struktur database

```bash
# Jalankan dan salin hasilnya ke laporan
php artisan db:show
php artisan db:table animals
php artisan db:table breeding_events
php artisan db:table mating_colonies
php artisan db:table weight_logs
php artisan db:table treatment_logs
php artisan db:table inventory_items
php artisan db:table inventory_purchases
php artisan db:table inventory_usage_logs
php artisan db:table hpp_manual_costs
php artisan db:table invoices
php artisan db:table farm_settings
php artisan db:table users
```

Untuk setiap tabel catat: nama kolom, tipe, nullable, default, index, foreign key.

### B. Isi tabel master & pengaturan (WAJIB lengkap, jangan diringkas)

```sql
SELECT * FROM farm_settings ORDER BY `group`, `key`;
SELECT * FROM master_breeds;
SELECT * FROM master_phys_status;
SELECT * FROM master_locations;
SELECT * FROM master_partners;
SELECT * FROM master_categories;
SELECT * FROM master_diseases;
SELECT * FROM master_sops;
SELECT id, name, email, role FROM users;
```

### C. Kondisi data produksi

```sql
SELECT COUNT(*) AS total,
       SUM(is_active=1) AS aktif,
       SUM(acquisition_type='HASIL_TERNAK') AS hasil_ternak,
       SUM(acquisition_type='BELI') AS beli,
       SUM(sire_id IS NOT NULL) AS punya_sire,
       SUM(dam_id IS NOT NULL) AS punya_dam,
       SUM(partner_id IS NOT NULL) AS milik_mitra
FROM animals;

SELECT partner_id, COUNT(*) FROM animals WHERE is_active=1 GROUP BY partner_id;
SELECT generation, COUNT(*) FROM animals GROUP BY generation;
SELECT tag_id, COUNT(*) c FROM animals GROUP BY tag_id HAVING c>1;
SELECT COUNT(*) FROM weight_logs;
SELECT COUNT(*) FROM breeding_events;
SELECT COUNT(*) FROM mating_colonies;
SELECT COUNT(*) FROM invoices;
SELECT MIN(created_at), MAX(created_at) FROM animals;
```

### D. Source code kunci (SALIN UTUH, bukan ringkasan)

File-file berikut **wajib disalin lengkap** ke `TAHAP-0-SOURCECODE.md`:

```
app/Models/Animal.php
app/Models/BreedingEvent.php
app/Models/MatingColony.php
app/Observers/AnimalObserver.php
app/Observers/WeightLogObserver.php
app/Observers/ExitLogObserver.php
app/Services/BreedingService.php
app/Services/DashboardService.php
app/Actions/Finance/CalculateDailyHpp.php
app/Http/Controllers/BirthController.php
app/Http/Controllers/ExitController.php
app/Http/Controllers/HppManualCostController.php
app/Http/Controllers/OperatorController.php
app/Imports/AnimalsImport.php
routes/web.php
routes/console.php
database/migrations/*_create_animals_table.php
config/app.php  (bagian provider & timezone saja)
```

Bila file tidak ada, catat "TIDAK DITEMUKAN" — jangan dilewati diam-diam.

### E. Verifikasi 5 dugaan kritis

Uji satu per satu dan laporkan **terbukti / tidak terbukti** beserta buktinya:

| # | Dugaan | Cara memverifikasi |
| :---: | :--- | :--- |
| 1 | `CalculateDailyHpp` tidak memfilter `partner_id` | Baca kode, cari `where('partner_id'` |
| 2 | Alokasi pakan memakai headcount, bukan bobot | Baca rumus pembagi |
| 3 | `sire_id` kosong 100% | `SELECT COUNT(*) FROM animals WHERE sire_id IS NOT NULL` |
| 4 | Biaya manual tidak pro-rata hari aktif | Baca `HppManualCostController` |
| 5 | Generasi memakai `max(sire,dam)+1` | Baca `BirthController` |

### F. Inventarisasi parameter hardcoded

Cari di seluruh `app/` dan `resources/views/` nilai yang seharusnya configurable:

```bash
grep -rn "60\|150\|30\|8\|3\b" app/Services app/Actions app/Observers | grep -v "//"
grep -rn "Kuning\|Orange\|Hijau\|Biru" app/ resources/views/
grep -rn "'PEMILIK'\|'PETERNAK'\|'STAF'\|'MITRA'" app/
```

Laporkan dalam tabel: file, baris, nilai, seharusnya jadi parameter apa.

## 1.2 Format laporan audit

```markdown
# TAHAP 0 — AUDIT SISTEM SFI
Tanggal: {tanggal}
Dikerjakan oleh: Gemini Antigravity

## A. Ringkasan Temuan
{5 temuan terpenting, masing-masing 1 kalimat}

## B. Struktur Database
{tabel per tabel: kolom, tipe, index, FK}

## C. Isi Master Data & Pengaturan
{dump lengkap farm_settings dan seluruh master_*}

## D. Kondisi Data Produksi
{hasil query bagian C, dalam tabel}

## E. Verifikasi 5 Dugaan Kritis
| # | Dugaan | Status | Bukti |
|---|--------|--------|-------|

## F. Parameter Hardcoded
| File | Baris | Nilai | Usulan parameter |
|------|-------|-------|------------------|

## G. Risiko yang Ditemukan
{hal berbahaya yang ditemukan saat audit, tidak ada di daftar dugaan}

## H. Rekomendasi Urutan Pengerjaan
{bila temuan audit mengubah urutan di MASTER_PROMPT, katakan dan jelaskan}
```

**Setelah laporan audit selesai: BERHENTI.** Tunggu perintah pemilik untuk lanjut ke Tahap 1.

---

# BAGIAN 2 — TAHAP PENGERJAAN

Kerjakan sesuai `MASTER_PROMPT_PERBAIKAN_SISTEM.md`, satu tahap per sesi:

| Tahap | Isi | Prasyarat |
| :---: | :--- | :--- |
| **0** | Audit sistem (Bagian 1 di atas) | — |
| **1** | Export & backup data | Tahap 0 selesai |
| **2** | Perbaikan logika kritis (HPP, generasi, sire) | Tahap 1 **terverifikasi berfungsi** |
| **3** | Pengaturan ke frontend | Tahap 2 selesai |
| **4** | Modul penjualan, pakan, laporan | Tahap 3 selesai |
| **5** | Reset & import data bersih | Tahap 1–4 selesai + backup teruji |
| **6** | Fitur lanjutan | Tahap 5 selesai |

## Aturan tiap tahap

**Sebelum mulai:**
- Baca laporan tahap sebelumnya
- Konfirmasi prasyarat terpenuhi
- Buat branch git: `git checkout -b sfi-tahap-{n}`

**Selama mengerjakan:**
- Ikuti pola arsitektur existing (Service/Observer/Action)
- Semua parameter baru masuk tabel pengaturan, **jangan hardcode**
- Tulis migration dengan `down()` yang benar-benar berfungsi
- Commit per fitur, bukan sekali di akhir

**Setelah selesai:**
- Jalankan `php artisan migrate --pretend` untuk verifikasi
- Uji dengan data nyata, bukan dummy
- Hasilkan 3 file laporan (lihat Bagian 3)
- **BERHENTI** — jangan lanjut tanpa perintah

---

# BAGIAN 3 — OUTPUT WAJIB TIAP TAHAP

## 3.1 `TAHAP-{n}-LAPORAN.md`

```markdown
# TAHAP {n} — {judul}
Tanggal: · Durasi: · Status: SELESAI / SEBAGIAN / TERHENTI

## Yang Dikerjakan
| # | Item | Status | File Terpengaruh |

## Hasil Verifikasi
{output perintah uji, screenshot query, bukti fitur berfungsi}

## Masalah yang Ditemukan
| Masalah | Dampak | Penanganan |

## Yang BELUM Selesai
{bila terhenti: persis di mana, apa yang sudah/belum, cara melanjutkan}

## Perintah untuk Deploy
{langkah persis yang harus dijalankan pemilik di Hostinger}

## Cara Rollback
{perintah persis bila terjadi masalah}
```

## 3.2 `TAHAP-{n}-SOURCECODE.md`

**Seluruh kode yang dibuat atau diubah, LENGKAP.** Format:

````markdown
# TAHAP {n} — SOURCE CODE

## File Baru

### `app/Services/GenerationResolverService.php`
```php
{seluruh isi file, jangan dipotong}
```

## File Diubah

### `app/Http/Controllers/BirthController.php`
**Perubahan:** {ringkas apa yang diubah}

**Sebelum:**
```php
{potongan kode lama}
```

**Sesudah:**
```php
{potongan kode baru}
```

## Migration
```php
{seluruh isi migration}
```

## Query SQL yang Dijalankan
```sql
{semua DDL/DML yang dieksekusi}
```
````

> **Kenapa ini penting:** pemilik tidak selalu bisa mengakses developer atau repository.
> File ini menjadi arsip agar AI lain (Claude/ChatGPT) bisa membaca dan melanjutkan
> pekerjaan tanpa perlu akses source code langsung.

## 3.3 `TAHAP-{n}-FEEDBACK.md`

```markdown
# TAHAP {n} — FEEDBACK & REKOMENDASI

## Keputusan Teknis yang Diambil
| Keputusan | Alasan | Alternatif yang Ditolak |

## Temuan Tak Terduga
{hal yang ditemukan saat mengerjakan, tidak ada di spesifikasi}

## Utang Teknis
{yang sengaja disederhanakan, kenapa, kapan harus dibereskan}

## Rekomendasi Tahap Berikutnya
| Prioritas | Rekomendasi | Alasan |

## Pertanyaan untuk Pemilik
{hal yang butuh keputusan bisnis, bukan teknis}

## Dampak ke Data
{apakah ada data yang berubah, berapa record, bisa di-rollback atau tidak}
```

---

# BAGIAN 4 — FILE RANGKUMAN AKHIR

Setelah **semua tahap** selesai, hasilkan satu file gabungan:
`/docs/sfi-progress/SISTEM-SFI-DOKUMENTASI-LENGKAP.md`

Isinya:

```markdown
# DOKUMENTASI SISTEM SFI — SETELAH PERBAIKAN

## 1. Ringkasan Perubahan
{tabel: tahap, apa yang berubah, dampak}

## 2. Arsitektur Setelah Perbaikan
{struktur folder, model, service, observer, job}

## 3. Skema Database Final
{seluruh tabel + kolom + relasi}

## 4. Seluruh Source Code
{gabungan semua TAHAP-{n}-SOURCECODE.md}

## 5. Parameter yang Bisa Diatur dari Frontend
{daftar lengkap + lokasi menu}

## 6. Cara Menjalankan & Merawat
{deploy, backup, restore, monitoring}

## 7. Utang Teknis & Rekomendasi Lanjutan
{gabungan semua feedback}

## 8. Kondisi Data
{jumlah record, kualitas data, yang masih perlu dilengkapi}
```

File ini menjadi **satu-satunya dokumen** yang perlu dibawa pemilik bila berpindah
platform AI atau developer.

---

# BAGIAN 5 — LARANGAN

❌ Jangan reset/hapus data sebelum export Tahap 1 terverifikasi berfungsi
❌ Jangan hitung ulang generasi 102 anakan sebelum `sire_id` terisi
❌ Jangan ubah rumus HPP di produksi tanpa persetujuan pemilik (mitra sudah melihat angkanya)
❌ Jangan hardcode parameter baru
❌ Jangan hapus data permanen — pakai soft delete
❌ Jangan proses export/import besar secara sinkron di Hostinger — pakai queue
❌ Jangan kerjakan lebih dari satu tahap per sesi
❌ Jangan meringkas source code di laporan — salin utuh
❌ Jangan lanjut ke tahap berikutnya tanpa perintah eksplisit

---

# BAGIAN 6 — BILA KONTEKS HAMPIR HABIS

Bila merasa konteks menipis di tengah pengerjaan:

1. **Hentikan** menulis kode baru
2. Commit apa yang sudah selesai
3. Tulis `TAHAP-{n}-LAPORAN.md` dengan status **TERHENTI**
4. Catat persis: file mana sudah selesai, mana setengah jadi, apa langkah berikutnya
5. Tulis `TAHAP-{n}-SOURCECODE.md` untuk kode yang sudah dibuat
6. Beri tahu pemilik: "Konteks hampir habis. Sudah selesai sampai {X}.
   Untuk melanjutkan, mulai sesi baru dan berikan file laporan ini."

**Lebih baik berhenti dengan rapi daripada menghasilkan kode setengah jadi yang tidak jalan.**

---

# MULAI SEKARANG

Tugas pertama: **TAHAP 0 — AUDIT SISTEM** (Bagian 1).

Jangan mengubah kode apa pun. Hanya baca, ekstrak, verifikasi, dan hasilkan laporan.

Setelah laporan audit selesai, berhenti dan tunggu perintah pemilik.
