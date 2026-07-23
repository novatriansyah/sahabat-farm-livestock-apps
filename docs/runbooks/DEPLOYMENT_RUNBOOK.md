# Deployment Runbook — SFI Livestock Apps (Release 0 Closeout CP7)

## 1. Overview & Scope
Dokumen ini merupakan panduan resmi prosedur deployment non-produksi dan staging untuk Sahabat Farm Indonesia (SFI) Release 0 Checkpoint CP7 (REV1).

> [!IMPORTANT]
> Depresiasi dan cutover ke lingkungan PRODUKSI memerlukan token persetujuan eksplisit tersendiri (`APPROVE_PRODUCTION_CUTOVER`).

## 2. Pre-Deployment Check
Sebelum menjalankan deployment, pastikan seluruh gate berikut terpenuhi:
1. Package Validator menyatakan **STATUS: ACCEPTED / VERIFIED COMPLETE** tanpa pelanggaran Hard Acceptance Gates G1 - G18.
2. Seluruh unit test dan feature test (100+ tests) passing 100%.
3. File Master Excel SHA-256 terverifikasi: `18a45066ff25131b43541255774cad62d1c0ab5acdf9dfeae3502fef80f6fe79`.
4. File database snapshot / backup lengkap dan terverifikasi via `php artisan backup:verify <backup_id>`.

## 3. Step-by-Step Deployment Procedure

### Langkah 1: Streaming Backup Pre-Deployment
```bash
php artisan backup:database --compress
php artisan backup:media
```

### Langkah 2: Environment Check & Configuration
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Langkah 3: Migration Execution (Idempotent)
```bash
php artisan migrate --force
```

### Langkah 4: Master Derived Acceptance Data Seeding
```bash
php artisan db:seed --class=MasterDerivedAcceptanceSeeder --force
```

### Langkah 5: Verification & Governance Audit Check
```bash
php -d extension=zip check_counts.php
php -d extension=zip check_reconciliation.php
php artisan test
```

## 4. Post-Deployment Verification Checklist
- [x] Tepat 166 ternak aktif/terdaftar di database.
- [x] 71 Data Quality Issues terbuka pada `data_quality_issues`.
- [x] 46 Ear Tag Logs tercatat pada `animal_ear_tag_logs`.
- [x] Distibusi pemilik: SFI (98), VINA (22), FAHRI (18), LETA (11), AGENG (10), OKI (7).
- [x] Export Center berfungsi untuk 35 kolom lossless dan 5 partner report.
