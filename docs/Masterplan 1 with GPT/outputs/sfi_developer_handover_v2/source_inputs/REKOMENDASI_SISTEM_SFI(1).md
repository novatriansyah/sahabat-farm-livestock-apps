# REKOMENDASI PERBAIKAN SISTEM
## Sahabat Farm Indonesia — Livestock Management System

**Disusun:** 20 Juli 2026
**Basis analisa:** Project Summary sistem Laravel 12 + Gap Analysis + data nyata 166 ekor ternak
**Status sistem:** Sudah jadi & berjalan (bukan greenfield)

---

# BAGIAN 0 — RINGKASAN EKSEKUTIF

## Penilaian umum

Sistem SFI **jauh lebih matang** dari yang tersirat di gap analysis. Arsitekturnya sehat:
Laravel 12, 28 model, observer pattern untuk integritas data, scheduled job untuk HPP harian,
RBAC 4 role, katalog publik, CMS, dan QR code per ekor. Ini bukan sistem yang perlu dirombak.

**Namun gap analysis melewatkan hal yang jauh lebih berbahaya daripada kolom yang hilang.**

Gap analysis fokus pada *"kolom Excel apa yang belum ada di database"* — pendekatan yang benar
tapi dangkal. Ia menemukan 9 kolom hilang dan menyebut 3 di antaranya "Critical Gap".
Analisa saya menemukan **3 cacat logika bisnis yang sedang aktif merusak data setiap hari**,
dan tidak satu pun disebut dalam gap analysis.

## 5 masalah terbesar (urut severity)

| # | Masalah | Severity | Dampak |
| :--- | :--- | :---: | :--- |
| 1 | **HPP mencampur biaya ternak mitra dengan ternak SFI** | 🔴 KRITIS | Bagi hasil salah → risiko sengketa hukum dengan 5 mitra |
| 2 | **Alokasi pakan dibagi rata per ekor tanpa bobot** | 🔴 KRITIS | Cempe 3 kg dibebani sama dengan bakalan 30 kg → HPP menyesatkan |
| 3 | **`sire_id` kosong 100% → anti-inbreeding mustahil** | 🔴 KRITIS | 46 calon indukan akan kawin 8–12 bulan lagi tanpa proteksi inses |
| 4 | **Biaya manual dibagi ke ternak umur 3 hari** | 🟠 TINGGI | HPP cempe melonjak tidak wajar → keputusan jual salah |
| 5 | **7 KPI reproduksi standar industri tidak ada** | 🟠 TINGGI | Investor tidak bisa menilai kualitas peternakan secara objektif |

## 5 rekomendasi prioritas

| # | Tindakan | Effort | Dampak |
| :--- | :--- | :---: | :--- |
| 1 | Tambah `partner_id` sebagai filter di semua alokasi HPP | 2–3 hari | Menutup risiko sengketa mitra |
| 2 | Ganti pembagi HPP pakan dari *headcount* → *bobot metabolis* | 3–4 hari | HPP akurat & bisa dipertanggungjawabkan |
| 3 | Auto-isi `sire_id` dari koloni kawin aktif saat registrasi lahir | 2 hari | Silsilah lengkap tanpa input tambahan |
| 4 | Tambah 9 kolom + importer multi-sheet (sesuai gap analysis) | 4–5 hari | Migrasi 166 ekor tanpa kehilangan data |
| 5 | Modul KPI reproduksi + dashboard mitra berbasis KPI | 5–7 hari | Nilai jual ke calon mitra & investor |

## Angka kinerja SFI saat ini (hasil hitung dari data nyata)

Angka ini **belum pernah muncul di sistem** karena modulnya belum ada, padahal sangat bagus:

| KPI | SFI | Benchmark Industri | Status |
| :--- | :---: | :---: | :---: |
| Lambing rate | **159%** | 150–175% | ✅ Baik |
| Fertility rate | **92%** | >90% | ✅ Baik |
| Pre-weaning mortality | **1,0%** | <10% | ✅ Sangat baik |
| Lambing interval | **133 hari** | 243 hari | ✅ Sangat baik |
| Prolificacy | **1,44** | 1,6–1,8 | ⚠️ Di bawah target |
| Kg anak disapih/induk | **tidak terukur** | 40–50 kg | ❌ Data tidak ada |
| Ewe efficiency | **tidak terukur** | ≥0,8 | ❌ Data tidak ada |

> **Ini temuan penting untuk pemasaran:** mortalitas 1% dan lambing interval 133 hari
> adalah angka yang sangat kompetitif. Sistem seharusnya menampilkan ini untuk menarik mitra baru.

---

# BAGIAN 1 — PEMAHAMAN SISTEM SAAT INI

## 1.1 Arsitektur

```
┌──────────────────────────────────────────────────────────┐
│  FRONTEND: Blade + Tailwind v4 + Alpine.js + Flowbite    │
│  Vite 6 · Axios · Quill.js (CMS) · QR Code               │
└────────────────────────┬─────────────────────────────────┘
                         │
┌────────────────────────┴─────────────────────────────────┐
│  APPLICATION: Laravel 12 (PHP 8.2/8.3)                   │
│  ├── Controllers  (routing & request handling)           │
│  ├── Services     (BreedingService, DashboardService)    │
│  ├── Observers    (Animal, WeightLog, ExitLog)           │
│  ├── Actions      (CalculateDailyHpp)                    │
│  └── Scheduler    (4 job harian/bulanan)                 │
└────────────────────────┬─────────────────────────────────┘
                         │
┌────────────────────────┴─────────────────────────────────┐
│  DATA: MySQL · 28 Eloquent Models · UUID primary keys    │
└──────────────────────────────────────────────────────────┘
```

**Penilaian arsitektur:** Sehat dan sesuai praktik Laravel modern. Pemisahan
Observer/Service/Action sudah tepat. UUID sebagai PK adalah pilihan bagus untuk
data yang akan disinkronkan dari banyak sumber.

## 1.2 Alur sistem end-to-end (rekonstruksi)

```
[1] KAWIN
    MatingColony dibuat (1 sire + N dam) di satu MasterLocation
    ↓ BreedingService validasi: umur ≥8 bln, bobot ≥30 kg, nifas ≥40 hr, is_breedable
    ↓ MatingColonyMember status → KAWIN
    ↓ Scheduler: setelah 60 hari → status SIAP, keluar koloni
    ⚠️ TITIK PUTUS: sire dari koloni TIDAK diturunkan ke anak

[2] LAHIR
    BirthController registrasi anak (acquisition_type = HASIL_TERNAK)
    ↓ partner_id  ← warisi dari dam        ✅ benar
    ↓ breed_id    ← warisi dari sire/dam   ✅ benar
    ↓ generation  ← max(sire,dam)+1        ✅ benar (terverifikasi 102/102 cocok)
    ↓ AnimalObserver → ear_tag_color dari FarmSetting
    ↓ Dam status → Menyusui
    ↓ MasterSop event BIRTH → generate AnimalTask
    ⚠️ TITIK PUTUS: sire_id kosong, litter_size & birth_weight tidak tercatat

[3] TUMBUH
    Staf scan QR → WeightLog
    ↓ WeightLogObserver hitung ADG = (BB_now − BB_prev) / hari
    ↓ Scheduler animal:auto-status → umur 60 hr: Cempe → Bakalan/Dara
    ↓ Dam kembali ke Dara saat anak disapih
    ⚠️ TITIK PUTUS: bobot sapih tidak ditandai khusus → KPI kg-sapih mustahil

[4] BIAYA (HPP)
    InventoryUsageLog (pakan/obat) per kandang
    ↓ CalculateDailyHpp (00:00) → alokasi pakan dibagi rata per ekor sekandang
    ↓ TreatmentLog → biaya obat real-time
    ↓ HppManualCost → dibagi rata ke SELURUH ternak aktif
    ↓ animals.current_hpp bertambah
    🔴 CACAT: pembagian tidak memandang pemilik & tidak memandang bobot

[5] JUAL
    ExitLog type=JUAL → final_hpp = current_hpp saat itu
    ↓ Net Profit = sale_price − (purchase_price + final_hpp)
    ↓ Invoice + InvoiceItem
    ⚠️ TITIK PUTUS: bagi hasil mitra 30% tidak ada di sistem

[6] PUBLIK
    is_for_sale = true → tampil di /katalog
    ↓ Sembunyikan HPP, lokasi, pemilik
    ↓ WhatsApp inquiry
```

## 1.3 Yang sudah bagus dan tidak perlu diubah

Saya sebutkan eksplisit agar tidak diotak-atik tanpa alasan:

- **Observer pattern untuk ADG** — tepat, otomatis, tidak bisa dilewati
- **Logika generasi F1→F2→F3→PURE** — sudah terverifikasi 100% cocok dengan aturan SFI
- **BreedingService dengan 4 validasi kawin** — sudah sesuai praktik industri (umur, bobot, nifas, status)
- **Weighted average unit cost untuk inventory** — metode akuntansi yang benar
- **Auto-weaning & dam reversion terjadwal** — mengurangi beban input manual
- **UUID + audit log (earTagLogs, ownershipLogs)** — fondasi ketertelusuran sudah ada
- **Katalog publik menyembunyikan HPP & pemilik** — keputusan keamanan yang benar

---

# BAGIAN 2 — ANALISA GAP TERKONSOLIDASI

## 2.1 Evaluasi terhadap Gap Analysis yang ada

Gap analysis Anda **akurat dalam hal yang diperiksanya**, tapi cakupannya terbatas
pada pemetaan kolom. Berikut penilaian saya per temuan:

| Temuan Gap Analysis | Penilaian Saya | Catatan |
| :--- | :---: | :--- |
| Gap 1: 9 kolom hilang di `animals` | ✅ **Benar & valid** | Semua 9 memang perlu. Lihat koreksi prioritas di bawah |
| Gap 2: Importer tidak kompatibel | ✅ **Benar & valid** | Analisa teknisnya tepat (multi-sheet, header row 5, bahasa) |
| Gap 3: Google Drive tanpa API | ✅ **Benar** | Tapi prioritas rendah — Apps Script manual sudah memadai |
| Gap 4: Tidak ada modul konflik data | ✅ **Benar** | Setuju, tapi bisa disederhanakan (lihat rekomendasi) |
| Gap 5: Kelahiran agregat tak tertampung | ⚠️ **Benar tapi over-engineered** | 25 baris data historis — tidak perlu tabel & modul khusus |
| Gap 6: Aturan warna eartag tak tervalidasi | ⚠️ **Sebagian keliru** | Sistem SUDAH punya mapping di `AnimalObserver` + `FarmSetting`. Yang kurang hanya *validasi*, bukan aturannya |

### Koreksi penting atas Gap Analysis

**Gap 6 tidak seakurat yang ditulis.** Summary sistem menyebut `AnimalObserver` sudah
melakukan lookup `eartag_map_dorper_{generation}` dengan fallback per breed. Jadi aturannya
**sudah ada dan berjalan**. Yang benar-benar kurang hanyalah *validasi saat input manual*
dan *penyelarasan nilai* — sistem memakai "F3 = Kuning Orange", sedangkan aturan SFI terbaru
(yang Anda konfirmasi) adalah **"F3 = Biru"**. Ini bukan gap struktural, tapi **konflik nilai
konfigurasi** yang harus diselaraskan, dan jauh lebih mudah diperbaiki.

**Gap 5 tidak perlu tabel baru.** 25 baris kelahiran agregat Jan–Jul 2025 tanpa detail
individu. Membuat tabel + model + UI untuk 25 baris statis adalah pemborosan. Cukup simpan
sebagai satu record JSON di `farm_settings` group `HISTORICAL`, atau bahkan cukup di
lampiran dokumen. **Rekomendasi saya: jangan dikerjakan.**

## 2.2 Gap yang TIDAK ditemukan Gap Analysis (temuan saya)

Ini bagian terpenting dari dokumen ini.

---

### 🔴 GAP-A: HPP mencampur biaya lintas pemilik — RISIKO SENGKETA

**Severity: KRITIS**

**Temuan.** Summary sistem menyatakan:

```
Manual Cost Per Head = Manual Cost Amount / Total Active Animals Count
Feed Cost Per Head   = (Feed Qty × Unit Price) / Count of Active Animals in Pen
```

Tidak ada satu pun filter `partner_id` dalam rumus ini.

**Bukti dari data nyata:**

| Pemilik | Indukan | Anakan | Total |
| :--- | ---: | ---: | ---: |
| SFI (inti) | 39 | 59 | 98 |
| 5 Mitra | 25 | 43 | 68 |

Ternak SFI dan ternak mitra **berada di kandang yang sama**. Ketika pakan dicatat
untuk "Kandang Koloni 1", biayanya dibagi rata ke semua ternak di kandang itu —
tercampur antara milik SFI dan milik mitra.

**Konsekuensi nyata:**
1. Mitra menanggung sebagian biaya pakan ternak SFI, dan sebaliknya
2. `current_hpp` ternak mitra tidak mencerminkan biaya sebenarnya
3. Saat jual, `Net Profit = sale_price − (purchase_price + final_hpp)` menjadi salah
4. **Bagi hasil 30% dihitung dari angka yang salah**

**Kenapa ini kritis:** ini bukan soal akurasi angka, tapi soal **kepercayaan mitra**.
Bila seorang mitra mengaudit dan menemukan ternaknya dibebani biaya pakan ternak SFI,
SFI berada pada posisi hukum yang lemah. Dengan 5 mitra aktif dan rencana penambahan,
risiko ini membesar seiring waktu.

**Yang harus dilakukan:** lihat Bagian 3.1.

---

### 🔴 GAP-B: Alokasi pakan tidak memandang bobot ternak

**Severity: KRITIS**

**Temuan.** Pakan dibagi rata per *jumlah ekor*, bukan per *kebutuhan nutrisi*.

**Bukti dari data nyata — Kandang Menyusui 1 berisi 34 anakan** dengan rentang usia
sangat lebar. Cempe umur 1 bulan (BB ±3 kg) dan cempe umur 6 bulan (BB ±25 kg)
mendapat alokasi biaya pakan **sama persis**.

**Realitas biologis:** konsumsi bahan kering domba ≈ 3–4% dari bobot badan.
Domba 30 kg makan ±1,0 kg/hari; domba 3 kg makan ±0,1 kg/hari. **Selisih 10 kali lipat**,
tapi sistem membebankan biaya identik.

**Konsekuensi:**
- HPP cempe muda **terlalu tinggi** → tampak tidak menguntungkan padahal sebenarnya untung
- HPP bakalan besar **terlalu rendah** → tampak sangat untung padahal margin tipis
- Keputusan "jual atau tahan" diambil berdasarkan angka yang menyesatkan

**Standar industri:** alokasi pakan memakai **metabolic body weight (BB^0,75)**,
bukan headcount. Ini praktik baku dalam nutrisi ruminansia.

**Yang harus dilakukan:** lihat Bagian 3.2.

---

### 🔴 GAP-C: `sire_id` kosong 100% — anti-inbreeding mustahil

**Severity: KRITIS (dampaknya baru terasa 8–12 bulan lagi, karena itu harus dikerjakan SEKARANG)**

**Temuan.** Sistem punya kolom `sire_id` dan `dam_id`, serta modul `MatingColony`
yang mencatat pejantan per koloni. **Tapi keduanya tidak terhubung.**

**Bukti dari data nyata:**

| Data silsilah | Kelengkapan |
| :--- | :---: |
| `dam_id` (induk betina) | **102/102 = 100%** ✅ |
| `sire_id` (pejantan) | **0/102 = 0%** ❌ |

**Konsekuensi berantai:**

1. *Inbreeding coefficient* tidak bisa dihitung — rumus Wright butuh silsilah dua sisi
2. Sistem hanya bisa mencegah kawin induk–anak dari sisi ibu
3. **Saudara sebapak (half-sib) tidak terdeteksi sama sekali**
4. Dalam koloni kawin, 1 pejantan mengawini banyak betina → anak-anaknya *pasti* half-sib

**Urgensinya:** ada **46 anakan betina hidup** yang akan mencapai usia kawin (8 bulan)
dalam 8–12 bulan ke depan. Mereka akan masuk koloni kawin. Tanpa data pejantan,
SFI tidak akan tahu mana yang sebapak dengan pejantan yang tersedia. Inbreeding depression
pada domba menurunkan bobot lahir, daya tahan hidup, dan fertilitas — persis tiga hal
yang menjadi nilai jual SFI.

**Kabar baiknya — solusinya murah.** Sistem sudah punya `MatingColony` dengan `sire_id`.
Saat registrasi kelahiran, sistem bisa **menelusuri koloni kawin aktif si induk**
pada perkiraan waktu konsepsi (±150 hari sebelum lahir) dan mengisi `sire_id` otomatis.
**Tidak butuh input tambahan dari kepala kandang.**

**Yang harus dilakukan:** lihat Bagian 3.3.

---

### 🟠 GAP-D: Biaya manual dibebankan ke ternak baru lahir

**Severity: TINGGI**

**Temuan.** `Manual Cost Per Head = Amount / Total Active Animals`

Cempe yang lahir tanggal 30 tetap menanggung **biaya gaji dan listrik satu bulan penuh**.

**Bukti:** dengan 165 ternak aktif dan asumsi biaya operasional Rp 10 juta/bulan,
setiap ekor dibebani Rp 60.606 — termasuk cempe umur 3 hari.

**Konsekuensi:** HPP cempe baru lahir melonjak tidak wajar. Jika SFI menjual cempe
umur 3 bulan, HPP-nya sudah menanggung 3 bulan penuh biaya overhead seolah-olah
ia dewasa sepanjang periode itu.

**Perbaikan:** alokasi harus **pro-rata terhadap hari aktif** dalam periode biaya.

---

### 🟠 GAP-E: 7 KPI reproduksi standar industri tidak ada

**Severity: TINGGI (dampak komersial, bukan teknis)**

Sistem punya ADG — bagus. Tapi tidak punya KPI reproduksi yang menjadi
**bahasa standar industri** saat investor menilai peternakan.

| KPI | Rumus | Nilai SFI (hitung manual) | Status di sistem |
| :--- | :--- | :---: | :---: |
| Lambing rate | anak lahir ÷ induk dikawinkan | 159% | ❌ Tidak ada |
| Fertility rate | induk beranak ÷ induk dikawinkan | 92% | ❌ Tidak ada |
| Prolificacy | anak ÷ kelahiran | 1,44 | ❌ Tidak ada |
| Pre-weaning mortality | mati sebelum sapih ÷ lahir | 1,0% | ❌ Tidak ada |
| Lambing interval | rata-rata jarak antar kelahiran | 133 hari | ❌ Tidak ada |
| Kg anak disapih/induk | Σ bobot sapih ÷ induk | **tidak terukur** | ❌ Data tidak ada |
| Ewe efficiency | kg sapih ÷ bobot induk saat kawin | **tidak terukur** | ❌ Data tidak ada |

**Dua KPI terakhir tidak bisa dihitung** karena sistem tidak menandai *bobot sapih*
secara khusus (hanya `WeightLog` generik) dan tidak mencatat *bobot induk saat kawin*.

**Nilai komersialnya:** angka SFI sangat baik (mortalitas 1%, interval 133 hari).
Ini seharusnya menjadi materi pemasaran utama untuk menarik mitra baru — tapi saat ini
tidak muncul di mana pun dalam sistem.

---

### 🟡 GAP-F: Bagi hasil mitra tidak ada di sistem

**Severity: SEDANG-TINGGI**

Model bisnis SFI memakai bagi hasil 30% dari penjualan anakan. Dalam summary sistem,
saya tidak menemukan modul apa pun yang menghitung ini. `Invoice` mencatat penjualan,
`ExitLog` mencatat harga, tapi **pembagian hasil ke mitra dihitung di luar sistem**.

Padahal seluruh datanya sudah ada: `partner_id`, `sale_price`, `final_hpp`.

---

### 🟡 GAP-G: Tidak ada modul kesehatan preventif terjadwal

**Severity: SEDANG**

Sistem punya `TreatmentLog` dengan `next_due_date` dan notifikasi vaksin H-14. Bagus.
Tapi tidak ada **program kesehatan terjadwal** (protokol vaksinasi & obat cacing rutin
berdasarkan umur). Pada peternakan pembibitan, jadwal preventif adalah penentu
angka mortalitas — dan mortalitas SFI yang 1% itu perlu dijaga.

Juga tidak ada **withdrawal period** (masa henti obat sebelum ternak boleh dijual/dipotong).
Ini isu keamanan pangan yang bisa berdampak hukum.

---

### 🟡 GAP-H: Tidak ada mode offline untuk kepala kandang

**Severity: SEDANG**

Summary menyebut UI staf "mobile-first" — bagus. Tapi tidak disebut adanya kemampuan
offline. Di kandang, sinyal sering lemah. Bila input kelahiran gagal karena koneksi,
kepala kandang akan kembali mencatat di buku tulis — dan seluruh masalah rekonsiliasi
manual yang baru saja kita selesaikan akan terulang.

---

## 2.3 Ringkasan seluruh gap

| Kode | Gap | Severity | Sumber |
| :---: | :--- | :---: | :---: |
| A | HPP campur antar pemilik | 🔴 Kritis | **Temuan baru** |
| B | Alokasi pakan tanpa bobot | 🔴 Kritis | **Temuan baru** |
| C | `sire_id` kosong → inbreeding | 🔴 Kritis | **Temuan baru** |
| D | Biaya manual tanpa pro-rata | 🟠 Tinggi | **Temuan baru** |
| E | 7 KPI reproduksi tidak ada | 🟠 Tinggi | **Temuan baru** |
| F | Bagi hasil mitra manual | 🟡 Sedang-Tinggi | **Temuan baru** |
| G | Kesehatan preventif & withdrawal | 🟡 Sedang | **Temuan baru** |
| H | Tidak ada mode offline | 🟡 Sedang | **Temuan baru** |
| 1 | 9 kolom hilang di `animals` | 🟠 Tinggi | Gap Analysis |
| 2 | Importer tidak kompatibel | 🟠 Tinggi | Gap Analysis |
| 3 | Google Drive tanpa API | 🟢 Rendah | Gap Analysis |
| 4 | Modul konflik data | 🟡 Sedang | Gap Analysis |
| 5 | Kelahiran agregat | ⚪ Abaikan | Gap Analysis |
| 6 | Validasi warna eartag | 🟡 Sedang | Gap Analysis (dikoreksi) |

---

# BAGIAN 3 — REKOMENDASI BACKEND

## 3.1 [KRITIS] Perbaikan alokasi HPP lintas pemilik

### Masalah
```php
// KONDISI SEKARANG — di CalculateDailyHpp
$animalsInPen = Animal::where('current_location_id', $locationId)
                      ->where('is_active', true)
                      ->count();
$costPerHead = ($qtyUsed * $unitPrice) / $animalsInPen;
```

### Perbaikan
Biaya harus dialokasikan **hanya kepada ternak yang secara ekonomi menanggungnya**.
Untuk pakan yang dibeli SFI dan dikonsumsi bersama, pembagian tetap per kandang —
tapi **pencatatan penanggung biaya harus dipisah per pemilik** agar dapat diaudit.

```php
// REKOMENDASI
// 1. Kelompokkan ternak di kandang berdasarkan pemilik
$animalsInPen = Animal::where('current_location_id', $locationId)
    ->where('is_active', true)
    ->get()
    ->groupBy(fn($a) => $a->partner_id ?? 'SFI');

// 2. Hitung total unit alokasi (lihat 3.2 untuk bobot metabolis)
$totalUnits = $allAnimals->sum(fn($a) => $a->allocation_unit);

// 3. Alokasikan & CATAT penanggungnya
foreach ($allAnimals as $animal) {
    $share = ($qtyUsed * $unitPrice) * ($animal->allocation_unit / $totalUnits);

    $animal->increment('accumulated_feed_cost', $share);
    $animal->increment('current_hpp', $share);

    // BARU: jejak audit per alokasi
    HppAllocationLog::create([
        'animal_id'   => $animal->id,
        'partner_id'  => $animal->partner_id,   // ← kunci auditabilitas
        'cost_type'   => 'FEED',
        'source_id'   => $usageLog->id,
        'amount'      => $share,
        'basis'       => 'METABOLIC_WEIGHT',
        'allocated_at'=> $date,
    ]);
}
```

### Tabel baru: `hpp_allocation_logs`

```php
Schema::create('hpp_allocation_logs', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('animal_id')->constrained('animals');
    $table->foreignId('partner_id')->nullable()->constrained('master_partners');
    $table->enum('cost_type', ['FEED','MEDICINE','MANUAL','OVERHEAD']);
    $table->uuid('source_id')->nullable();      // usage_log / manual_cost id
    $table->decimal('amount', 12, 2);
    $table->enum('basis', ['HEADCOUNT','METABOLIC_WEIGHT','PRORATA_DAYS']);
    $table->decimal('allocation_unit', 8, 3)->nullable();
    $table->date('allocated_at');
    $table->timestamps();

    $table->index(['partner_id','allocated_at']);
    $table->index(['animal_id','cost_type']);
});
```

**Manfaat:** setiap rupiah HPP bisa ditelusuri — kapan, dari sumber apa, dengan dasar
perhitungan apa, dan ditanggung pemilik mana. Ini yang membuat SFI aman saat mitra
meminta audit.

---

## 3.2 [KRITIS] Alokasi pakan berbasis bobot metabolis

### Konsep
Kebutuhan energi ruminansia proporsional terhadap **BB^0,75** (metabolic body weight),
bukan terhadap jumlah ekor. Ini standar baku nutrisi ternak.

### Implementasi

```php
// app/Support/FeedAllocation.php
class FeedAllocation
{
    /**
     * Unit alokasi pakan = BB^0.75
     * Fallback berjenjang jika bobot tidak tersedia.
     */
    public static function unitFor(Animal $animal, Carbon $date): float
    {
        $weight = $animal->weightOn($date)          // WeightLog terdekat
            ?? $animal->birth_weight                 // kolom baru
            ?? self::estimateByAge($animal, $date);  // kurva pertumbuhan

        return pow(max($weight, 1.0), 0.75);
    }

    /** Estimasi bobot dari umur bila tak ada timbangan */
    private static function estimateByAge(Animal $animal, Carbon $date): float
    {
        $ageMonths = $animal->birth_date->diffInMonths($date);
        return match(true) {
            $ageMonths <= 1  => 5.0,
            $ageMonths <= 3  => 12.0,
            $ageMonths <= 6  => 20.0,
            $ageMonths <= 12 => 30.0,
            default          => 40.0,
        };
    }
}
```

### Dampak nyata

| Ternak | Bobot | Headcount (sekarang) | Bobot metabolis (usulan) |
| :--- | ---: | ---: | ---: |
| Cempe 1 bln | 3 kg | 1,00 unit | 2,28 unit |
| Cempe 6 bln | 20 kg | 1,00 unit | 9,46 unit |
| Bakalan 12 bln | 30 kg | 1,00 unit | 12,82 unit |

Dengan headcount, cempe 3 kg dibebani **sama** dengan bakalan 30 kg.
Dengan bobot metabolis, bakalan menanggung ±5,6× lipat cempe — mendekati kenyataan.

> **Catatan implementasi:** buat ini *configurable* di `FarmSetting`
> (`hpp_feed_allocation_basis` = `HEADCOUNT` | `METABOLIC_WEIGHT`) agar bisa
> di-rollback tanpa deploy ulang bila terjadi masalah.

---

## 3.3 [KRITIS] Auto-isi `sire_id` dari koloni kawin

### Konsep
Sistem sudah punya semua data yang dibutuhkan. Yang kurang hanya penghubungnya.

```php
// app/Services/BreedingService.php — method baru
/**
 * Cari pejantan dari koloni kawin aktif induk pada masa konsepsi.
 * Kebuntingan domba ±147-152 hari (pakai 150 ± toleransi 15 hari).
 */
public function inferSireFromColony(Animal $dam, Carbon $birthDate): ?Animal
{
    $gestation      = (int) FarmSetting::get('gestation_days', 150);
    $tolerance      = (int) FarmSetting::get('gestation_tolerance_days', 15);
    $conceptionFrom = $birthDate->copy()->subDays($gestation + $tolerance);
    $conceptionTo   = $birthDate->copy()->subDays($gestation - $tolerance);

    $membership = MatingColonyMember::where('animal_id', $dam->id)
        ->whereHas('colony', fn($q) => $q
            ->where('start_date', '<=', $conceptionTo)
            ->where(fn($q2) => $q2
                ->whereNull('end_date')
                ->orWhere('end_date', '>=', $conceptionFrom)))
        ->with('colony.sire')
        ->latest('created_at')
        ->first();

    return $membership?->colony?->sire;
}
```

### Integrasi di `BirthController`

```php
// Saat registrasi kelahiran
$sire = $this->breedingService->inferSireFromColony($dam, $birthDate);

$offspring->sire_id            = $sire?->id;
$offspring->sire_confidence    = $sire ? 'INFERRED_COLONY' : 'UNKNOWN';
// Jika kepala kandang memilih sire manual → 'CONFIRMED'
```

Tambahkan kolom `sire_confidence` (`CONFIRMED` | `INFERRED_COLONY` | `UNKNOWN`)
agar tingkat keyakinan silsilah terlacak — penting untuk perhitungan inbreeding
yang jujur.

### Modul anti-inbreeding

```php
// app/Services/InbreedingService.php
class InbreedingService
{
    /**
     * Koefisien kekerabatan (coefficient of relationship) berbasis silsilah.
     * Ambang aman: F < 6.25% (setara sepupu satu kali).
     */
    public function coefficientOfInbreeding(Animal $sire, Animal $dam, int $depth = 4): float
    {
        $sireAncestors = $this->ancestorPaths($sire, $depth);
        $damAncestors  = $this->ancestorPaths($dam, $depth);

        $common = array_intersect_key($sireAncestors, $damAncestors);

        $f = 0.0;
        foreach ($common as $id => $sireGen) {
            $damGen = $damAncestors[$id];
            // Rumus Wright: F = Σ (0.5)^(n1+n2+1)
            $f += pow(0.5, $sireGen + $damGen + 1);
        }
        return round($f * 100, 2); // dalam persen
    }

    public function checkMating(Animal $sire, Animal $dam): array
    {
        // Blok mutlak
        if ($sire->id === $dam->sire_id || $sire->id === $dam->dam_id)
            return ['allowed' => false, 'reason' => 'Tetua langsung (parent-offspring)'];

        if ($dam->sire_id && $sire->sire_id === $dam->sire_id)
            return ['allowed' => false, 'reason' => 'Saudara sebapak (half-sibling)'];

        $f = $this->coefficientOfInbreeding($sire, $dam);
        $threshold = (float) FarmSetting::get('max_inbreeding_pct', 6.25);

        return [
            'allowed'     => $f < $threshold,
            'coefficient' => $f,
            'level'       => match(true) {
                $f == 0    => 'AMAN',
                $f < 3.125 => 'RENDAH',
                $f < 6.25  => 'SEDANG',
                default    => 'TINGGI — DITOLAK',
            },
            'reason' => $f >= $threshold ? "Inbreeding {$f}% melebihi ambang {$threshold}%" : null,
        ];
    }
}
```

**Integrasikan sebagai validasi ke-5 di `BreedingService`** (setelah umur, bobot,
nifas, status). Saat kepala kandang menyusun koloni kawin, sistem langsung menolak
pasangan berisiko dan menyarankan alternatif.

---

## 3.4 [TINGGI] Pro-rata biaya manual

```php
// HppManualCostController — perbaikan
$periodStart = $cost->period_start;
$periodEnd   = $cost->period_end;
$periodDays  = $periodStart->diffInDays($periodEnd) + 1;

$animals = Animal::activeBetween($periodStart, $periodEnd)->get();

// Total hari-ternak dalam periode (bukan sekadar jumlah ekor)
$totalAnimalDays = $animals->sum(fn($a) =>
    $a->activeDaysWithin($periodStart, $periodEnd)
);

foreach ($animals as $animal) {
    $days  = $animal->activeDaysWithin($periodStart, $periodEnd);
    $share = $cost->amount * ($days / $totalAnimalDays);
    // ... alokasikan + catat di hpp_allocation_logs
}
```

Cempe yang lahir tanggal 25 hanya menanggung 6/31 bagian biaya bulan itu — adil dan akurat.

---

## 3.5 [TINGGI] Migrasi skema: 9 kolom dari Gap Analysis + 6 tambahan

Gap analysis sudah benar. Saya tambahkan 6 kolom yang diperlukan untuk perbaikan di atas:

```php
Schema::table('animals', function (Blueprint $table) {
    // === Dari Gap Analysis (9) ===
    $table->text('physical_description')->nullable();      // CIRI FISIK
    $table->tinyInteger('litter_size')->default(1);        // KEMBAR
    $table->decimal('birth_weight', 5, 2)->nullable();     // BB LAHIR
    $table->boolean('is_birth_weight_estimated')->default(false);
    $table->enum('confidence_level', ['RENDAH','SEDANG','TINGGI'])->default('TINGGI');
    $table->string('data_source')->nullable();             // SUMBER
    $table->text('notes')->nullable();                     // CATATAN
    $table->boolean('is_in_partner_records')->default(false);
    $table->unsignedTinyInteger('breeding_cycles_count')->default(0);

    // === Tambahan saya (6) — untuk perbaikan kritis ===
    $table->enum('sire_confidence', ['CONFIRMED','INFERRED_COLONY','UNKNOWN'])
          ->default('UNKNOWN');                            // GAP-C
    $table->decimal('weaning_weight', 5, 2)->nullable();   // GAP-E: KPI kg-sapih
    $table->date('weaning_date')->nullable();              // GAP-E
    $table->decimal('weight_at_mating', 5, 2)->nullable(); // GAP-E: ewe efficiency
    $table->string('legacy_tag_number')->nullable();       // NOMOR / NOMOR AKTIF
    $table->date('inactive_date')->nullable();             // pro-rata GAP-D

    $table->index(['partner_id','is_active']);
    $table->index(['dam_id','birth_date']);
    $table->index('sire_id');
});
```

> **Catatan atas `breeding_cycles_count`:** gap analysis menyarankan ini sebagai kolom.
> Saya setuju **dengan syarat** ia diperbarui otomatis via observer saat kelahiran dicatat —
> bukan diisi manual. Nilai turunan yang diisi manual pasti akan menjadi tidak sinkron.

---

## 3.6 [TINGGI] Importer multi-sheet

Gap analysis benar. Tambahan penting: **urutan impor wajib 2 tahap** karena `dam_id`
adalah foreign key ke `animals` itu sendiri.

```php
class MasterTernakImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'INDUKAN' => new IndukanSheetImport(),  // TAHAP 1: induk dulu
            'ANAKAN'  => new AnakanSheetImport(),   // TAHAP 2: baru anak
        ];
    }
}

class AnakanSheetImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int { return 5; }  // header di baris 5

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $dam = Animal::where('tag_id', $row['ind_tag_final'])->first();

            if (!$dam) {
                ImportIssue::create([
                    'sheet' => 'ANAKAN',
                    'tag'   => $row['tag_final'],
                    'issue' => "Induk '{$row['ind_tag_final']}' tidak ditemukan",
                ]);
                continue;   // JANGAN buat record yatim
            }

            $animal = Animal::updateOrCreate(
                ['tag_id' => $row['tag_final']],
                [
                    'dam_id'                    => $dam->id,
                    'birth_date'                => $this->parseDate($row['tgl_lahir']),
                    'gender'                    => strtoupper($row['jenis_kelamin']),
                    'birth_weight'              => $this->parseDecimal($row['bb_lahir']),
                    'is_birth_weight_estimated' => $row['bb_asumsi'] === 'YA',
                    'litter_size'               => (int) ($row['kembar'] ?? 1),
                    'confidence_level'          => strtoupper($row['keyakinan'] ?? 'TINGGI'),
                    'notes'                     => $row['catatan'],
                    'necklace_color'            => $row['kalung'],
                    'legacy_tag_number'         => $row['nomor_aktif'],
                    // ...
                ]
            );

            // Bobot lahir juga masuk WeightLog agar ADG punya titik awal
            WeightLog::firstOrCreate([
                'animal_id' => $animal->id,
                'weigh_date'=> $animal->birth_date,
            ], ['weight_kg' => $animal->birth_weight]);

            // Riwayat eartag
            if ($row['tag_lama'] && $row['tag_lama'] !== $row['tag_final']) {
                AnimalEarTagLog::firstOrCreate([
                    'animal_id'  => $animal->id,
                    'old_tag_id' => $row['tag_lama'],
                    'new_tag_id' => $row['tag_final'],
                ], ['changed_at' => $animal->birth_date, 'reason' => 'Migrasi data']);
            }
        }
    }
}
```

**Wajib ada:** *dry-run mode* yang melaporkan apa yang akan terjadi tanpa menulis ke database.
Migrasi 166 ekor tanpa uji coba adalah risiko yang tidak perlu diambil.

---

## 3.7 [SEDANG-TINGGI] Modul bagi hasil mitra

```php
Schema::create('partner_settlements', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignId('partner_id')->constrained('master_partners');
    $table->date('period_start');
    $table->date('period_end');
    $table->decimal('gross_sales', 14, 2);        // Σ harga jual ternak mitra
    $table->decimal('total_hpp', 14, 2);          // Σ HPP (dari hpp_allocation_logs)
    $table->decimal('net_profit', 14, 2);
    $table->decimal('partner_share_pct', 5, 2);   // default 30
    $table->decimal('partner_amount', 14, 2);
    $table->decimal('sfi_amount', 14, 2);
    $table->enum('status', ['DRAFT','APPROVED','PAID'])->default('DRAFT');
    $table->timestamp('approved_at')->nullable();
    $table->foreignId('approved_by')->nullable()->constrained('users');
    $table->timestamps();
});
```

Alur: generate draft otomatis per periode → Owner meninjau & menyetujui → terbit
laporan PDF untuk mitra. **Angka HPP diambil dari `hpp_allocation_logs`** sehingga
dapat diaudit sampai ke tiap transaksi pakan.

---

## 3.8 [SEDANG] Modul KPI reproduksi

```php
// app/Services/FlockKpiService.php
class FlockKpiService
{
    public function calculate(Carbon $from, Carbon $to, ?int $partnerId = null): array
    {
        $ewesExposed = $this->ewesExposedToRam($from, $to, $partnerId);
        $ewesLambed  = $this->ewesThatLambed($from, $to, $partnerId);
        $lambsBorn   = $this->lambsBorn($from, $to, $partnerId);
        $lambsWeaned = $this->lambsWeaned($from, $to, $partnerId);
        $kgWeaned    = $this->totalWeaningWeight($from, $to, $partnerId);

        return [
            'lambing_rate'          => $this->pct($lambsBorn, $ewesExposed),
            'fertility_rate'        => $this->pct($ewesLambed, $ewesExposed),
            'prolificacy'           => $this->ratio($lambsBorn, $ewesLambed),
            'weaning_rate'          => $this->pct($lambsWeaned, $ewesExposed),
            'pre_weaning_mortality' => $this->pct($lambsBorn - $lambsWeaned, $lambsBorn),
            'avg_lambing_interval'  => $this->avgLambingInterval($partnerId),
            'kg_weaned_per_ewe'     => $this->ratio($kgWeaned, $ewesExposed),
            'ewe_efficiency'        => $this->eweEfficiency($from, $to, $partnerId),
        ];
    }
}
```

Simpan hasil bulanan ke tabel `flock_kpi_snapshots` agar tren historis terjaga
dan dashboard tidak menghitung ulang tiap kali dibuka.

---

## 3.9 [SEDANG] Penyelarasan aturan warna eartag

**Ini koreksi atas Gap 6.** Sistem sudah punya mapping, tapi nilainya **berbeda**
dengan aturan SFI terbaru:

| Generasi | Sistem sekarang | Aturan SFI (dikonfirmasi) | Tindakan |
| :--- | :--- | :--- | :---: |
| F1 | Kuning | Kuning | ✅ Cocok |
| F2 | Orange | Orange | ✅ Cocok |
| F3 | **Kuning Orange** | **Biru** | ⚠️ **Perbaiki** |
| F4 | Orange Persegi | — | Perlu konfirmasi |
| F5 | Hijau Persegi | — | Perlu konfirmasi |
| Lokal/Komposit | Hijau (default) | Hijau | ✅ Cocok |

Perbaikan: jadikan tabel referensi sebagai **master data** (`master_eartag_rules`),
bukan key-value string, lalu tambahkan validasi saat input:
"Generasi F3 seharusnya eartag Biru, Anda memilih Orange — lanjutkan?"


---

# BAGIAN 4 — REKOMENDASI FRONTEND

## 4.1 Matriks RBAC lengkap

Summary menyebut 4 role tapi tidak merinci izin per modul. Berikut usulan matriks eksplisit:

| Modul | Owner (PEMILIK) | Breeder (PETERNAK) | Staff (STAF) | Partner (MITRA) | Publik |
| :--- | :---: | :---: | :---: | :---: | :---: |
| Data ternak | CRUD | CRU | R (miliknya di kandang) | R (miliknya) | — |
| Registrasi lahir | CRUD | CRU | **C** ← usulan | — | — |
| Timbang bobot | CRUD | CRU | CR | R | — |
| Kesehatan | CRUD | CRU | CR | R | — |
| Koloni kawin | CRUD | CRU | R | — | — |
| Pakan & inventory | CRUD | CRU | **CR (pemakaian)** | — | — |
| HPP & biaya | CRUD | R | — | R (agregat) | — |
| Invoice & jual | CRUD | CU | — | R | — |
| Bagi hasil | **CRUD + Approve** | R | — | R | — |
| Laporan KPI | R | R | — | R (miliknya) | — |
| Master data | CRUD | R | — | — | — |
| Pengguna & mitra | CRUD | — | — | — | — |
| CMS & artikel | CRUD | — | — | — | R |
| Katalog | CRUD | CU | — | — | R |

**Usulan perubahan penting:** Staf saat ini tidak bisa registrasi kelahiran
(hanya Breeder). Padahal **kepala kandang yang menyaksikan kelahiran**. Bila ia harus
menunggu Breeder, catatan akan tertunda dan kembali ke buku tulis. Beri Staf hak
*Create* untuk kelahiran, dengan status `PENDING_REVIEW` yang harus dikonfirmasi Breeder.

## 4.2 Dashboard per role

### Owner — fokus: keputusan strategis
```
┌─ Populasi ───────┬─ Keuangan Bulan Ini ─┬─ Perlu Tindakan ──┐
│ 165 ekor hidup   │ HPP    Rp  xx jt     │ 🔴 5 vaksin due   │
│ ↑ 12 bln terakhir│ Jual   Rp  xx jt     │ 🟠 3 stok menipis │
│                  │ Margin     xx %      │ 🟡 2 konflik data │
├──────────────────┴──────────────────────┴───────────────────┤
│ KPI REPRODUKSI          SFI     Target   Status             │
│ Lambing rate           159%    150-175%    ✅               │
│ Mortalitas pra-sapih    1.0%      <10%     ✅               │
│ Lambing interval      133 hr     243 hr    ✅               │
│ Prolificacy            1.44    1.6-1.8     ⚠️               │
├─────────────────────────────────────────────────────────────┤
│ [Grafik] Tren kelahiran 12 bulan · Komposisi generasi       │
└─────────────────────────────────────────────────────────────┘
```

### Kepala Kandang (Staf) — fokus: kerja hari ini
```
┌───────────────────────────────────────┐
│  📷  SCAN QR TERNAK                   │  ← tombol besar, aksi utama
├───────────────────────────────────────┤
│  TUGAS HARI INI              7 tugas  │
│  ☐ Vaksin B24, B25, B26               │
│  ☐ Timbang kandang Penggemukan 2      │
│  ☐ Sapih F12, F13                     │
├───────────────────────────────────────┤
│ [+ Kelahiran] [+ Timbang] [+ Obat]    │  ← 3 aksi tercepat
│ [+ Pakan]     [+ Pindah]  [+ Mati]    │
└───────────────────────────────────────┘
```

### Mitra — fokus: transparansi & kepercayaan
```
┌─ Ternak Saya ────────────────────────────────┐
│ 5 indukan · 17 anakan · 16 hidup             │
│ Nilai investasi Rp 27,5 jt                   │
├─ Kinerja vs Rata-rata Kelompok ──────────────┤
│ Anakan/indukan   3,4  vs  2,6  ✅ di atas    │
│ Mortalitas       5,9% vs  1,0% ⚠️ di bawah   │
├─ Bagi Hasil ─────────────────────────────────┤
│ Periode berjalan     Rp x.xxx.xxx            │
│ [Unduh laporan PDF]                          │
├─ Dokumentasi ────────────────────────────────┤
│ [Galeri foto/video ternak saya]              │
└──────────────────────────────────────────────┘
```

> **Prinsip untuk dashboard mitra:** tampilkan perbandingan terhadap rata-rata kelompok,
> **tanpa** menyebut nama mitra lain. Ini memberi konteks tanpa melanggar privasi.

## 4.3 Desain untuk pengguna non-teknis

Ini permintaan Anda yang paling sering diabaikan pengembang. Prinsip konkret:

**Kurangi ketikan, perbanyak pilihan.** Input kelahiran seharusnya: scan QR induk →
sistem tampilkan nama & foto induk → pilih jumlah anak (tombol 1/2/3) → per anak pilih
jenis kelamin (♂/♀) → isi bobot (numpad besar) → foto → simpan. **Tanpa mengetik satu
huruf pun** kecuali angka bobot.

**Bahasa lapangan, bukan bahasa sistem.** Gunakan "Domba Melahirkan" bukan
"Registrasi Birth Event". "Berapa ekor lahir?" bukan "Litter size".

**Konfirmasi visual, bukan teks.** Setelah simpan: tampilkan foto ternak + tanda centang
besar hijau + suara. Kepala kandang harus yakin datanya masuk tanpa membaca notifikasi kecil.

**Toleran terhadap kesalahan.** Semua input punya *undo* 15 menit. Kesalahan input di
kandang itu wajar; sistem yang menghukum kesalahan akan ditinggalkan.

**Target ukuran sentuh minimal 48×48 px**, kontras teks minimal 4.5:1 (WCAG AA),
karena dipakai di luar ruangan dengan tangan kotor dan layar terkena matahari.

## 4.4 Mode offline (GAP-H)

Minimal yang harus ada:
- **Service Worker** cache halaman input inti
- **IndexedDB** menyimpan antrian input saat offline
- **Indikator status** jelas: "📴 Offline — 3 data menunggu dikirim"
- **Sinkronisasi otomatis** saat koneksi pulih, dengan penanganan konflik

Tanpa ini, kepala kandang akan kembali ke buku tulis saat sinyal hilang — dan seluruh
kerja rekonsiliasi yang baru saja selesai akan terulang.

## 4.5 Katalog publik — peningkatan

Katalog sudah ada. Tambahan yang meningkatkan konversi:

- **Silsilah yang bisa dilihat** (2 generasi) — bukti pembibitan serius, bukan sekadar jual domba
- **Grafik pertumbuhan** dari `WeightLog` — bukti performa, bukan klaim
- **QR per ternak** yang bisa dipindai calon pembeli di lokasi → langsung ke halaman detail
- **Badge KPI peternakan**: "Tingkat kelangsungan hidup 99%" — angka SFI yang layak dipamerkan
- **Sertifikat digital** per ternak (PDF): silsilah, bobot, riwayat kesehatan, foto

---

# BAGIAN 5 — MIGRASI DATA

## 5.1 Prinsip

**Jangan pernah impor langsung ke produksi.** Urutan wajib:

```
1. Backup database produksi (mysqldump + verifikasi restore)
2. Impor ke staging → validasi → perbaiki → ulangi sampai bersih
3. Impor produksi di jam sepi, dengan maintenance mode
4. Validasi pasca-impor (checklist di 5.3)
5. Simpan rencana rollback selama 7 hari
```

## 5.2 Penanganan data asumsi

File `SFI_MASTER_TERNAK_v3.xlsx` mengandung data asumsi yang **harus tetap ditandai**
setelah migrasi — jangan sampai asumsi berubah menjadi "fakta" di database:

| Jenis data | Jumlah | Perlakuan saat impor |
| :--- | ---: | :--- |
| BB anakan asumsi | 12 | `is_birth_weight_estimated = true` |
| Tgl lahir indukan asumsi | 39 | `confidence_level = 'RENDAH'` + `notes` |
| Jenis kelamin asumsi | 3 | `confidence_level = 'RENDAH'` |
| Indukan belum bernomor | 6 | `tag_id = 'BELUM-ADA-NO-01'` + flag |
| Anakan belum bernomor | 5 | idem |
| Keyakinan rendah | 19 | `confidence_level = 'RENDAH'` |

## 5.3 Checklist validasi pasca-impor

```sql
-- 1. Jumlah harus persis
SELECT COUNT(*) FROM animals WHERE is_active = 1;           -- harus 165
SELECT COUNT(*) FROM animals WHERE gender = 'BETINA';        -- harus 110

-- 2. Tidak boleh ada anak yatim
SELECT COUNT(*) FROM animals
WHERE acquisition_type = 'HASIL_TERNAK' AND dam_id IS NULL;  -- harus 0

-- 3. Tidak boleh ada tag ganda
SELECT tag_id, COUNT(*) FROM animals GROUP BY tag_id HAVING COUNT(*) > 1;  -- kosong

-- 4. Nilai aset harus cocok dengan Excel
SELECT SUM(purchase_price) FROM animals WHERE is_active = 1; -- ±Rp 450,25 jt

-- 5. Distribusi kepemilikan
SELECT partner_id, COUNT(*) FROM animals WHERE is_active=1 GROUP BY partner_id;
-- SFI 98, FAHRI 18, VINA 22, LETA 11, AGENG 10, OKI 7

-- 6. Bobot lahir masuk ke weight_logs
SELECT COUNT(*) FROM weight_logs w
JOIN animals a ON a.id = w.animal_id AND w.weigh_date = a.birth_date;  -- ±102
```

## 5.4 Rencana rollback

```bash
# Sebelum impor
mysqldump -u user -p sfi_db > backup_pre_import_$(date +%Y%m%d_%H%M).sql

# Bila gagal
php artisan down
mysql -u user -p sfi_db < backup_pre_import_XXXX.sql
php artisan up
```

Tandai seluruh record hasil impor dengan `data_source = 'MIGRASI_EXCEL_2026-07'`
sehingga bisa dihapus selektif tanpa mengembalikan seluruh database.

---

# BAGIAN 6 — ROADMAP BERTAHAP

Prinsip urutan: **perbaiki yang sedang merusak data → lengkapi yang menghambat migrasi →
tambah yang menaikkan nilai bisnis → optimasi.**

## FASE 0 — Perbaikan Darurat (Minggu 1–2)
> *Tujuan: hentikan kerusakan data yang sedang berlangsung*

| # | Pekerjaan | Effort | Gap |
| :---: | :--- | :---: | :---: |
| 0.1 | Tabel `hpp_allocation_logs` + jejak audit alokasi | 2 hari | A |
| 0.2 | Filter `partner_id` di seluruh alokasi HPP | 1 hari | A |
| 0.3 | Alokasi pakan berbasis bobot metabolis (configurable) | 3 hari | B |
| 0.4 | Pro-rata biaya manual terhadap hari aktif | 1 hari | D |
| 0.5 | Selaraskan aturan warna eartag (F3 = Biru) | 0,5 hari | 6 |
| | **Total** | **~8 hari** | |

**Definisi selesai:** HPP setiap ternak dapat ditelusuri sampai ke transaksi sumbernya,
terpisah per pemilik, dan proporsional terhadap bobot.

## FASE 1 — Fondasi Data (Minggu 3–4)
> *Tujuan: 166 ekor masuk sistem tanpa kehilangan informasi*

| # | Pekerjaan | Effort | Gap |
| :---: | :--- | :---: | :---: |
| 1.1 | Migrasi 15 kolom baru di `animals` | 1 hari | 1 |
| 1.2 | Importer multi-sheet + dry-run mode | 4 hari | 2 |
| 1.3 | Impor ke staging + validasi + perbaikan | 2 hari | — |
| 1.4 | Impor produksi + validasi pasca-impor | 1 hari | — |
| | **Total** | **~8 hari** | |

## FASE 2 — Silsilah & Anti-Inbreeding (Minggu 5–6)
> *Tujuan: cegah inses sebelum 46 calon indukan mencapai usia kawin*

| # | Pekerjaan | Effort | Gap |
| :---: | :--- | :---: | :---: |
| 2.1 | `inferSireFromColony()` + kolom `sire_confidence` | 2 hari | C |
| 2.2 | `InbreedingService` (koefisien Wright) | 3 hari | C |
| 2.3 | Validasi ke-5 di `BreedingService` + UI peringatan | 2 hari | C |
| 2.4 | Rekomendasi pasangan kawin (daftar aman + skor) | 3 hari | C |
| | **Total** | **~10 hari** | |

**Kenapa fase ini tidak boleh ditunda:** 46 anakan betina akan mencapai usia kawin
dalam 8–12 bulan. Membangun modul ini setelah mereka kawin berarti terlambat —
kerusakan genetiknya permanen.

## FASE 3 — Nilai Bisnis (Minggu 7–9)
> *Tujuan: sistem menjadi alat jual, bukan sekadar alat catat*

| # | Pekerjaan | Effort | Gap |
| :---: | :--- | :---: | :---: |
| 3.1 | `FlockKpiService` + snapshot bulanan | 4 hari | E |
| 3.2 | Dashboard KPI Owner + Mitra | 3 hari | E |
| 3.3 | Modul bagi hasil `partner_settlements` | 4 hari | F |
| 3.4 | Laporan PDF mitra | 2 hari | F |
| 3.5 | Pencatatan bobot sapih & bobot saat kawin | 2 hari | E |
| | **Total** | **~15 hari** | |

## FASE 4 — Kualitas Operasional (Minggu 10–12)
> *Tujuan: jaga mortalitas 1% dan pastikan sistem dipakai di lapangan*

| # | Pekerjaan | Effort | Gap |
| :---: | :--- | :---: | :---: |
| 4.1 | Mode offline (Service Worker + IndexedDB) | 5 hari | H |
| 4.2 | Program kesehatan preventif terjadwal | 3 hari | G |
| 4.3 | Withdrawal period + blokir jual | 2 hari | G |
| 4.4 | Modul konflik data (`data_conflicts`) | 3 hari | 4 |
| 4.5 | Hak Staf registrasi lahir (`PENDING_REVIEW`) | 2 hari | — |
| | **Total** | **~15 hari** | |

## FASE 5 — Penyempurnaan (Bulan 4+)
> *Kerjakan hanya setelah Fase 0–4 stabil dan dipakai rutin*

- Google Drive API otomatis (Gap 3) — Apps Script manual sudah memadai untuk sekarang
- Sertifikat digital + QR publik per ternak
- Formulasi ransum & perhitungan FCR
- Integrasi timbangan digital / RFID
- Aplikasi mobile native

## ❌ Yang saya sarankan JANGAN dikerjakan sekarang

| Item | Alasan |
| :--- | :--- |
| Tabel `KELAHIRAN AWAL` (Gap 5) | 25 baris statis. Simpan sebagai JSON di `farm_settings` atau lampiran dokumen |
| Computer vision / face recognition | Populasi 165 ekor — biaya & kompleksitas jauh melebihi manfaat |
| Sensor IoT kandang | Belum ada masalah yang dipecahkannya. Tunggu skala >500 ekor |
| Formulasi ransum otomatis | Butuh data komposisi nutrisi pakan yang belum dikumpulkan |
| Aplikasi mobile native | PWA + mode offline sudah cukup dan jauh lebih murah dirawat |

---

# BAGIAN 7 — RISIKO & MITIGASI

| Risiko | Kemungkinan | Dampak | Mitigasi |
| :--- | :---: | :---: | :--- |
| Perubahan rumus HPP mengubah angka historis | Tinggi | Tinggi | Buat *configurable*; hitung ulang hanya periode berjalan; simpan angka lama sebagai `hpp_legacy` |
| Mitra mempertanyakan HPP yang berubah | Sedang | **Tinggi** | Komunikasikan **sebelum** deploy: "kami memperbaiki metode agar lebih adil"; siapkan laporan perbandingan |
| Impor gagal / data rusak | Sedang | Tinggi | Dry-run wajib, staging dulu, backup + rencana rollback |
| Kepala kandang tidak memakai sistem | **Tinggi** | **Tinggi** | Libatkan dalam desain; uji coba 1 minggu; sediakan mode offline; dampingi 2 minggu pertama |
| `sire_id` hasil inferensi keliru | Sedang | Sedang | Tandai `INFERRED_COLONY`; izinkan koreksi manual; jangan pakai untuk keputusan final tanpa konfirmasi |
| Pengembang tunggal / bus factor | Sedang | Tinggi | Dokumentasi kode, test otomatis untuk logika HPP & inbreeding |

## Peringatan khusus soal perubahan HPP

Memperbaiki rumus HPP **akan mengubah angka yang sudah dilihat mitra**. Ini masalah
komunikasi, bukan teknis. Rekomendasi saya:

1. Hitung dampaknya lebih dulu di staging — berapa selisih HPP per mitra
2. Siapkan laporan "sebelum vs sesudah" beserta penjelasan metodenya
3. Sampaikan ke mitra **sebelum** angkanya berubah di dashboard mereka
4. Bila selisihnya merugikan mitra, pertimbangkan menerapkan hanya untuk periode ke depan

---

# BAGIAN 8 — METRIK KEBERHASILAN

| Kategori | Metrik | Baseline | Target 3 bln |
| :--- | :--- | :---: | :---: |
| **Adopsi** | Input kelahiran via sistem (bukan buku) | 0% | >90% |
| | Kepala kandang aktif harian | — | ≥5 hari/minggu |
| | Mitra membuka dashboard | — | ≥1×/minggu |
| **Akurasi** | Ternak dengan `sire_id` terisi | 0% | >80% |
| | Ternak `confidence_level = TINGGI` | ~55% | >85% |
| | Selisih stok fisik vs sistem | — | <2% |
| **Efisiensi** | Waktu input 1 kelahiran | ±5 mnt (tulis+entri) | <90 detik |
| | Waktu tutup laporan bulanan | ±2 hari | <2 jam |
| **Bisnis** | Sengketa HPP dengan mitra | — | 0 |
| | Mitra baru bergabung | 5 | ≥7 |
| | Perkawinan inses tercegah | tidak terukur | 100% terdeteksi |

---

# PENUTUP: URUTAN YANG SAYA SARANKAN

Bila Anda hanya sempat mengerjakan **satu hal minggu ini**, kerjakan **Fase 0.1 + 0.2**
(pemisahan HPP per pemilik). Alasannya sederhana: itu satu-satunya masalah yang
memiliki **konsekuensi hukum dan reputasi**, dan setiap hari yang berlalu menambah
jumlah data yang harus dikoreksi nanti.

Bila sempat **satu bulan**, kerjakan Fase 0 + 1 + 2. Setelah itu sistem sudah:
akurat secara finansial, lengkap datanya, dan aman secara genetik.

Fase 3 ke atas adalah membangun nilai jual — penting, tapi tidak mendesak.

---

*Dokumen ini disusun berdasarkan Project Summary sistem, Gap Analysis, dan verifikasi
langsung terhadap 166 record ternak nyata pada `SFI_MASTER_TERNAK_v3.xlsx`.
Seluruh angka KPI dihitung dari data aktual, bukan estimasi.*
