# MASTER PROMPT — PERBAIKAN SISTEM SFI
## Instruksi Lengkap untuk AI Pembangun Sistem (Hostinger + Laravel 12)

**Sistem:** www.sahabatfarmindonesia.com
**Stack:** Laravel 12 · PHP 8.2/8.3 · MySQL · Blade + Tailwind v4 + Alpine.js · Vite 6
**Status:** SUDAH BERJALAN PRODUKSI — ini pekerjaan perbaikan, bukan membangun ulang
**Hosting:** Hostinger

---

# ⚠️ BACA INI DULU — URUTAN TIDAK BOLEH DIUBAH

Pemilik sistem **tidak bisa mengekspor data ternak yang sudah diinput**. Akibatnya:

- Data existing di sistem **tidak bisa dikurasi** dan digabung dengan data Excel
- Reset sistem sekarang = **kehilangan seluruh data historis**
- Migrasi data baru **tidak bisa dilakukan** tanpa risiko duplikasi

**Karena itu: EXPORT adalah pekerjaan NOMOR SATU.** Jangan kerjakan apa pun sebelum
export berfungsi dan pemilik berhasil mengunduh seluruh datanya.

**Urutan wajib:**
```
TAHAP 1  Export lengkap  →  pemilik unduh & kurasi data
TAHAP 2  Perbaikan aturan & parameter (generasi, kategori umur, HPP)
TAHAP 3  Pengaturan frontend (role, tampilan, semua parameter)
TAHAP 4  Modul baru (penjualan, pakan, laporan)
TAHAP 5  Reset + import data bersih
TAHAP 6  Fitur lanjutan
```

**Aturan pengerjaan:**
1. JANGAN merombak arsitektur — ikuti pola existing (Service/Observer/Action)
2. JANGAN hardcode parameter apa pun — semua harus dapat diatur dari frontend
3. Setiap perubahan wajib punya migration + rollback teruji
4. Bahasa antarmuka: **Indonesia**. Nama tabel/variabel: **Inggris**
5. Uji dengan data nyata, bukan dummy
6. Setiap fitur wajib menghormati RBAC

---

# TAHAP 1 — EXPORT & BACKUP DATA
## 🔴 KERJAKAN PERTAMA — pemblokir semua pekerjaan lain

## 1.1 Export Data Ternak Lengkap

### Kebutuhan
Pemilik harus bisa mengunduh **seluruh data ternak beserta historisnya** ke Excel,
untuk dicocokkan dengan data yang dikerjakan terpisah.

### Spesifikasi

```php
// app/Exports/AnimalMasterExport.php
class AnimalMasterExport implements WithMultipleSheets
{
    public function __construct(private array $filters = []) {}

    public function sheets(): array
    {
        return [
            'INDUKAN'         => new IndukanSheet($this->filters),
            'ANAKAN'          => new AnakanSheet($this->filters),
            'RIWAYAT BOBOT'   => new WeightHistorySheet($this->filters),
            'RIWAYAT KESEHATAN'=> new TreatmentHistorySheet($this->filters),
            'RIWAYAT EARTAG'  => new EarTagHistorySheet(),
            'RIWAYAT PEMILIK' => new OwnershipHistorySheet(),
            'RIWAYAT HPP'     => new HppHistorySheet($this->filters),
            'KOLONI KAWIN'    => new MatingColonySheet($this->filters),
            'KELAHIRAN'       => new BirthEventSheet($this->filters),
            'PENJUALAN'       => new SalesHistorySheet($this->filters),
            'KONFLIK DATA'    => new DataConflictSheet(),
            'REKAP'           => new SummarySheet($this->filters),
        ];
    }
}
```

### Kolom WAJIB di sheet ANAKAN

```
tag_id, legacy_tag_number, old_tag_id,
dam_tag_id, sire_tag_id, sire_confidence,
gender, breed_name, generation, generation_confidence, ear_tag_color,
birth_date, birth_weight, is_birth_weight_estimated, litter_size,
current_weight, adg, weaning_weight, weaning_date,
physical_status, is_active, necklace_color,
location_name, partner_name,
current_hpp, purchase_price, sale_price,
gdrive_folder_url, photo_url, video_url,        ← WAJIB (temuan poin 1)
confidence_level, data_source, notes, needs_review,
created_at, updated_at, created_by, last_modified_by
```

> **PENTING:** kolom `gdrive_folder_url` **belum ada di template import maupun export**,
> padahal sistem punya field input link Google Drive. Ini harus ditambahkan di
> **kedua sisi** (import dan export) agar dokumentasi foto/video tidak hilang.

### Aturan format (agar tidak rusak saat dibuka Excel)
- Tanggal: `YYYY-MM-DD` sebagai **teks**, bukan serial date
- Desimal: titik (`3.45`), **bukan** koma
- Nomor eartag: **paksa sebagai teks** agar `036` tidak berubah jadi `36`
- Kolom kosong: biarkan kosong, jangan isi `NULL` atau `0`

### Endpoint

```
GET  /admin/export/animals?format=xlsx&partner_id=&status=&from=&to=&location_id=
GET  /admin/export/animals/template          → template kosong siap isi
GET  /admin/export/full-backup               → seluruh tabel (JSON/SQL)
POST /admin/import/reconcile                 → upload hasil edit, tampilkan diff
```

### 1.2 Rekonsiliasi dua arah (WAJIB)

Saat pemilik mengunggah kembali file yang sudah diedit, sistem **TIDAK BOLEH langsung
menimpa**. Alur yang benar:

```
Upload file → Sistem bandingkan dengan data existing
            → Tampilkan tabel DIFF:
              [ ] tag_id  | field      | nilai lama | nilai baru | aksi
              [✓] B31     | birth_date | 2025-11-24 | 2025-11-23 | [Terima][Tolak]
            → Pemilik centang mana yang diterima
            → Klik "Terapkan Perubahan Terpilih"
            → Semua perubahan tercatat di audit log
```

### 1.3 Export Semua Laporan (poin 10)

Setiap laporan wajib punya panel filter seragam:

```
┌─ FILTER LAPORAN ──────────────────────────────────┐
│ Periode  : [Harian|Mingguan|Bulanan|Tahunan|Custom]│
│ Dari     : [__/__/____]   Sampai : [__/__/____]    │
│ Mitra    : [Semua ▼]                               │
│ Kandang  : [Semua ▼]                               │
│ Status   : [Semua ▼]                               │
│ Kolom    : [☑ Tag] [☑ Bobot] [☐ HPP] [☑ Status]... │
│                                    [Terapkan Filter]│
├────────────────────────────────────────────────────┤
│ Export: [📄 PDF] [📊 Excel] [📽 PPT] [🖼 PNG] [📋 CSV]│
└────────────────────────────────────────────────────┘
```

**Library yang disarankan (kompatibel Hostinger shared hosting):**
- PDF: `barryvdh/laravel-dompdf` (ringan, tanpa binary eksternal)
- Excel: `maatwebsite/excel`
- PPT: `phpoffice/phppresentation`
- Image: render HTML → canvas via `html2canvas` di sisi klien

> **Catatan Hostinger:** hindari library yang butuh `wkhtmltopdf`, Puppeteer, atau
> Chrome headless — umumnya tidak tersedia di shared hosting. DomPDF aman.

---

# TAHAP 2 — ATURAN & PARAMETER INTI

## 2.1 Aturan Generasi Berbasis Pejantan (poin 4) 🔴

### Aturan yang benar

| Pejantan | Indukan | Hasil Anakan |
| :--- | :--- | :--- |
| **Fullblood Dorper** | Lokal/Garut/Teksel/Merino | **F1 DORPER** |
| **Fullblood Dorper** | F1 | **F2 DORPER** |
| **Fullblood Dorper** | F2 | **F3 DORPER** |
| **Fullblood Dorper** | F3 | **F4 DORPER** |
| **Fullblood Dorper** | F4 | **F5 DORPER** |
| **Fullblood Dorper** | F5 | **F6 DORPER** |
| **Fullblood Dorper** | Fullblood | **FULLBLOOD DORPER** |
| **BUKAN Fullblood** | apapun | **CROSS DORPER** |

### Implementasi

```php
Schema::create('master_generation_rules', function (Blueprint $table) {
    $table->id();
    $table->enum('sire_type', ['FULLBLOOD','NON_FULLBLOOD']);
    $table->string('dam_generation', 30);      // LOKAL|GARUT|F1|F2|...|PURE|*
    $table->string('result_generation', 30);
    $table->string('result_breed_name', 60);
    $table->string('result_eartag_color', 40);
    $table->unsignedSmallInteger('priority')->default(100);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

```php
// app/Services/GenerationResolverService.php
public function resolve(?Animal $sire, Animal $dam): array
{
    if (!$sire) {
        return ['generation'=>null, 'needs_review'=>true,
                'reason'=>'Pejantan belum diketahui — generasi tidak dapat ditentukan'];
    }
    $sireType = $this->isFullblood($sire) ? 'FULLBLOOD' : 'NON_FULLBLOOD';
    $damGen   = $this->normalizeGeneration($dam);

    $rule = MasterGenerationRule::active()
        ->where('sire_type', $sireType)
        ->where(fn($q)=>$q->where('dam_generation',$damGen)->orWhere('dam_generation','*'))
        ->orderByRaw("CASE WHEN dam_generation='*' THEN 999 ELSE priority END")
        ->first();

    return [
        'generation'   => $rule?->result_generation,
        'breed_name'   => $rule?->result_breed_name,
        'eartag_color' => $rule?->result_eartag_color,
        'needs_review' => $sire->sire_confidence !== 'CONFIRMED',
    ];
}
```

### ⚠️ PERINGATAN untuk data existing

102 anakan sudah punya generasi **tanpa data pejantan** (`sire_id` kosong 100%).
Verifikasi: bila semua pejantan fullblood → generasi kebetulan benar; bila ada yang
non-fullblood → **seluruhnya harus jadi CROSS DORPER**.

**JANGAN hitung ulang massal sebelum `sire_id` terisi.** Tandai
`generation_confidence = 'UNVERIFIED'` dan sediakan halaman verifikasi silsilah.

## 2.2 Kategori Umur Configurable (poin 6) 🔴

### Aturan baru pemilik

| Umur | Betina | Jantan |
| :--- | :--- | :--- |
| 1–3 bulan | Cempe | Cempe |
| 3–5 bulan | Cempe Sapih | Cempe Sapih |
| 5–8 bulan | **Dara** | **Bakalan** |
| > 8 bulan | **Betina Indukan** | **Jantan** |

> **DAMPAK TERUKUR:** dengan aturan ini, **63 dari 102 ekor (62%) berubah kategori**
> dibanding aturan lama. Ini bukan perubahan kecil — komunikasikan ke mitra sebelum
> diterapkan, dan simpan kategori lama di kolom `legacy_physical_status`.

```php
Schema::create('master_age_categories', function (Blueprint $table) {
    $table->id();
    $table->string('code', 30)->unique();
    $table->string('name_male', 50);        // Cempe, Bakalan, Jantan
    $table->string('name_female', 50);      // Cempe, Dara, Betina Indukan
    $table->decimal('age_from_months', 5, 2);
    $table->decimal('age_to_months', 5, 2)->nullable();  // NULL = tak terbatas
    $table->boolean('is_breedable')->default(false);
    $table->boolean('is_sellable')->default(true);
    $table->string('badge_color', 20)->nullable();
    $table->unsignedSmallInteger('sort_order');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**UI Pengaturan:** tabel yang bisa diedit langsung (inline edit), dengan validasi
rentang umur tidak boleh tumpang tindih atau ada celah.

## 2.3 Perbaikan HPP (poin 8) 🔴

### Masalah saat ini
```
Feed Cost/Head = (Qty × Price) / Jumlah ternak di kandang     ← tanpa filter pemilik
Manual Cost/Head = Amount / Total ternak aktif                ← tanpa pro-rata
```

Akibatnya biaya ternak SFI dibebankan ke ternak mitra → **risiko sengketa**.

### Yang harus dibangun

**A. Item konsumsi configurable (poin 8)**

```php
Schema::create('master_consumable_types', function (Blueprint $table) {
    $table->id();
    $table->string('code', 30)->unique();      // PAKAN, VITAMIN, OBAT, SUPLEMEN
    $table->string('name', 80);
    $table->string('unit', 20);                // kg, liter, sachet
    $table->boolean('affects_hpp')->default(true);
    $table->enum('allocation_method', ['EQUAL','METABOLIC_WEIGHT','BY_LOCATION','MANUAL'])
          ->default('EQUAL');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

Pemilik bisa menambah jenis item baru (mis. "Mineral Blok") dari frontend tanpa developer.

**B. Cutoff HPP bulanan + histori**

```php
Schema::create('hpp_monthly_snapshots', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('animal_id');
    $table->unsignedBigInteger('partner_id')->nullable();
    $table->year('period_year');
    $table->unsignedTinyInteger('period_month');
    $table->decimal('opening_hpp', 12, 2);
    $table->decimal('feed_cost', 12, 2)->default(0);
    $table->decimal('medicine_cost', 12, 2)->default(0);
    $table->decimal('other_cost', 12, 2)->default(0);
    $table->decimal('overhead_cost', 12, 2)->default(0);
    $table->decimal('closing_hpp', 12, 2);
    $table->unsignedSmallInteger('active_days')->default(30);
    $table->timestamp('closed_at')->nullable();
    $table->timestamps();
    $table->unique(['animal_id','period_year','period_month'], 'hpp_snap_uniq');
});
```

**C. Alokasi berbasis bobot + pemisahan pemilik**

```php
// Unit alokasi = BB^0.75 (metabolic body weight — standar nutrisi ruminansia)
$unit = pow(max($animal->currentWeight(), 1.0), 0.75);

// Kelompokkan per pemilik agar dapat diaudit
$share = $totalCost * ($unit / $totalUnitsInGroup);

HppAllocationLog::create([
    'animal_id'  => $animal->id,
    'partner_id' => $animal->partner_id,   // ← kunci auditabilitas
    'cost_type'  => 'FEED',
    'amount'     => $share,
    'basis'      => 'METABOLIC_WEIGHT',
    'allocation_unit' => $unit,
    'total_units'     => $totalUnitsInGroup,
    'allocated_at'    => $date,
]);
```

**Dampak nyata:** cempe 3 kg → 2,28 unit; bakalan 30 kg → 12,82 unit.
Sebelumnya keduanya dihitung 1,00 unit (sama besar) — jelas keliru.

## 2.4 Notifikasi Ternak Tanpa Nomor (poin 5) 🟠

### Kebutuhan
Ternak tanpa eartag harus punya ID sementara + peringatan aktif. Pelacakan
memakai **nomor anakan tetangga** karena setiap kelahiran dilaporkan di grup WA
beserta foto.

```php
Schema::create('pending_tag_assignments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('animal_id');
    $table->string('temp_tag', 40);              // BELUM-ADA-NO-01
    $table->date('birth_date');
    $table->string('dam_tag', 40)->nullable();
    $table->string('prev_sibling_tag', 40)->nullable();  // ← petunjuk pelacakan
    $table->string('next_sibling_tag', 40)->nullable();  // ← petunjuk pelacakan
    $table->text('tracking_hint')->nullable();
    $table->enum('status', ['PENDING','ASSIGNED','SKIPPED'])->default('PENDING');
    $table->string('assigned_tag', 40)->nullable();
    $table->timestamp('assigned_at')->nullable();
    $table->timestamps();
});
```

**Logika petunjuk pelacakan** (dibangkitkan otomatis):
```php
$hint = "Lahir {$birthDate->format('d M Y')} dari indukan {$damTag}. "
      . "Nomor anakan sebelumnya: {$prevTag}, sesudahnya: {$nextTag}. "
      . "Cek laporan grup WA tanggal tersebut beserta fotonya.";
```

**Tampilan:**
- Badge merah di dashboard: "⚠️ 11 ternak belum bernomor — klik untuk melengkapi"
- Halaman khusus daftar pending dengan petunjuk pelacakan per ekor
- Notifikasi berulang tiap 7 hari sampai selesai
- Tombol "Tetapkan Nomor" yang langsung memperbarui `tag_id` + catat di `ear_tag_logs`

## 2.5 Auto-isi `sire_id` dari Koloni Kawin 🔴

```php
public function inferSireFromColony(Animal $dam, Carbon $birthDate): ?Animal
{
    $gestation = (int) FarmSetting::get('gestation_days', 150);
    $tol       = (int) FarmSetting::get('gestation_tolerance_days', 15);

    return MatingColonyMember::where('animal_id', $dam->id)
        ->whereHas('colony', fn($q) => $q
            ->where('start_date','<=',$birthDate->copy()->subDays($gestation-$tol))
            ->where(fn($x)=>$x->whereNull('end_date')
                              ->orWhere('end_date','>=',$birthDate->copy()->subDays($gestation+$tol))))
        ->with('colony.sire')->latest()->first()?->colony?->sire;
}
```

> **Perbaikan bug:** scheduler saat ini memakai **60 hari** untuk masa kebuntingan.
> Kebuntingan domba adalah **147–152 hari**. Ini bug yang harus diperbaiki.

---

# TAHAP 3 — SEMUA PARAMETER KE FRONTEND (poin 2, 3, 9)

## 3.1 Hasil inventarisasi: 45 parameter masih di backend

| Kategori | Jumlah | Kritis |
| :--- | ---: | ---: |
| Aturan Pemuliaan | 8 | 2 |
| Kategori Umur Ternak | 5 | 4 |
| Keuangan & HPP | 7 | 2 |
| Penjualan | 5 | 0 |
| User & Role | 3 | 0 |
| Tampilan Web | 6 | 0 |
| Export & Laporan | 7 | 2 |
| Audit & Notifikasi | 4 | 0 |
| **TOTAL** | **45** | **10** |

## 3.2 Struktur Menu Pengaturan (baru)

```
⚙️ PENGATURAN
├── 🧬 Aturan Pemuliaan
│   ├── Aturan Generasi Anakan       ← master_generation_rules
│   ├── Warna Eartag per Generasi    ← master_eartag_rules (F3 = Biru)
│   ├── Syarat Perkawinan            ← umur/bobot min, masa nifas
│   ├── Lama Kebuntingan             ← 150 ± 15 hari (perbaiki dari 60)
│   └── Ambang Inbreeding            ← default 6,25%
│
├── 🐑 Kategori Ternak
│   ├── Kategori Umur                ← master_age_categories (poin 6)
│   ├── Status Fisik
│   └── Ras / Breed
│
├── 💰 Keuangan
│   ├── Basis Alokasi Pakan          ← HEADCOUNT | METABOLIC_WEIGHT
│   ├── Pemisahan HPP per Pemilik    ← on/off
│   ├── Jenis Item Konsumsi          ← master_consumable_types (poin 8)
│   ├── Tanggal Cutoff Bulanan       ← default tanggal 1
│   └── Persentase Bagi Hasil        ← default 30%
│
├── 👥 Pengguna & Hak Akses
│   ├── Daftar Pengguna
│   ├── Role (bisa tambah baru)      ← RBAC dinamis
│   └── Matriks Izin (Role × Modul)  ← checkbox CRUD
│
├── 🎨 Tampilan Web
│   ├── Logo & Identitas
│   ├── Gambar Hero / Banner
│   ├── Judul & Teks Halaman
│   ├── Bahasa Antarmuka
│   ├── Warna Tema
│   └── Pengaturan Katalog
│
├── 📍 Master Data
│   ├── Lokasi Kandang
│   ├── Mitra
│   ├── Kategori Inventory
│   └── Supplier
│
└── 📜 Audit & Histori
    ├── Log Perubahan Data
    ├── Log Perubahan Pengaturan
    └── Dashboard Aktivitas
```

## 3.3 RBAC Dinamis

Ganti enum 4 role hardcoded dengan:

```php
Schema::create('roles', function (Blueprint $t) {
    $t->id(); $t->string('name',50)->unique(); $t->string('display_name',80);
    $t->text('description')->nullable(); $t->boolean('is_system')->default(false);
    $t->timestamps();
});
Schema::create('permissions', function (Blueprint $t) {
    $t->id(); $t->string('module',60); $t->string('action',20);
    $t->enum('scope',['ALL','OWN','PARTNER'])->default('ALL');
    $t->string('display_name',100); $t->timestamps();
    $t->unique(['module','action','scope']);
});
Schema::create('role_permission', function (Blueprint $t) {
    $t->foreignId('role_id')->constrained()->cascadeOnDelete();
    $t->foreignId('permission_id')->constrained()->cascadeOnDelete();
    $t->primary(['role_id','permission_id']);
});
```

**Migrasi aman:** pertahankan `users.role` lama sebagai fallback, isi tabel baru
dari nilai enum existing, hapus enum setelah stabil minimal 2 minggu.

**Matriks izin default:**

| Modul | Owner | Kepala Kandang | Mitra | Publik |
| :--- | :---: | :---: | :---: | :---: |
| Ternak | CRUD | CRU | R (miliknya) | — |
| Kelahiran | CRUD | **C**+RU | — | — |
| Timbang | CRUD | CR | R | — |
| Kesehatan | CRUD | CR | R | — |
| Koloni Kawin | CRUD | R | — | — |
| Pakan/Inventory | CRUD | CR | — | — |
| HPP | CRUD | — | R (agregat) | — |
| Penjualan | CRUD | — | R | — |
| Bagi Hasil | CRUD+Approve | — | R | — |
| Laporan | R+Export | R | R (miliknya) | — |
| Pengaturan | CRUD | — | — | — |
| Katalog | CRUD | CU | — | R |

> **Usulan:** beri Kepala Kandang hak **Create** untuk kelahiran (status
> `PENDING_REVIEW`). Dialah yang menyaksikan kelahiran; jika harus menunggu Owner,
> data akan tertunda dan kembali ke buku tulis.

## 3.4 Pengaturan Tampilan Web

```php
Schema::create('site_settings', function (Blueprint $t) {
    $t->id(); $t->string('key',80)->unique(); $t->text('value')->nullable();
    $t->enum('type',['TEXT','TEXTAREA','IMAGE','COLOR','BOOLEAN','NUMBER','SELECT','JSON']);
    $t->string('group',40);      // BRANDING|HERO|CATALOG|CONTACT|LANGUAGE
    $t->string('label',120); $t->text('help_text')->nullable();
    $t->json('options')->nullable();
    $t->unsignedSmallInteger('sort_order')->default(0);
    $t->timestamps();
});
```

Untuk `type=IMAGE`: uploader dengan preview, auto-resize, validasi dimensi minimum,
simpan di `storage/app/public/site/`.

## 3.5 Audit Trail Menyeluruh

Saat ini hanya eartag & ownership yang punya log. Perubahan bobot, status, harga,
kepemilikan **tidak terlacak**.

```php
composer require owen-it/laravel-auditing

class Animal extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;
    protected $auditInclude = ['tag_id','gender','breed_id','generation','birth_date',
        'birth_weight','partner_id','current_location_id','phys_status_id',
        'current_hpp','is_active','sire_id','dam_id'];
}
```

**Dashboard Histori:** waktu · pengguna · entitas · field · nilai lama → baru,
dengan filter dan pencarian.

---

# TAHAP 4 — MODUL BARU

## 4.1 Modul Penjualan Lengkap (poin 7) 🟠

### Alur yang diminta

```
[1] PILIH TERNAK
    Filter: kandang, kategori umur, mitra, rentang bobot, status
    Search: nomor eartag (autocomplete)
    Pilih: checkbox satu-satu / "Pilih Semua" / "Pilih Halaman Ini"
    ↓
[2] PRO FORMA INVOICE
    Harga per ekor: [Pakai HPP] [Pakai HPP + margin %] [Input manual]
    Preview lengkap: daftar ternak, harga, subtotal, diskon, PPN, total
    ↓
[3] KIRIM KE CUSTOMER
    Generate PDF → unduh / kirim WhatsApp / email
    Status: DRAFT → SENT
    ↓
[4] PEMBAYARAN
    [Bayar DP]   → status DP_PAID, catat jumlah & tanggal
    [Pelunasan]  → status PAID, terbit INVOICE resmi
    [Batalkan]   → status CANCELLED, ternak KEMBALI ke stok farm
    ↓
[5] LAPORAN PENJUALAN
    Filter periode, status, customer, mitra
    Export PDF/Excel
```

### Skema

```php
Schema::create('proforma_invoices', function (Blueprint $t) {
    $t->uuid('id')->primary();
    $t->string('proforma_number',40)->unique();   // PI-2026-07-001
    $t->unsignedBigInteger('customer_id')->nullable();
    $t->string('customer_name',150);
    $t->string('customer_phone',30)->nullable();
    $t->text('customer_address')->nullable();
    $t->date('issue_date'); $t->date('valid_until')->nullable();
    $t->enum('price_source',['HPP','HPP_MARGIN','MANUAL'])->default('MANUAL');
    $t->decimal('margin_pct',5,2)->nullable();
    $t->decimal('subtotal',14,2)->default(0);
    $t->decimal('discount',14,2)->default(0);
    $t->decimal('tax',14,2)->default(0);
    $t->decimal('total',14,2)->default(0);
    $t->decimal('dp_amount',14,2)->default(0);
    $t->date('dp_date')->nullable();
    $t->decimal('paid_amount',14,2)->default(0);
    $t->enum('status',['DRAFT','SENT','DP_PAID','PAID','CANCELLED','EXPIRED'])
      ->default('DRAFT');
    $t->uuid('invoice_id')->nullable();     // terisi saat lunas
    $t->text('notes')->nullable();
    $t->text('cancel_reason')->nullable();
    $t->timestamps();
});

Schema::create('proforma_invoice_items', function (Blueprint $t) {
    $t->uuid('id')->primary();
    $t->uuid('proforma_invoice_id');
    $t->uuid('animal_id');
    $t->string('tag_id_snapshot',40);       // simpan nomor saat itu
    $t->decimal('weight_snapshot',6,2)->nullable();
    $t->decimal('hpp_snapshot',12,2)->nullable();
    $t->decimal('unit_price',12,2);
    $t->decimal('line_total',12,2);
    $t->timestamps();
    $t->unique(['proforma_invoice_id','animal_id']);
});
```

### Aturan bisnis wajib

- Ternak yang masuk pro forma **dikunci** (`is_reserved = true`) agar tidak dijual ganda
- Saat **DP dibayar** → ternak tetap terkunci
- Saat **lunas** → terbit `Invoice`, buat `ExitLog`, `is_active = false`
- Saat **dibatalkan** → `is_reserved = false`, ternak kembali ke stok
- Pro forma kedaluwarsa otomatis setelah `valid_until` → status `EXPIRED`, kunci dilepas
- **Blokir** ternak yang masih dalam masa henti obat (withdrawal period)

## 4.2 Modul Pakan & Vitamin (poin 8) 🟠

```php
Schema::create('consumable_purchases', function (Blueprint $t) {
    $t->uuid('id')->primary();
    $t->foreignId('consumable_type_id')->constrained('master_consumable_types');
    $t->string('item_name',120);
    $t->decimal('quantity',12,3);
    $t->string('unit',20);
    $t->decimal('unit_price',12,2);
    $t->decimal('total_price',14,2);
    $t->date('purchase_date');
    $t->unsignedBigInteger('supplier_id')->nullable();
    $t->decimal('remaining_qty',12,3);          // untuk weighted average
    $t->timestamps();
});

Schema::create('consumable_usages', function (Blueprint $t) {
    $t->uuid('id')->primary();
    $t->foreignId('consumable_type_id')->constrained('master_consumable_types');
    $t->unsignedBigInteger('location_id')->nullable();  // NULL = seluruh farm
    $t->decimal('quantity',12,3);
    $t->decimal('unit_cost',12,2);              // weighted average saat itu
    $t->decimal('total_cost',14,2);
    $t->date('usage_date');
    $t->enum('allocation_method',['EQUAL','METABOLIC_WEIGHT','BY_LOCATION']);
    $t->boolean('is_allocated')->default(false);
    $t->timestamp('allocated_at')->nullable();
    $t->timestamps();
});
```

**Job cutoff bulanan:**
```php
// app/Console/Commands/CloseMonthlyHpp.php
// Jadwal: tanggal 1 pukul 01:00
// 1. Kumpulkan seluruh consumable_usages bulan lalu yang belum dialokasikan
// 2. Alokasikan sesuai allocation_method + pisahkan per partner_id
// 3. Tulis hpp_allocation_logs (audit) + hpp_monthly_snapshots (histori)
// 4. Perbarui animals.current_hpp
// 5. Kunci periode agar tidak dihitung ganda
```

## 4.3 Laporan dengan Filter Lengkap (poin 10) 🟠

Laporan yang harus ada:

| Laporan | Filter | Format Export |
| :--- | :--- | :--- |
| Populasi Ternak | periode, mitra, kandang, kategori | PDF, Excel, PPT, PNG |
| Kelahiran | periode, mitra, indukan | PDF, Excel, PPT, PNG |
| Pertumbuhan (ADG) | periode, mitra, kategori | PDF, Excel, PPT, PNG |
| KPI Reproduksi | periode, mitra | PDF, Excel, PPT, PNG |
| HPP per Ternak | periode, mitra, kandang | PDF, Excel |
| Penjualan | periode, status, customer | PDF, Excel |
| Bagi Hasil Mitra | periode, mitra | PDF, Excel |
| Inventory & Pakan | periode, jenis item | PDF, Excel |
| Kesehatan | periode, jenis tindakan | PDF, Excel |
| Histori Perubahan | periode, pengguna, modul | Excel, CSV |

**Template PDF wajib memuat:** logo SFI, judul, periode, filter yang dipakai,
tanggal cetak, nama pencetak, nomor halaman.

---

# TAHAP 5 — RESET & IMPORT DATA BERSIH

**Prasyarat mutlak:** TAHAP 1 selesai, pemilik sudah mengunduh & mengkurasi data.

```
1. Backup penuh database + file  (mysqldump + tar storage/)
2. Verifikasi backup bisa di-restore di staging
3. Pemilik unggah file Excel hasil kurasi
4. DRY RUN import → tampilkan laporan: akan dibuat X, diperbarui Y, error Z
5. Pemilik menyetujui hasil dry run
6. Maintenance mode ON
7. Truncate tabel transaksional (JANGAN master data & settings)
8. Import bertahap: INDUKAN → ANAKAN → riwayat
9. Validasi pasca-import (checklist SQL di bawah)
10. Maintenance mode OFF
11. Simpan backup 30 hari
```

### Checklist validasi pasca-import

```sql
SELECT COUNT(*) FROM animals WHERE is_active=1;                    -- harus 165
SELECT COUNT(*) FROM animals WHERE acquisition_type='HASIL_TERNAK'
  AND dam_id IS NULL;                                              -- harus 0
SELECT tag_id, COUNT(*) FROM animals GROUP BY tag_id HAVING COUNT(*)>1;  -- kosong
SELECT partner_id, COUNT(*) FROM animals WHERE is_active=1 GROUP BY 1;
-- SFI 98, VINA 22, FAHRI 18, LETA 11, AGENG 10, OKI 7
SELECT SUM(purchase_price) FROM animals WHERE is_active=1;         -- ±Rp 450,25 jt
SELECT COUNT(*) FROM pending_tag_assignments WHERE status='PENDING';    -- 11
```

---

# TAHAP 6 — REKOMENDASI TAMBAHAN (belum ada di analisa pemilik)

## 6.1 🔴 Validasi silsilah melingkar
Tanpa proteksi, salah input bisa membuat A induk B sekaligus B induk A →
perhitungan inbreeding infinite loop. Batasi traversal maksimal 10 generasi.

## 6.2 🟠 Withdrawal period obat
Ternak yang baru diobati tidak boleh dijual sebelum masa henti obat berakhir.
Isu keamanan pangan dengan konsekuensi hukum. Blokir di modul penjualan & katalog.

## 6.3 🟠 Mode offline kepala kandang
Sinyal kandang sering lemah. Bila input gagal → kembali ke buku tulis → seluruh
masalah rekonsiliasi terulang. Minimal: Service Worker + IndexedDB + indikator status.

## 6.4 🟠 Anti-inbreeding
46 anakan betina akan mencapai usia kawin dalam 8–12 bulan. Hitung koefisien Wright,
blokir kawin tetua-anak & saudara sekandung/sebapak, ambang 6,25%.

## 6.5 🟠 KPI reproduksi
7 KPI standar industri belum ada. Angka SFI sangat bagus dan layak dipamerkan:

| KPI | SFI | Benchmark |
| :--- | :---: | :---: |
| Lambing rate | **159%** | 150–175% |
| Fertility rate | **92%** | >90% |
| Pre-weaning mortality | **1,0%** | <10% |
| Lambing interval | **133 hari** | 243 hari |

## 6.6 🟡 Notifikasi WhatsApp
Email jarang dibuka peternak. WhatsApp dibaca. Integrasikan untuk: kelahiran baru,
vaksin jatuh tempo, stok menipis, ternak belum bernomor, laporan bagi hasil.

## 6.7 🟡 Bulk operations
Timbang 30 ekor satu per satu terlalu lambat. Mode batch: pilih kandang → daftar
muncul → isi bobot berurutan → simpan sekaligus.

## 6.8 🟡 Kalender operasional
Gabungkan perkiraan kelahiran (koloni kawin + 150 hari), jadwal vaksin, sapih, timbang.

## 6.9 🟡 Sertifikat digital per ternak
PDF: silsilah, riwayat bobot, kesehatan, foto, QR verifikasi. Nilai jual tinggi.

## 6.10 🟢 Backup otomatis terjadwal
Belum ada. Wajib: backup harian DB + file, retensi 30 hari, uji restore berkala.

---

# DEFINISI SELESAI

Setiap pekerjaan dianggap selesai bila:

- [ ] Mengikuti pola arsitektur existing
- [ ] Ada migration + rollback teruji
- [ ] **Parameter masuk halaman Pengaturan, bukan hardcode**
- [ ] Validasi di sisi server
- [ ] Tercatat di audit trail
- [ ] Teks Indonesia, mudah dipahami awam
- [ ] Diuji dengan data nyata 166 ekor
- [ ] Menghormati RBAC
- [ ] Ada dokumentasi singkat untuk Owner

---

# LARANGAN

❌ Reset/truncate data sebelum export berfungsi & pemilik berhasil mengunduh
❌ Hitung ulang generasi 102 anakan sebelum `sire_id` terisi
❌ Ubah rumus HPP di produksi tanpa memberi tahu mitra
❌ Hardcode parameter baru — semua configurable
❌ Hapus data permanen — pakai soft delete + audit
❌ Impor langsung ke produksi tanpa dry run
❌ Pakai library PDF yang butuh binary eksternal (tidak jalan di Hostinger shared)
❌ Bangun computer vision/IoT/app native — belum sepadan untuk 166 ekor

---

# CATATAN KHUSUS HOSTINGER

- **Shared hosting**: hindari `exec()`, Puppeteer, wkhtmltopdf → pakai DomPDF
- **Cron job**: Hostinger mendukung, set `php artisan schedule:run` tiap menit
- **Batas memori**: proses export besar harus pakai *chunking* (jangan load semua ke memori)
- **Queue**: gunakan driver `database`, bukan Redis (kecuali VPS)
- **Storage**: pastikan `php artisan storage:link` sudah dijalankan
- **Upload limit**: cek `upload_max_filesize` untuk impor Excel besar
