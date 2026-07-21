# SUMMARY PROJECT — SISTEM RECORDING TERNAK SFI
## Dokumen Pemindahan Project ke Claude Cowork / Platform AI Lain

**Versi:** 1.0 · 20 Juli 2026
**Pemilik:** Rizki — Sahabat Farm Indonesia (SFI)
**Tujuan dokumen:** membawa seluruh konteks project ini ke sesi AI baru tanpa
kehilangan riwayat keputusan dan temuan.

---

# 1. GAMBARAN USAHA

**Sahabat Farm Indonesia (SFI)** adalah peternakan **pembibitan (breeding)** domba
Dorper dengan model kemitraan inti-plasma. Bukan penggemukan — fokusnya menghasilkan
bibit unggul melalui program grading-up bertahap menuju Fullblood Dorper.

**Model bisnis:**
- Mitra investor membeli indukan (Rp 5,5–6,5 juta per ekor, paket 5 ekor)
- SFI mengelola seluruh operasional (kandang, pakan, kesehatan, perkawinan)
- Bagi hasil **30%** dari penjualan anakan untuk mitra
- Manfaat qurban tahunan per mitra

**Skala saat ini:** 166 ekor (64 indukan + 102 anakan), 5 mitra aktif.

**Lima mitra & penanda warna kalung:**

| Mitra | Warna Kalung | Indukan | Anakan |
| :--- | :--- | ---: | ---: |
| FAHRI | Hijau | 5 | 13 |
| OKI | Coklat | 5 | 2 |
| LETA | Kuning | 5 | 6 |
| AGENG | Merah | 5 | 5 |
| VINA | Pink | 5 | 17 |
| **SFI (inti)** | Biru / Tanpa Kalung | **39** | **59** |

---

# 2. ATURAN BISNIS YANG SUDAH DISEPAKATI

Ini hasil klarifikasi berulang dengan pemilik. **Jangan diubah tanpa konfirmasi.**

## 2.1 Identitas ternak
- Nomor eartag **bisa sama antar mitra** — pembedanya adalah **warna eartag**
- Suffix (`-OKI`, `-INDUK`) **hanya dipakai bila terjadi konflik nomor**
- Bila tidak konflik: pakai nomor apa adanya (`36`, `157`, `174`)
- Format nomor ganda `B29-235` berarti: **B29 = nomor lama, 235 = nomor baru**
- Nomor awal **selalu** nomor lama

## 2.2 Warna eartag per generasi

| Generasi | Warna |
| :--- | :--- |
| Lokal / Komposit / Garut / Teksel / Merino | **Hijau** |
| F1 Dorper | **Kuning** |
| F2 Dorper | **Orange** |
| F3 Dorper | **Biru** |
| Fullblood Dorper | **Original** |

> Catatan: sistem web saat ini masih memakai "F3 = Kuning Orange" — **salah**, harus Biru.

## 2.3 Aturan generasi anakan (ATURAN TERBARU — mengubah asumsi lama)

| Pejantan | Indukan | Hasil |
| :--- | :--- | :--- |
| **Fullblood** | Lokal/Garut/Teksel/Merino | F1 DORPER |
| **Fullblood** | F1 | F2 DORPER |
| **Fullblood** | F2 | F3 DORPER |
| **Fullblood** | F3 → F5 | F4 → F6 DORPER |
| **Fullblood** | Fullblood | FULLBLOOD DORPER |
| **BUKAN Fullblood** | apapun | **CROSS DORPER** |

## 2.4 Kategori umur ternak (ATURAN TERBARU)

| Umur | Betina | Jantan |
| :--- | :--- | :--- |
| 1–3 bulan | Cempe | Cempe |
| 3–5 bulan | Cempe Sapih | Cempe Sapih |
| 5–8 bulan | **Dara** | **Bakalan** |
| > 8 bulan | **Betina Indukan** | **Jantan** |

> **Dampak terukur:** 63 dari 102 ekor (62%) berubah kategori dibanding aturan lama.

## 2.5 Penamaan breed (sudah diperbarui di sistem)
- Lokal → **"Lokal/Komposit"**
- Texel → **"Cross Texel"**
- Merino → **"Cross Merino"**
- Seri F: **huruf besar semua** — F1 DORPER, F2 DORPER, ... F6 DORPER
- Kalung kosong → **"Tanpa Kalung"**

## 2.6 Asumsi harga & bobot (bila data tidak ada)

| Item | Nilai |
| :--- | :--- |
| Indukan SFI lokal | Rp 4.500.000 |
| Indukan SFI F1 | Rp 5.500.000 |
| Indukan SFI F2 | Rp 6.500.000 |
| Indukan mitra FAHRI/VINA | Rp 5.500.000 (27,5jt ÷ 5) |
| Indukan mitra LETA/AGENG/OKI | Rp 6.500.000 (32,5jt ÷ 5) |
| Cempe 1–3 bulan | Rp 750.000 |
| Cempe > 3 bulan | Rp 1.250.000 |
| BB indukan tanpa data | 35 kg |
| BB anakan lahir tunggal | 4,5 kg |
| BB anakan kembar 2 | 3,5 kg |
| BB anakan kembar 3 | 2,5 kg |
| Tgl lahir indukan tanpa data | 19-09-2024 (samakan VINA) |

---

# 3. RIWAYAT PEKERJAAN YANG SUDAH SELESAI

## Tahap 1 — Rekonsiliasi catatan tulisan tangan
- Transkrip 15 halaman scan catatan kelahiran (Sep 2025 – Jul 2026)
- Transkrip 2 halaman pemetaan penggantian eartag (46 entri)
- **Validasi silang: 46 dari 46 tag lama cocok** dengan catatan kelahiran → bukti transkripsi akurat
- Rekonsiliasi dengan 5 file Excel mitra

## Tahap 2 — Pembangunan database master
Menghasilkan `SFI_MASTER_TERNAK_v3.xlsx` dengan 10 sheet:
PETUNJUK · LAPORAN ANALISA (5 grafik) · REKAP · INDUKAN · ANAKAN ·
HISTORI EARTAG · FOLDER GDRIVE · PERLU KONFIRMASI · KELAHIRAN AWAL · REFERENSI

## Tahap 3 — File import ke sistem web
`IMPORT_TERNAK_SFI_siap_upload.xlsx` — 166 baris, 12 kolom terisi penuh,
nol duplikat, nol nilai tidak valid terhadap dropdown sistem.

## Tahap 4 — Analisa sistem web
Menghasilkan `REKOMENDASI_SISTEM_SFI.md` (1.196 baris) berisi 14 gap
(8 temuan baru + 6 dari gap analysis pemilik).

## Tahap 5 — Master prompt perbaikan
`MASTER_PROMPT_PERBAIKAN_SISTEM.md` — instruksi 6 tahap untuk AI pembangun sistem.

---

# 4. TEMUAN PENTING YANG HARUS DIINGAT

## 4.1 Tiga cacat kritis di sistem web (sedang aktif merusak data)

**A. HPP mencampur biaya mitra dengan SFI**
Rumus alokasi biaya tidak memfilter `partner_id`. Ternak SFI dan mitra sekandang,
sehingga biaya tercampur → bagi hasil 30% salah → **risiko sengketa hukum**.

**B. Alokasi pakan dibagi rata per ekor**
Cempe 3 kg dan bakalan 30 kg dibebani biaya sama, padahal konsumsi beda 10× lipat.
Standar industri: alokasi berbasis **bobot metabolis (BB^0,75)**.

**C. `sire_id` kosong 100%**
102 dari 102 anakan punya data induk, **nol** punya data pejantan.
Akibatnya anti-inbreeding mustahil. **46 anakan betina** akan mencapai usia kawin
dalam 8–12 bulan. Solusinya murah: tarik dari `MatingColony` pada masa konsepsi
(±150 hari sebelum lahir).

## 4.2 Bug yang ditemukan
- Scheduler memakai **60 hari** untuk masa kebuntingan — seharusnya **147–152 hari**
- Sistem memakai "F3 = Kuning Orange" — seharusnya **Biru**
- Template import/export **tidak punya kolom `gdrive_folder_url`**, padahal sistem
  punya field input link Google Drive

## 4.3 Kendala operasional utama
**Pemilik tidak bisa mengekspor data ternak dari sistem.** Akibatnya data existing
tidak bisa dikurasi, dan reset sistem = kehilangan data. **Export harus dikerjakan
lebih dulu sebelum apa pun.**

## 4.4 KPI SFI (dihitung dari data nyata, belum ada di sistem)

| KPI | SFI | Benchmark Industri | Status |
| :--- | :---: | :---: | :---: |
| Lambing rate | **159%** | 150–175% | ✅ Baik |
| Fertility rate | **92%** | >90% | ✅ Baik |
| Pre-weaning mortality | **1,0%** | <10% | ✅ Sangat baik |
| Lambing interval | **133 hari** | 243 hari | ✅ Sangat baik |
| Prolificacy | **1,44** | 1,6–1,8 | ⚠️ Di bawah target |
| Kg anak disapih/induk | tidak terukur | 40–50 kg | ❌ Data tidak ada |
| Ewe efficiency | tidak terukur | ≥0,8 | ❌ Data tidak ada |

> Angka mortalitas 1% dan interval 133 hari sangat kompetitif — layak jadi
> materi pemasaran utama untuk menarik mitra baru.

## 4.5 Data yang masih perlu dilengkapi pemilik

| Item | Jumlah | Keterangan |
| :--- | ---: | :--- |
| Indukan belum bernomor | 6 | Diberi ID sementara `BELUM ADA NO-01` s/d `-06` |
| Anakan belum bernomor | 5 | Dilacak via nomor anakan tetangga + laporan grup WA |
| BB anakan asumsi | 12 | Ditandai kuning di file master |
| Tgl lahir indukan asumsi | 39 | Disamakan dengan VINA (19-09-2024) |
| Jenis kelamin asumsi | 3 | Diisi default Betina, perlu cek fisik |
| Data pejantan (`sire_id`) | 102 | **Belum ada sama sekali** |

---

# 5. SISTEM WEB SAAT INI

**URL:** www.sahabatfarmindonesia.com
**Stack:** Laravel 12 · PHP 8.2/8.3 · MySQL · Blade + Tailwind v4 + Alpine.js · Vite 6
**Hosting:** Hostinger (shared hosting)
**Dibangun dengan:** bantuan AI

**Yang sudah ada & berfungsi baik:**
- 28 model Eloquent, UUID sebagai primary key
- Observer pattern (ADG otomatis, warna eartag, pewarisan data kelahiran)
- Scheduled job harian untuk HPP
- RBAC 4 role: PEMILIK / PETERNAK / STAF / MITRA
- Katalog publik + CMS + QR code per ekor
- Weighted average unit cost untuk inventory
- Audit log untuk eartag & kepemilikan

**Yang belum ada:** export data, pengaturan parameter di frontend, RBAC dinamis,
modul penjualan lengkap, KPI reproduksi, anti-inbreeding, bagi hasil otomatis,
mode offline, notifikasi WhatsApp.

---

# 6. DAFTAR DOKUMEN & FILE PROJECT

## 6.1 File Excel (data)

| File | Isi | Status |
| :--- | :--- | :--- |
| `SFI_MASTER_TERNAK_v3.xlsx` | Database master 10 sheet, 166 ekor + laporan analisa | **Utama** |
| `IMPORT_TERNAK_SFI_siap_upload.xlsx` | 166 baris siap upload ke sistem web | **Utama** |
| `RECORDING_MITRA_SFI_2025_-_FAHRI_UPDATED.xlsx` | File mitra FAHRI | Sumber |
| `RECORDING_MITRA_SFI_2025_-_OKI_UPDATED.xlsx` | File mitra OKI | Sumber |
| `RECORDING_MITRA_SFI_2025_-_LETA_UPDATED.xlsx` | File mitra LETA | Sumber |
| `RECORDING_MITRA_SFI_2025_-_AGENG_UPDATED.xlsx` | File mitra AGENG | Sumber |
| `RECORDING_MITRA_SFI_2025_-_VINA_UPDATED.xlsx` | File mitra VINA | Sumber |
| `SFI_ANALITIK_KELOMPOK.xlsx` | Analitik gabungan 5 mitra | Sumber |
| `RECORDING_MITRA_SFI_TEMPLATE_KOSONG.xlsx` | Template kosong untuk mitra baru | Pendukung |
| `template_ternak_sfi.xlsx` | Template import bawaan sistem web | Referensi |

## 6.2 File CSV (siap impor ke database)

| File | Baris | Isi |
| :--- | ---: | :--- |
| `IMPORT_01_INDUKAN.csv` | 64 | Data indukan siap impor |
| `IMPORT_02_ANAKAN.csv` | 102 | Data anakan siap impor |
| `IMPORT_03_RIWAYAT_EARTAG.csv` | 46 | Riwayat penggantian nomor |
| `IMPORT_04_KONFLIK_DATA.csv` | 37 | Daftar konflik perlu konfirmasi |

## 6.3 Dokumen analisa & instruksi

| File | Isi |
| :--- | :--- |
| `REKOMENDASI_SISTEM_SFI.md` | Analisa lengkap 14 gap + roadmap 6 fase (1.196 baris) |
| `MASTER_PROMPT_PERBAIKAN_SISTEM.md` | **Instruksi untuk AI pembangun sistem** |
| `INSTRUKSI_GAP_ANALYSIS_LANJUTAN.md` | Data tambahan untuk gap analysis putaran-2 |
| `LAPORAN_ANALISA_SFI.md` | Laporan analisa untuk calon mitra & investor |
| `AUDIT_AWAL_DATA_SFI.md` | Audit kondisi data awal sebelum rekonsiliasi |
| `PANDUAN_UPLOAD_SISTEM.md` | Panduan upload ke sistem web |
| `SUMMARY_PROJECT_SFI.md` | **Dokumen ini** |

## 6.4 File teknis

| File | Isi |
| :--- | :--- |
| `2026_07_20_000001_sfi_critical_fixes.php` | Migrasi Laravel siap `php artisan migrate` |
| `BUAT_FOLDER_GDRIVE.gs` | Google Apps Script pembuat 96 folder Drive otomatis |
| `RefreshSFI.bas` | Macro VBA sinkronisasi file Excel mitra |

## 6.5 Scan catatan tulisan tangan
15 file JPEG catatan kelahiran + 2 file pemetaan eartag.
**Sudah ditranskrip seluruhnya** ke CSV — file asli disimpan sebagai arsip verifikasi.

---

# 7. KEPUTUSAN TEKNIS YANG SUDAH DIAMBIL

Agar tidak dibahas ulang di sesi baru:

| Keputusan | Alasan |
| :--- | :--- |
| File master **terpisah** dari file mitra | Agar tidak merusak file yang sudah terkoneksi macro |
| VBA dikirim sebagai `.bas`, bukan `.xlsm` | Lebih aman & portabel; `.xlsm` tidak bisa dibuat dari Linux |
| Folder Drive dibuat via **Apps Script**, bukan API | AI tidak punya akses tulis ke Drive pemilik |
| Excel diproses via **zip-level XML patch** | `openpyxl` merusak chart saat re-save |
| Suffix tag hanya untuk konflik | Permintaan pemilik — nomor apa adanya lebih mudah dibaca |
| Kolom asumsi ditandai **kuning** | Agar pemilik tahu mana yang perlu diverifikasi |
| Export **wajib** sebelum reset sistem | Tanpa export, data existing hilang permanen |

---

# 8. CARA MELANJUTKAN PROJECT INI

## 8.1 Bila melanjutkan di Claude Cowork

Cowork bisa mengakses folder lokal dan Google Drive. Struktur folder yang disarankan:

```
SFI-PROJECT/
├── 01-DATA-MASTER/
│   ├── SFI_MASTER_TERNAK_v3.xlsx
│   └── IMPORT_TERNAK_SFI_siap_upload.xlsx
├── 02-FILE-MITRA/
│   └── RECORDING_MITRA_SFI_2025_-_*.xlsx
├── 03-CSV-IMPORT/
│   └── IMPORT_0*.csv
├── 04-DOKUMEN/
│   ├── MASTER_PROMPT_PERBAIKAN_SISTEM.md
│   ├── REKOMENDASI_SISTEM_SFI.md
│   └── SUMMARY_PROJECT_SFI.md
├── 05-TEKNIS/
│   ├── 2026_07_20_000001_sfi_critical_fixes.php
│   └── BUAT_FOLDER_GDRIVE.gs
└── 06-ARSIP-SCAN/
    └── (15 JPEG catatan + 2 pemetaan)
```

## 8.2 Bila menguji di platform AI lain

**Minimal yang harus dilampirkan:**
1. `SUMMARY_PROJECT_SFI.md` (dokumen ini) — konteks lengkap
2. `SFI_MASTER_TERNAK_v3.xlsx` — data ternak
3. `MASTER_PROMPT_PERBAIKAN_SISTEM.md` — bila membahas sistem web

**Bila membahas perbaikan sistem, tambahkan:**
4. `REKOMENDASI_SISTEM_SFI.md`
5. Project Summary sistem web (dari pemilik)
6. Hasil gap analysis (dari pemilik)

## 8.3 Alur update data ternak baru

```
Kelahiran baru di kandang
    ↓
[Opsi A] Input langsung di sistem web (HP/desktop)  ← utama
[Opsi B] Catat di grup WA + foto, lalu input menyusul
[Opsi C] Update di Cowork → sinkronkan ke sistem
    ↓
Verifikasi mingguan: cocokkan sistem vs catatan WA
    ↓
Bulanan: export dari sistem → arsip di Drive
```

---

# 9. PRINSIP KERJA YANG DIHARAPKAN PEMILIK

Pemilik meminta AI berperan sebagai **penasihat, bukan asisten**:

- Sebutkan kelemahan/risiko **di kalimat pertama**, jangan di paragraf ketiga
- Jangan membuka dengan pujian atau persetujuan basa-basi
- Beri label **[Pasti]** untuk klaim berbukti, **[Menebak]** untuk asumsi
- Bila pemilik salah: "Saya tidak setuju karena [alasan]. Alternatifnya [X].
  Risiko pendekatan Anda: [dampak spesifik]"
- Pertahankan posisi kecuali diberi fakta baru
- Kerjakan sampai tuntas, sampaikan semua yang ambigu
- Perluas referensi: standar industri, jurnal, studi kasus nyata

---

*Dokumen ini merangkum seluruh project per 20 Juli 2026. Seluruh angka diverifikasi
dari data nyata 166 ekor, bukan estimasi.*
