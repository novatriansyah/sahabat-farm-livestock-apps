# INSTRUKSI GAP ANALYSIS LANJUTAN
## Data & pemeriksaan yang perlu ditambahkan pada putaran berikutnya

**Untuk:** tim/AI yang mengerjakan gap analysis sistem SFI
**Konteks:** gap analysis putaran-1 sudah menemukan 6 gap struktural (kolom & importer).
Putaran-2 harus memeriksa **logika bisnis dan alur data**, bukan hanya pemetaan kolom.

---

## 1. Penilaian atas gap analysis putaran-1

**Yang sudah baik:** pemetaan kolom Excel → skema database dikerjakan teliti dan akurat.
Temuan importer (multi-sheet, header baris 5, bahasa Indonesia) tepat sasaran.

**Keterbatasannya:** analisa berhenti pada pertanyaan *"kolom apa yang belum ada?"*
sehingga melewatkan pertanyaan yang lebih penting: *"apakah perhitungan yang sudah
berjalan sudah benar?"*

Akibatnya 8 gap logika bisnis tidak terdeteksi, 3 di antaranya berseverity kritis.

**Dua koreksi faktual:**

| Temuan | Koreksi |
| :--- | :--- |
| Gap 6: "kode menangani warna eartag sebagai teks bebas tanpa aturan" | **Kurang tepat.** `AnimalObserver` sudah melakukan lookup `eartag_map_dorper_{generation}` dengan fallback per breed. Aturannya **sudah ada**. Yang kurang: validasi input + penyelarasan nilai (sistem F3=Kuning Orange, aturan SFI F3=Biru) |
| Gap 5: rekomendasi membuat tabel agregat kelahiran | **Over-engineered.** Hanya 25 baris data historis statis. Tabel + model + UI tidak sebanding. Cukup JSON di `farm_settings` |

---

## 2. Data yang perlu diminta (belum tersedia)

Gap analysis putaran-2 tidak akan akurat tanpa data berikut:

### 2.1 Prioritas tinggi — tanpa ini analisa hanya menebak

| Data | Kenapa dibutuhkan | Cara memperolehnya |
| :--- | :--- | :--- |
| **Isi tabel `farm_settings`** | Seluruh parameter operasional (nifas, umur sapih, est. biaya, mapping eartag) ada di sini. Tanpa ini, tidak bisa dinilai apakah parameternya wajar | `SELECT * FROM farm_settings;` |
| **Isi `master_phys_status`** | Status fisik menentukan alur otomatis (`is_breedable`, `is_pregnant`, `is_quarantine`). Perlu dicek apakah selaras dengan status di Excel | `SELECT * FROM master_phys_status;` |
| **Isi `master_breeds`** | Untuk memastikan "Lokal/Komposit", "Cross Texel", "F1–F6 DORPER" sudah terdaftar dengan `min_age_mate_months` yang benar | `SELECT * FROM master_breeds;` |
| **Isi `master_locations`** | Memverifikasi 7 kandang di Excel cocok dengan yang di sistem | `SELECT * FROM master_locations;` |
| **Migration files lengkap** | Gap analysis putaran-1 menyebut skema tapi tidak melampirkan definisi tabel. Perlu untuk memeriksa indeks, constraint, cascade | `database/migrations/*.php` |
| **Jumlah record produksi saat ini** | Menentukan apakah impor 166 ekor akan bentrok dengan data existing | `SELECT COUNT(*), acquisition_type FROM animals GROUP BY acquisition_type;` |

### 2.2 Prioritas sedang

| Data | Kenapa dibutuhkan |
| :--- | :--- |
| Kode `CalculateDailyHpp.php` | Memverifikasi apakah rumus alokasi benar-benar seperti di summary |
| Kode `BreedingService.php` | Memeriksa urutan & kelengkapan validasi kawin |
| Kode `AnimalsImport.php` | Menilai seberapa besar refactor yang dibutuhkan |
| Definisi tabel `animal_ear_tag_logs` & `animal_ownership_logs` | Memastikan riwayat 46 penggantian eartag bisa masuk |
| Contoh `Invoice` + `InvoiceItem` terisi | Menilai kesiapan modul bagi hasil |
| Struktur tabel `notifications` | Menilai kelayakan untuk peringatan inbreeding |

### 2.3 Informasi non-teknis yang menentukan roadmap

Ini **belum pernah ditanyakan** dan sangat menentukan kelayakan rekomendasi:

1. **Siapa yang akan mengerjakan perbaikan?** (internal / vendor / Anda sendiri)
   → menentukan apakah estimasi effort realistis
2. **Berapa lama sistem sudah berjalan & berapa banyak data di dalamnya?**
   → menentukan strategi migrasi (fresh vs merge)
3. **Apakah mitra sudah melihat angka HPP di dashboard?**
   → menentukan apakah perbaikan HPP perlu komunikasi khusus
4. **Apakah ada test suite?**
   → menentukan risiko regresi saat mengubah logika HPP
5. **Berapa target penambahan mitra & populasi 12 bulan ke depan?**
   → menentukan apakah perlu optimasi skala sekarang atau nanti
6. **Siapa pejantan yang dipakai selama ini?** (data di luar sistem & Excel)
   → **kritis** untuk mengisi `sire_id` retroaktif dan menghitung inbreeding

---

## 3. Pemeriksaan yang harus ditambahkan (metodologi)

Gap analysis putaran-2 harus mencakup **5 dimensi** berikut, bukan hanya dimensi ke-1:

### Dimensi 1 — Struktur data ✅ *(sudah dikerjakan putaran-1)*
Pemetaan kolom, tipe data, relasi.

### Dimensi 2 — Kebenaran logika bisnis ❌ *(belum)*
Untuk **setiap rumus** dalam sistem, uji dengan data nyata:
- Apakah rumus HPP menghasilkan angka yang masuk akal?
- Apakah pembagian biaya adil antar pemilik?
- Apakah logika generasi cocok dengan aturan SFI? *(sudah saya uji: cocok 102/102)*
- Apakah validasi kawin sudah lengkap?

**Metode:** ambil 10 sampel ternak nyata, hitung manual, bandingkan dengan output sistem.

### Dimensi 3 — Kelengkapan alur end-to-end ❌ *(belum)*
Telusuri satu ekor dari lahir sampai jual. Di titik mana data terputus?

**Temuan saya dengan metode ini:** 3 titik putus (sire tidak diturunkan dari koloni,
bobot sapih tidak ditandai, bagi hasil tidak dihitung).

### Dimensi 4 — Kesesuaian dengan standar industri ❌ *(belum)*
Bandingkan dengan standar ICAR & KPI reproduksi baku.

**Temuan saya:** 7 dari 8 KPI reproduksi standar tidak ada di sistem.

### Dimensi 5 — Kesiapan pengguna nyata ❌ *(belum)*
Apakah kepala kandang bisa memakainya di kandang dengan sinyal lemah?
Apakah mitra memahami angka yang ditampilkan?

---

## 4. Pertanyaan spesifik untuk putaran-2

Salin daftar ini sebagai checklist pemeriksaan:

**Tentang HPP**
- [ ] Apakah `CalculateDailyHpp` memfilter berdasarkan `partner_id`? *(dugaan: tidak)*
- [ ] Apakah alokasi pakan memperhitungkan bobot/umur? *(dugaan: tidak)*
- [ ] Apakah biaya manual di-pro-rata terhadap hari aktif? *(dugaan: tidak)*
- [ ] Apakah ada tabel jejak audit alokasi biaya? *(dugaan: tidak)*
- [ ] Apa yang terjadi pada `current_hpp` bila ternak pindah kandang di tengah bulan?
- [ ] Apa yang terjadi bila `InventoryPurchase` dihapus setelah biayanya dialokasikan?

**Tentang silsilah**
- [ ] Berapa persen `animals` yang punya `sire_id` terisi? *(terverifikasi: 0%)*
- [ ] Apakah `MatingColony` terhubung ke `BirthController`? *(dugaan: tidak)*
- [ ] Apakah ada validasi yang mencegah `sire_id == dam_id`?
- [ ] Apakah ada perlindungan terhadap silsilah melingkar (A anak B, B anak A)?

**Tentang mitra**
- [ ] Apakah query dashboard mitra benar-benar memfilter `partner_id` di **semua** endpoint?
- [ ] Bisakah mitra melihat data mitra lain melalui manipulasi URL/parameter?
- [ ] Apakah `is_for_sale` ternak mitra bisa diubah tanpa persetujuan mitra?
- [ ] Di mana perhitungan bagi hasil 30% dilakukan? *(dugaan: di luar sistem)*

**Tentang integritas data**
- [ ] Apakah ada constraint UNIQUE pada `tag_id`?
- [ ] Apakah soft delete diterapkan? Apa dampaknya pada perhitungan HPP?
- [ ] Apakah `ExitLog` mencegah ternak yang sudah keluar dicatat lagi?
- [ ] Apakah ada audit trail siapa mengubah apa? *(hanya eartag & ownership yang ada log)*

**Tentang operasional lapangan**
- [ ] Apakah UI staf berfungsi tanpa koneksi internet? *(dugaan: tidak)*
- [ ] Berapa banyak ketikan yang dibutuhkan untuk mencatat 1 kelahiran?
- [ ] Apakah ada fungsi undo untuk kesalahan input?
- [ ] Apakah `MasterSop` menghasilkan tugas yang benar-benar dikerjakan?

---

## 5. Format output yang disarankan untuk putaran-2

Agar hasilnya langsung bisa dieksekusi:

```markdown
### GAP-XX: [Judul singkat]
**Severity:** Kritis / Tinggi / Sedang / Rendah
**Dimensi:** Struktur / Logika / Alur / Standar / Pengguna

**Temuan.** [Apa yang salah, dengan kutipan kode/skema]
**Bukti.** [Angka dari data nyata — bukan dugaan]
**Dampak bisnis.** [Konsekuensi konkret dalam rupiah/risiko/waktu]
**Perbaikan.** [Kode/skema spesifik, bukan saran umum]
**Effort.** [Hari kerja]
**Prasyarat.** [Gap lain yang harus selesai dulu]
```

Setiap temuan **wajib menyertakan bukti dari data nyata**. Temuan tanpa bukti angka
adalah dugaan, dan dugaan tidak boleh masuk roadmap.

---

## 6. Ringkasan: apa yang harus diminta ke pemilik sistem

Kirim daftar ini apa adanya:

> Untuk melanjutkan gap analysis, mohon sediakan:
>
> **Data database (export SQL atau CSV):**
> 1. `farm_settings` — seluruh baris
> 2. `master_phys_status`, `master_breeds`, `master_locations`, `master_partners`
> 3. `SELECT COUNT(*), acquisition_type, is_active FROM animals GROUP BY 2,3;`
> 4. `SELECT COUNT(*) FROM animals WHERE sire_id IS NOT NULL;`
> 5. Struktur tabel: `animal_ear_tag_logs`, `animal_ownership_logs`, `notifications`
>
> **Kode sumber:**
> 6. `app/Actions/Finance/CalculateDailyHpp.php`
> 7. `app/Services/BreedingService.php`
> 8. `app/Http/Controllers/BirthController.php`
> 9. `app/Imports/AnimalsImport.php`
> 10. `database/migrations/*_create_animals_table.php`
>
> **Informasi operasional:**
> 11. Siapa pejantan yang dipakai Sep 2025–Jul 2026? (untuk mengisi `sire_id` retroaktif)
> 12. Sudah berapa lama sistem berjalan & berapa record di dalamnya?
> 13. Apakah mitra sudah melihat angka HPP di dashboard mereka?
> 14. Siapa yang akan mengerjakan perbaikan & berapa kapasitasnya?

---

*Instruksi ini disusun setelah memeriksa Project Summary sistem dan memverifikasi
166 record ternak nyata. Delapan gap logika bisnis yang ditemukan tercantum lengkap
pada dokumen `REKOMENDASI_SISTEM_SFI.md`.*
