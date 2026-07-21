# PANDUAN LANGKAH DEMI LANGKAH
## Apa yang harus Anda lakukan, dengan file mana

**Untuk:** Pemilik SFI
**Konteks:** perbaikan sistem web + penyelarasan data + upload data bersih

---

# RINGKASAN CEPAT

```
LANGKAH 1  Kirim 8 file ke developer         → dia audit sistem
LANGKAH 2  Terima laporan audit               → kirim ke saya untuk ditinjau
LANGKAH 3  Developer bangun fitur EXPORT      → ini pemblokir semuanya
LANGKAH 4  ANDA export data dari sistem       → kirim ke saya
LANGKAH 5  Saya rekonsiliasi & buat file final
LANGKAH 6  Developer lanjut perbaikan Tahap 2-4
LANGKAH 7  Reset & upload data bersih
LANGKAH 8  Verifikasi & lanjut fitur
```

**Waktu realistis:** 8–12 minggu, bukan 2–3 minggu. Cakupannya setara 6 sprint.

---

# LANGKAH 1 — KIRIM FILE KE DEVELOPER

## File yang WAJIB dikirim (8 file)

| # | Nama File | Fungsi bagi developer |
| :---: | :--- | :--- |
| 1 | **`PROMPT_ANTIGRAVITY_EKSEKUSI.md`** | ⭐ **Prompt utama** — paste ini ke Antigravity |
| 2 | `MASTER_PROMPT_PERBAIKAN_SISTEM.md` | Spesifikasi teknis 6 tahap |
| 3 | `REKOMENDASI_SISTEM_SFI.md` | Alasan tiap perbaikan + analisa mendalam |
| 4 | `2026_07_20_000001_sfi_critical_fixes.php` | Migrasi Laravel siap pakai |
| 5 | `IMPORT_01_INDUKAN.csv` | 64 indukan format database |
| 6 | `IMPORT_02_ANAKAN.csv` | 102 anakan format database |
| 7 | `IMPORT_03_RIWAYAT_EARTAG.csv` | 46 riwayat penggantian nomor |
| 8 | `IMPORT_04_KONFLIK_DATA.csv` | 37 item yang perlu konfirmasi |

## Pesan yang Anda kirim ke developer

> Tolong kerjakan perbaikan sistem SFI sesuai dokumen terlampir.
>
> **Cara mulai:** paste isi `PROMPT_ANTIGRAVITY_EKSEKUSI.md` ke Antigravity,
> lampirkan 7 file lainnya.
>
> **Penting — jangan dilewati:**
> 1. Kerjakan **satu tahap per sesi**, jangan sekaligus. Cakupannya setara 6 sprint.
> 2. Mulai dari **TAHAP 0 (Audit)** — jangan ubah kode apa pun dulu.
> 3. Setiap tahap **wajib** menghasilkan 3 file di `/docs/sfi-progress/`:
>    laporan, source code lengkap, dan feedback.
> 4. File source code harus **lengkap, bukan ringkasan** — saya butuh arsipnya
>    untuk keperluan dokumentasi dan bila nanti ganti developer.
> 5. Setelah tiap tahap selesai, kirim ketiga file itu ke saya. Jangan lanjut
>    tahap berikutnya sebelum saya konfirmasi.
>
> **Prioritas mutlak:** TAHAP 1 (fitur Export) harus selesai lebih dulu.
> Saya tidak bisa melakukan apa pun sebelum bisa mengekspor data dari sistem.

## Yang Anda terima dari LANGKAH 1

```
/docs/sfi-progress/
├── TAHAP-0-AUDIT-SISTEM.md      ← kondisi sistem sebenarnya
├── TAHAP-0-SOURCECODE.md        ← salinan kode kunci
└── TAHAP-0-FEEDBACK.md          ← temuan & rekomendasi
```

---

# LANGKAH 2 — TINJAU HASIL AUDIT

Kirim ketiga file hasil Tahap 0 ke saya (atau AI lain). Saya akan periksa:

- Apakah 5 dugaan kritis terbukti? (HPP campur, pakan headcount, `sire_id` kosong,
  biaya manual tak pro-rata, generasi salah)
- Apakah ada temuan baru yang mengubah urutan pengerjaan?
- Apakah data produksi lebih banyak/berbeda dari dugaan?
- Apakah ada risiko yang belum terpetakan?

**Kenapa langkah ini penting:** rekomendasi saya sejauh ini dibuat dari *Project Summary*,
bukan dari kode asli. Audit akan mengonfirmasi atau mengoreksinya. Bisa saja ada
temuan yang membuat urutan pengerjaan harus diubah.

---

# LANGKAH 3 — DEVELOPER BANGUN FITUR EXPORT

Ini **satu-satunya pekerjaan** di Tahap 1. Jangan biarkan developer mengerjakan
hal lain dulu.

## Yang harus jadi

| Fitur | Kriteria selesai |
| :--- | :--- |
| Export data ternak multi-sheet | Indukan, anakan, riwayat bobot/kesehatan/eartag/pemilik/HPP |
| Kolom `gdrive_folder_url` | Ada di export **dan** template import |
| Format aman | Tanggal `YYYY-MM-DD` teks, desimal titik, eartag sebagai teks |
| Export laporan | PDF, Excel, PPT, gambar |
| Filter laporan | Periode harian–tahunan–kustom, per mitra, pilih kolom |
| Backup otomatis | Terjadwal + **sudah diuji restore** |

## Cara Anda memverifikasi (jangan hanya percaya laporan)

1. Buka menu export di sistem, unduh file
2. Buka di Excel — pastikan tidak error
3. Hitung jumlah baris, bandingkan dengan jumlah ternak di dashboard
4. Cek kolom `gdrive_folder_url` **ada** dan terisi
5. Cek nomor eartag `036` tidak berubah jadi `36`
6. Cek tanggal tidak berubah format
7. Coba export laporan ke PDF — pastikan bisa dibuka

**Bila salah satu gagal, jangan lanjut ke Tahap 2.**

---

# LANGKAH 4 — ANDA EXPORT DATA DARI SISTEM

Setelah fitur export berfungsi, Anda kerjakan ini sendiri:

1. Login sebagai Owner
2. Menu Export → pilih **semua data** (jangan difilter)
3. Unduh file Excel hasil export
4. Simpan dengan nama jelas: `EXPORT_SISTEM_2026-08-15.xlsx`
5. **Jangan diedit dulu** — kirim apa adanya ke saya

## Yang perlu Anda sertakan saat mengirim ke saya

> "Ini hasil export dari sistem per tanggal {tanggal}.
> Jumlah ternak di dashboard: {angka}.
> Tolong bandingkan dengan `SFI_MASTER_TERNAK_v3.xlsx` dan buatkan file final."

---

# LANGKAH 5 — SAYA REKONSILIASI DATA

Yang saya kerjakan:

1. **Bandingkan** data sistem vs data Excel master
2. **Identifikasi:**
   - Ternak yang ada di sistem tapi tidak di Excel (kemungkinan input setelah Excel dibuat)
   - Ternak yang ada di Excel tapi tidak di sistem (belum diinput)
   - Ternak yang ada di keduanya tapi datanya berbeda
   - Duplikasi (satu ekor tercatat dua kali dengan nomor berbeda)
3. **Laporkan konflik** untuk Anda putuskan
4. **Hasilkan file final** yang sudah bersih dan siap upload

## Yang Anda terima

```
EXPORT_vs_MASTER_PERBANDINGAN.xlsx    ← daftar selisih untuk Anda tinjau
SFI_MASTER_TERNAK_v4.xlsx             ← database master terbaru
IMPORT_FINAL_siap_upload.xlsx         ← file siap diunggah ke sistem
LAPORAN_REKONSILIASI.md               ← penjelasan apa yang berubah
```

## Yang Anda kerjakan di langkah ini

Tinjau file perbandingan, lalu jawab pertanyaan konflik. Contoh:

> "Ternak nomor 269 ada di sistem dengan tanggal lahir 1 Des 2025,
> tapi di catatan tangan tertulis 17 Feb 2026. Mana yang benar?"

Semakin cepat Anda menjawab, semakin cepat file final jadi.

---

# LANGKAH 6 — DEVELOPER LANJUT TAHAP 2–4

Setelah data beres, developer melanjutkan:

| Tahap | Isi | Perkiraan |
| :---: | :--- | :---: |
| **2** | Perbaikan HPP, aturan generasi, auto-isi `sire_id` | 2 minggu |
| **3** | Pengaturan ke frontend (role, kategori umur, tampilan web) | 2–3 minggu |
| **4** | Modul penjualan, pakan/vitamin, laporan berfilter | 2–3 minggu |

## ⚠️ Peringatan penting untuk Tahap 2

Perbaikan HPP **akan mengubah angka yang sudah dilihat mitra**. Sebelum deploy:

1. Minta developer hitung dampaknya di staging — berapa selisih HPP per mitra
2. Siapkan laporan "sebelum vs sesudah"
3. **Beri tahu mitra lebih dulu**, jangan sampai mereka kaget melihat angka berubah
4. Bila selisihnya merugikan mitra, pertimbangkan terapkan hanya untuk periode ke depan

Ini masalah kepercayaan, bukan teknis. Salah penanganan bisa merusak hubungan
dengan 5 mitra sekaligus.

---

# LANGKAH 7 — RESET & UPLOAD DATA BERSIH

**Jangan lakukan ini sebelum Tahap 1–4 selesai.**

## Urutan wajib

```
1. Export ulang data terbaru dari sistem        → arsip
2. Backup penuh database                        → uji restore dulu!
3. Uji import file final di STAGING             → dry-run
4. Perbaiki temuan, ulangi sampai bersih
5. Maintenance mode ON
6. Reset data transaksional (JANGAN reset master data & pengaturan)
7. Import file final
8. Validasi (checklist di bawah)
9. Maintenance mode OFF
```

## Checklist validasi setelah upload

Minta developer jalankan, Anda periksa hasilnya:

```sql
-- Jumlah harus sesuai
SELECT COUNT(*) FROM animals WHERE is_active=1;

-- Tidak boleh ada anak tanpa induk
SELECT COUNT(*) FROM animals
WHERE acquisition_type='HASIL_TERNAK' AND dam_id IS NULL;   -- harus 0

-- Tidak boleh ada nomor ganda
SELECT tag_id, COUNT(*) FROM animals GROUP BY tag_id HAVING COUNT(*)>1;  -- harus kosong

-- Sebaran kepemilikan
SELECT partner_id, COUNT(*) FROM animals WHERE is_active=1 GROUP BY partner_id;

-- Ternak belum bernomor masuk daftar pending
SELECT COUNT(*) FROM pending_tag_assignments WHERE status='PENDING';
```

---

# LANGKAH 8 — VERIFIKASI & LANJUT

## Yang Anda kerjakan sendiri setelah sistem beres

**Prioritas 1 — Lengkapi 11 ternak tanpa nomor**
Buka menu notifikasi "Ternak belum bernomor". Sistem akan menampilkan rentang nomor
perkiraan (mis. "antara B44 dan B48"). Cek laporan grup WA pada tanggal tersebut,
temukan nomornya, lalu perbarui.

**Prioritas 2 — Isi data pejantan (`sire_id`)**
Ini yang membuka kunci fitur anti-inbreeding. Buka menu "Verifikasi Silsilah",
isi pejantan untuk tiap kelahiran berdasarkan koloni kawin saat itu.
**Baru setelah ini** generasi bisa dihitung ulang dengan benar.

**Prioritas 3 — Lengkapi data asumsi**
- 39 tanggal lahir indukan (saat ini disamakan dengan VINA)
- 12 bobot lahir anakan
- 3 jenis kelamin

**Prioritas 4 — Buat folder Google Drive**
Jalankan `BUAT_FOLDER_GDRIVE.gs` di script.google.com, salin link hasilnya
ke kolom `gdrive_folder_url`.

---

# DAFTAR FILE — SIAPA MENDAPAT APA

## Untuk DEVELOPER (8 file)

```
✅ PROMPT_ANTIGRAVITY_EKSEKUSI.md        ← prompt utama, paste ke Antigravity
✅ MASTER_PROMPT_PERBAIKAN_SISTEM.md     ← spesifikasi teknis
✅ REKOMENDASI_SISTEM_SFI.md             ← analisa & alasan
✅ 2026_07_20_000001_sfi_critical_fixes.php
✅ IMPORT_01_INDUKAN.csv
✅ IMPORT_02_ANAKAN.csv
✅ IMPORT_03_RIWAYAT_EARTAG.csv
✅ IMPORT_04_KONFLIK_DATA.csv
```

## Untuk ANDA SIMPAN (jangan dikirim ke developer)

```
📁 SFI_MASTER_TERNAK_v3.xlsx             ← database master, sumber kebenaran
📁 IMPORT_TERNAK_SFI_siap_upload.xlsx
📁 LAPORAN_ANALISA_SFI.md                ← untuk mitra & investor
📁 SUMMARY_PROJECT_SFI.md                ← konteks project
📁 PROMPT_COWORK_SFI.md                  ← bila pindah ke Cowork/AI lain
📁 INSTRUKSI_GAP_ANALYSIS_LANJUTAN.md
📁 BUAT_FOLDER_GDRIVE.gs                 ← jalankan sendiri di script.google.com
📁 15 scan catatan tulisan tangan
📁 5 file Excel recording mitra
```

## Untuk DIBAWA bila pindah AI/platform

```
1. SUMMARY_PROJECT_SFI.md                ← wajib
2. PROMPT_COWORK_SFI.md                  ← wajib
3. SFI_MASTER_TERNAK_v3.xlsx             ← wajib
4. Seluruh /docs/sfi-progress/*.md       ← dari developer, bila sudah ada
5. Sisanya opsional
```

---

# YANG PALING SERING SALAH

| Kesalahan | Akibat | Cara menghindari |
| :--- | :--- | :--- |
| Reset sistem sebelum export jadi | **Data hilang permanen** | Verifikasi export sendiri, jangan percaya laporan saja |
| Developer kerjakan semua tahap sekaligus | Kode setengah jadi, tidak terintegrasi | Tegaskan: satu tahap per sesi |
| Terima laporan tanpa cek sendiri | Masalah baru ketahuan saat sudah terlambat | Selalu uji sendiri hasil tiap tahap |
| Ubah HPP tanpa beri tahu mitra | Mitra kehilangan kepercayaan | Komunikasikan **sebelum** deploy |
| Hitung ulang generasi sebelum `sire_id` terisi | Generasi 102 anakan bisa salah semua | Isi pejantan dulu, baru hitung ulang |
| Tidak menyimpan file source code dari developer | Terkunci pada satu developer | Minta `TAHAP-{n}-SOURCECODE.md` tiap tahap |

---

# KAPAN HARUS KEMBALI KE SAYA

Kirim ke saya bila:

- ✅ Laporan audit Tahap 0 selesai → saya tinjau temuan
- ✅ Export data sistem berhasil → saya rekonsiliasi dengan Excel master
- ✅ Ada konflik data yang perlu diputuskan
- ✅ Laporan tiap tahap dari developer → saya periksa kualitasnya
- ✅ Ada data ternak baru → saya perbarui file master
- ✅ Butuh laporan untuk mitra/investor
- ✅ Developer memberi rekomendasi yang Anda ragukan

Bawa file `SUMMARY_PROJECT_SFI.md` + `PROMPT_COWORK_SFI.md` bila memulai sesi baru,
agar saya langsung paham konteksnya tanpa mengulang dari awal.
